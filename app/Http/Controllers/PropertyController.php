<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\GeocodingService;
use App\Traits\CreatesNotifications;
use App\Traits\LogsActivity;

class PropertyController extends Controller
{
    use CreatesNotifications, LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::where('status', 'approved');
        
        // Apply sorting (default to 'recommended' to match UI)
        $sort = $request->input('sort', 'recommended');
        $query = $this->applySorting($query, $sort);
        
        $properties = $query->paginate(12)->withQueryString();
        $amenities = Amenity::all();
        $rules = Rule::all();
        return view('search', compact('properties', 'amenities', 'rules', 'request'));
    }

    public function filterSearch(Request $request)
    {   
        $amenities = Amenity::all();
        $rules = Rule::all();
        $query = Property::where('status', 'approved');

        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function($q) use ($location) {
                $q->where('country', 'like', "%$location%")
                  ->orWhere('city', 'like', "%$location%")
                  ->orWhere('address', 'like', "%$location%");
            });
        }

        if ($request->filled('min-price')) {
            $query->where('price', '>=', $request->input('min-price'));
        }
        if ($request->filled('max-price')) {
            $query->where('price', '<=', $request->input('max-price'));
        }

        if ($request->has('property_type')) {
            $query->whereIn('property_type', (array)$request->input('property_type'));
        }

        if ($request->has('listing_type')) {
            $query->whereIn('listing_type', (array)$request->input('listing_type'));
        }

        if ($request->has('amenities')) {
            $amenitiesInput = (array)$request->input('amenities');
            $amenityIds = array_map(function($item) {
                if (is_array($item)) {
                    return $item['id'] ?? null;
                }
                if (is_string($item) && str_starts_with($item, '{')) {
                    $decoded = json_decode($item, true);
                    return $decoded['id'] ?? null;
                }
                return $item;
            }, $amenitiesInput);
            $amenityIds = array_filter($amenityIds);
            if (count($amenityIds) > 0) {
                $query->whereHas('amenities', function($q) use ($amenityIds) {
                    $q->whereIn('amenities.id', $amenityIds);
                }, '=', count($amenityIds));
            }
        }

        if ($request->has('rules')) {
            $rulesInput = (array)$request->input('rules');
            $ruleNames = array_map(function($item) {
                if (is_array($item)) {
                    return $item['name'] ?? $item['title'] ?? null;
                }
                if (is_string($item) && str_starts_with($item, '{')) {
                    $decoded = json_decode($item, true);
                    return $decoded['name'] ?? $decoded['title'] ?? null;
                }
                return $item;
            }, $rulesInput);
            $ruleNames = array_filter($ruleNames);
            if (count($ruleNames) > 0) {
                $query->whereHas('rules', function($q) use ($ruleNames) {
                    $q->whereIn('rules.name', $ruleNames);
                }, '=', count($ruleNames));
            }
        }

        // Apply sorting (default to 'recommended' to match UI)
        $sort = $request->input('sort', 'recommended');
        $query = $this->applySorting($query, $sort);

        $properties = $query->paginate(12)->withQueryString();
        return view('search', compact('properties', 'request', 'amenities', 'rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Only show approved properties, unless user is admin or property owner
        if ($property->status !== 'approved') {
            $user = Auth::user();
            if (!$user || ($user->role !== 'admin' && $user->id !== $property->user_id)) {
                abort(404, 'Property not found or pending approval');
            }
        }
        
        $property->load(['images', 'reviews.user']);
        
        $avgRating = $property->average_rating;
        $reviewsCount = $property->reviews_count;
        
        return view('show', compact('property', 'avgRating', 'reviewsCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || $property->user_id === Auth::id())) {
            $amenities = Amenity::all();
            $rules = Rule::all();
            $units = Unit::all();

            $property->loadMissing(['amenities', 'rules', 'images', 'unit']);

            return view('edit-property', compact('property', 'amenities', 'rules', 'units'));
        }

        return redirect()->route('properties.show', $property)
            ->with('error', 'You are not authorized to edit this property!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        if (!(Auth::check() && (Auth::user()->role === 'admin' || $property->user_id === Auth::id()))) {
            return redirect()->route('properties.show', $property)
                ->with('error', 'You are not authorized to edit this property!');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'property_type' => 'required|string',
            'listing_type'  => 'required|string',
            'country'       => 'nullable|string',
            'city'          => 'nullable|string',
            'address'       => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|integer|exists:units,id',
            'area_m3'       => 'required|numeric|min:0',
            'room_nb'       => 'required|integer|min:0',
            'bathroom_nb'   => 'required|integer|min:0',
            'bedroom_nb'    => 'required|integer|min:0',

            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',

            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            'amenities'     => 'nullable|array',
            'amenities.*'   => 'integer|exists:amenities,id',
            'rules'         => 'nullable|array',
            'rules.*'       => 'integer|exists:rules,id',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|exists:property_images,id',
        ]);

        DB::beginTransaction();

        try {
            $validated['unit_id'] = $validated['unit'];
            unset($validated['unit'], $validated['images'], $validated['remove_images']);

            if (isset($validated['latitude']) && isset($validated['longitude']) 
                && !empty($validated['latitude']) && !empty($validated['longitude'])) {
                $validated['latitude'] = (float) $validated['latitude'];
                $validated['longitude'] = (float) $validated['longitude'];
            } else {
                if ($this->shouldGeocode($property, $validated)) {
                    $coordinates = $this->geocodeAddress($validated);
                    if ($coordinates) {
                        $validated['latitude'] = $coordinates['latitude'];
                        $validated['longitude'] = $coordinates['longitude'];
                        if (isset($coordinates['country']) && !isset($validated['country'])) {
                            $validated['country'] = $coordinates['country'];
                        }
                        if (isset($coordinates['city']) && !isset($validated['city'])) {
                            $validated['city'] = $coordinates['city'];
                        }
                        if (isset($coordinates['address']) && !isset($validated['address'])) {
                            $validated['address'] = $coordinates['address'];
                        }
                    }
                }
            }

            $property->update($validated);

            $property->amenities()->sync((array)$request->input('amenities', []));
            $property->rules()->sync((array)$request->input('rules', []));

            // Log activity
            $this->logActivity(
                'property_updated',
                "Property '{$property->title}' was updated",
                $property,
                ['property_id' => $property->id, 'property_title' => $property->title]
            );

            $removeImageIds = collect($request->input('remove_images', []))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique();

            if ($removeImageIds->isNotEmpty()) {
                $imagesToDelete = $property->images()
                    ->whereIn('id', $removeImageIds)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $image->delete();
                }

                $property->load('images');

                if ($property->images()->where('is_primary', true)->doesntExist()) {
                    $nextImage = $property->images()->first();
                    if ($nextImage) {
                        $nextImage->setAsPrimary();
                    }
                }
            }

            if ($request->hasFile('images')) {
                $hasPrimary = $property->images()->where('is_primary', true)->exists();
                foreach ($request->file('images') as $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path'        => $path,
                        'is_primary'  => !$hasPrimary && !$property->images()->exists(),
                    ]);
                    $hasPrimary = $hasPrimary || true;
                }
            }

            DB::commit();

            return redirect()->route('properties.show', $property)
                ->with('success', 'Property updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('properties.show', $property)
                ->with('error', 'Failed to update property: '.$e->getMessage());
        }
    }


    public function destroy(Property $property)
    {
    
            if (Auth::check() && (Auth::user()->role === 'admin' || $property->user_id === Auth::id())) {
                $propertyTitle = $property->title;
                $propertyId = $property->id;
                
                foreach ($property->images as $image) {
                    Storage::disk('public')->delete($image->path);
                }

                $property->delete();

                // Log activity
                $this->logActivity(
                    'property_deleted',
                    "Property '{$propertyTitle}' was deleted",
                    null,
                    ['property_id' => $propertyId, 'property_title' => $propertyTitle]
                );

                return redirect()->route('search')->with('success', 'Property deleted successfully!');
            }

            return back()->with('error', 'You are not authorized to delete this property!');
       
    }

    public function submitListing(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isLandlord()) {
            return redirect()->route('home')
                ->with('error', 'Only landlords and admins can list properties. <a href="' . route('landlord.apply') . '">Become a Landlord</a>');
        }

        if (!$request->hasFile('images')) {
            Log::error('No images uploaded');
            return back()->withErrors(['images' => 'Please upload at least one image.'])->withInput();
        }

        $imageFiles = $request->file('images');
        if (empty($imageFiles) || count($imageFiles) < 1) {
            Log::error('Images array is empty');
            return back()->withErrors(['images' => 'Please upload at least one image.'])->withInput();
        }

        try {
            $validated = $request->validate([
                'title'         => 'required|string|max:255',
                'description'   => 'nullable|string',
                'property_type' => 'required|string',
                'listing_type'  => 'required|string',
                'country'       => 'nullable|string',
                'city'          => 'nullable|string',
                'address'       => 'nullable|string',
                'price'         => 'required|numeric|min:0',
                'unit'          => 'required|integer|exists:units,id',
                'area_m3'       => 'required|numeric|min:0',
                'room_nb'       => 'nullable|integer|min:0',
                'bathroom_nb'   => 'required|integer|min:0',
                'bedroom_nb'    => 'nullable|integer|min:0',
                'latitude'      => 'required|numeric|between:-90,90',
                'longitude'     => 'required|numeric|between:-180,180',
                'images'        => 'required|array|min:1',
                'images.*'      => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'amenities'     => 'nullable|array',
                'amenities.*'   => 'integer|exists:amenities,id',
                'rules'         => 'nullable|array',
                'rules.*'       => 'integer|exists:rules,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            Log::error('Request data:', $request->except(['images', 'password']));
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();

        try {
            $validated['user_id'] = Auth::id();
            $validated['unit_id'] = $validated['unit'] ?? null;
            $validated['status'] = 'pending'; // New properties require admin approval
            unset($validated['unit']);
            $amenityIds = (array)($request->input('amenities', []));
            $ruleIds    = (array)($request->input('rules', []));

            $latitude = (float) $validated['latitude'];
            $longitude = (float) $validated['longitude'];
            
            $geocodingService = app(GeocodingService::class);
            $reverseGeocodeResult = $geocodingService->reverseGeocode($latitude, $longitude);
            
            if ($reverseGeocodeResult) {
                $addressComponents = $reverseGeocodeResult['address_components'] ?? [];
                
                $validated['country'] = $this->extractAddressComponent($addressComponents, 'country') 
                    ?? $validated['country'] ?? 'Lebanon';
                $validated['city'] = $this->extractAddressComponent($addressComponents, 'locality')
                    ?? $this->extractAddressComponent($addressComponents, 'administrative_area_level_1')
                    ?? $validated['city'] ?? 'Unknown';
                $streetNumber = $this->extractAddressComponent($addressComponents, 'street_number');
                $route = $this->extractAddressComponent($addressComponents, 'route');
                $address = trim(($streetNumber ?? '') . ' ' . ($route ?? ''));
                $validated['address'] = $address ?: ($reverseGeocodeResult['formatted_address'] ?? $validated['address'] ?? 'Unknown');
            } else {
                $validated['country'] = $validated['country'] ?? 'Lebanon';
                $validated['city'] = $validated['city'] ?? 'Unknown';
                $validated['address'] = $validated['address'] ?? 'Unknown';
                Log::warning('Reverse geocoding failed for coordinates', [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
            }

            $validated['latitude'] = $latitude;
            $validated['longitude'] = $longitude;

            $property = Property::create($validated);

            if (!empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }
            if (!empty($ruleIds)) {
                $property->rules()->sync($ruleIds);
            }

            // Log activity
            $this->logActivity(
                'property_created',
                "New property '{$property->title}' was created",
                $property,
                ['property_id' => $property->id, 'property_title' => $property->title, 'status' => 'pending']
            );

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path'        => $path,
                        'is_primary'  => $index === 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('search')->with('success', 'Property listed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to create property', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['images', 'password'])
            ]);

            if (isset($property) && $property->exists) {
                foreach ($property->images as $img) {
                    Storage::disk('public')->delete($img->path);
                }
                $property->delete();
            }

            return back()
                ->withErrors(['error' => 'Failed to create property: ' . $e->getMessage()])
                ->withInput()
                ->with('error', 'Failed to create property. Please check the errors below.');
        }
    }


    /**
     * Toggle like status for a property
     */
    public function like(Property $property)
    {
        $user = Auth::user();
        
        if ($property->likedBy()->where('user_id', $user->id)->exists()) {
            $property->likedBy()->detach($user->id);
            $status = 'unliked';
        } else {
            $property->likedBy()->attach($user->id);
            $status = 'liked';
            
            if ($property->user_id !== $user->id) {
                $this->createNotification(
                    $property->user,
                    'like',
                    'New Like on Your Property',
                    $user->name . ' liked your property: ' . $property->title,
                    $property
                );
            }
        }
        
        return response()->json([
            'status' => $status,
            'count' => $property->likedBy()->count(),
        ]);
    }

    /**
     * Check if property address has changed and needs geocoding
     */
    private function shouldGeocode(Property $property, array $validated): bool
    {
        $locationFields = ['address', 'city', 'country'];
        
        foreach ($locationFields as $field) {
            if (isset($validated[$field]) && $property->$field !== $validated[$field]) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Geocode an address using the GeocodingService
     */
    private function geocodeAddress(array $data): ?array
    {
        $geocodingService = app(GeocodingService::class);
        
        $addressParts = array_filter([
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['country'] ?? ''
        ]);
        
        $address = implode(', ', $addressParts);
        
        if (empty($address)) {
            return null;
        }
        
        return $geocodingService->geocode($address);
    }

    private function extractAddressComponent(array $addressComponents, string $type): ?string
    {
        foreach ($addressComponents as $component) {
            if (in_array($type, $component['types'] ?? [])) {
                return $component['long_name'] ?? null;
            }
        }
        return null;
    }

       private function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'price-low':
                return $query->orderBy('price', 'asc');
            
            case 'price-high':
                return $query->orderBy('price', 'desc');
            
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            
            case 'latest':
                return $query->orderBy('created_at', 'asc');
            
            case 'recommended':
            default:
                // Sort by number of likes (most liked first)
                return $query->withCount('likedBy')
                    ->orderBy('liked_by_count', 'desc')
                    ->orderBy('created_at', 'desc'); 
        }
    }

    public function popularCities($limit = 10)
    {
        return Property::where('status', 'approved')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->selectRaw('city, COUNT(*) as property_count')
            ->groupBy('city')
            ->orderBy('property_count', 'desc')
            ->orderBy('city', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->city,
                    'count' => $item->property_count,
                    'url' => route('filter-search', ['location' => $item->city])
                ];
            });
    }
}


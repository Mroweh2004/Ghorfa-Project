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

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::orderBy('created_at', 'desc')->paginate(12);
        $amenities = Amenity::all();
        $rules = Rule::all();
        return view('search', compact('properties', 'amenities', 'rules'));
    }

    public function filterSearch(Request $request)
    {   
        $amenities = Amenity::all();
        $rules = Rule::all();
        $query = Property::query();

        // Location
        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function($q) use ($location) {
                $q->where('country', 'like', "%$location%")
                  ->orWhere('city', 'like', "%$location%")
                  ->orWhere('address', 'like', "%$location%");
            });
        }

        // Price Range
        if ($request->filled('min-price')) {
            $query->where('price', '>=', $request->input('min-price'));
        }
        if ($request->filled('max-price')) {
            $query->where('price', '<=', $request->input('max-price'));
        }

        // Property Type
        if ($request->has('property_type')) {
            $query->whereIn('property_type', (array)$request->input('property_type'));
        }

        // Listing Type
        if ($request->has('listing_type')) {
            $query->whereIn('listing_type', (array)$request->input('listing_type'));
        }

        // Amenities (expects amenities[] as array of amenity IDs)
        if ($request->has('amenities')) {
            $amenitiesInput = (array)$request->input('amenities');
            // Extract IDs if input is array of objects (JSON strings)
            $amenityIds = array_map(function($item) {
                if (is_array($item)) {
                    return $item['id'] ?? null;
                }
                // If item is a JSON string, decode it
                if (is_string($item) && str_starts_with($item, '{')) {
                    $decoded = json_decode($item, true);
                    return $decoded['id'] ?? null;
                }
                // Otherwise, assume it's an ID
                return $item;
            }, $amenitiesInput);
            $amenityIds = array_filter($amenityIds); // Remove nulls
            if (count($amenityIds) > 0) {
                $query->whereHas('amenities', function($q) use ($amenityIds) {
                    $q->whereIn('amenities.id', $amenityIds);
                }, '=', count($amenityIds));
            }
        }

        // Rules (expects rules[] as array of rule titles)
        if ($request->has('rules')) {
            $rulesInput = (array)$request->input('rules');
            // Extract titles if input is array of objects (JSON strings)
            $ruleTitles = array_map(function($item) {
                if (is_array($item)) {
                    return $item['title'] ?? null;
                }
                // If item is a JSON string, decode it
                if (is_string($item) && str_starts_with($item, '{')) {
                    $decoded = json_decode($item, true);
                    return $decoded['title'] ?? null;
                }
                // Otherwise, assume it's a title
                return $item;
            }, $rulesInput);
            $ruleTitles = array_filter($ruleTitles); // Remove nulls
            if (count($ruleTitles) > 0) {
                $query->whereHas('rules', function($q) use ($ruleTitles) {
                    $q->whereIn('rules.title', $ruleTitles);
                }, '=', count($ruleTitles)); // Only properties with ALL selected rules
            }
        }

        $properties = $query->paginate(12);
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
        $property->load(['images', 'reviews.user']);
        
        // Calculate review statistics
        $avgRating = $property->average_rating;
        $reviewsCount = $property->reviews_count;
        
        return view('show', compact('property', 'avgRating', 'reviewsCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        // Allow admin users to edit any property
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
            'country'       => 'required|string',
            'city'          => 'required|string',
            'address'       => 'required|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|integer|exists:units,id',
            'area_m3'       => 'required|numeric|min:0',
            'room_nb'       => 'required|integer|min:0',
            'bathroom_nb'   => 'required|integer|min:0',
            'bedroom_nb'    => 'required|integer|min:0',

            // optional images
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // pivots
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

            // Geocode address if location fields changed
            if ($this->shouldGeocode($property, $validated)) {
                $coordinates = $this->geocodeAddress($validated);
                if ($coordinates) {
                    $validated['latitude'] = $coordinates['latitude'];
                    $validated['longitude'] = $coordinates['longitude'];
                }
            }

            // update main fields
            $property->update($validated);

            // sync amenities / rules (empty array clears selections)
            $property->amenities()->sync((array)$request->input('amenities', []));
            $property->rules()->sync((array)$request->input('rules', []));

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

            // new images (append)
            if ($request->hasFile('images')) {
                $hasPrimary = $property->images()->where('is_primary', true)->exists();
                foreach ($request->file('images') as $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path'        => $path,
                        'is_primary'  => !$hasPrimary && !$property->images()->exists(), // if none exists, set first new as primary
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
                foreach ($property->images as $image) {
                    Storage::disk('public')->delete($image->path);
                }

                $property->delete();

                return redirect()->route('search')->with('success', 'Property deleted successfully!');
            }

            return back()->with('error', 'You are not authorized to delete this property!');
       
    }

    public function submitListing(Request $request)
    {
        // validate
        try {
            $validated = $request->validate([
                'title'         => 'required|string|max:255',
                'description'   => 'nullable|string',
                'property_type' => 'required|string',
                'listing_type'  => 'required|string',
                'country'       => 'required|string',
                'city'          => 'required|string',
                'address'       => 'required|string',
                'price'         => 'required|numeric|min:0',
                'unit'          => 'required|integer|exists:units,id',
                'area_m3'       => 'required|numeric|min:0',
                'room_nb'       => 'nullable|integer|min:0',
                'bathroom_nb'   => 'required|integer|min:0',
                'bedroom_nb'    => 'nullable|integer|min:0',

                // images
                'images'        => 'required|array|min:1',
                'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

                // pivots
                'amenities'     => 'nullable|array',
                'amenities.*'   => 'integer|exists:amenities,id',
                'rules'         => 'nullable|array',
                'rules.*'       => 'integer|exists:rules,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();

        try {
            $validated['user_id'] = Auth::id();
            $validated['unit_id'] = $validated['unit'] ?? null;
            unset($validated['unit']);
            $amenityIds = (array)($request->input('amenities', []));
            $ruleIds    = (array)($request->input('rules', []));

            // Geocode address for new property
            $coordinates = $this->geocodeAddress($validated);
            if ($coordinates) {
                $validated['latitude'] = $coordinates['latitude'];
                $validated['longitude'] = $coordinates['longitude'];
            }

            // create property
            $property = Property::create($validated);

            // sync pivots
            if (!empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }
            if (!empty($ruleIds)) {
                $property->rules()->sync($ruleIds);
            }

            // images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path'        => $path,
                        'is_primary'  => $index === 0, // first image primary
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('search')->with('success', 'Property listed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            // cleanup partially stored images (if any)
            if (isset($property) && $property->exists) {
                foreach ($property->images as $img) {
                    Storage::disk('public')->delete($img->path);
                }
                $property->delete();
            }

            return back()->with('error', 'Failed to create property: '.$e->getMessage())
                        ->withInput();
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
}


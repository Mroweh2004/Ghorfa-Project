<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::paginate(12);
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
        $property->load('images');
        return view('show', data: compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        // Allow admin users to edit any property
        if (auth()->user()->role === 'admin' || $property->user_id === auth()->id()) {
            return view('edit-property', compact('property'));
        }

        return redirect()->route('properties.show', $property)
            ->with('error', 'You are not authorized to edit this property!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        if (auth()->user()->role === 'admin' || $property->user_id === auth()->id()) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'property_type' => 'required|string',
                'price' => 'required|numeric|min:0',
                'area_m3' => 'required|numeric|min:0',
                'room_nb' => 'required|integer|min:0',
                'bathroom_nb' => 'required|integer|min:0',
                'bedroom_nb' => 'required|integer|min:0',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $property->update($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path' => $path,
                        'is_primary' => false
                    ]);
                }
            }

            return redirect()->route('properties.show', $property)
                ->with('success', 'Property updated successfully!');
        }

        return redirect()->route('properties.show', $property)
            ->with('error', 'You are not authorized to edit this property!');
    }

    public function destroy(Property $property)
    {
    
            if (auth()->user()->role === 'admin' || $property->user_id === auth()->id()) {
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
      
        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'property_type' => 'required|string',
            'listing_type' => 'required|string',
            'country' => 'string',
            'city' => 'required|string',
            'address' => 'required|string',
            'price' => 'required|numeric|min:0',
            'area_m3' => 'nullable|numeric|min:0',
            'room_nb' => 'nullable|integer|min:0',
            'bathroom_nb' => 'nullable|integer|min:0',
            'bedroom_nb' => 'nullable|integer|min:0',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
            $validated['user_id'] = auth()->id();
            $property = Property::create($validated);
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $index => $image) {
                    $path = $image->store('property-images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path' => $path,
                        'is_primary' => $index === 0
                    ]);
                }
            }
          

            return redirect()->route('search')->with('success', 'Property listed successfully!');
        
            if (isset($property)) {
                        foreach ($property->images as $image) {
                        Storage::disk('public')->delete($image->path);
                    }
                    $property->delete();             
            }
            
            return back()->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }


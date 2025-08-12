<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::paginate(12);
        return view('search', compact('properties'));
    }

    public function filterSearch(Request $request)
    {
        $query = $request->input('location');
        
        if (empty($query)) {      
            return redirect()->route('search');
        }

        $properties = Property::where(function($q) use ($query) {
            $q->where('country', 'like', '%' . $query . '%')
              ->orWhere('city', 'like', '%' . $query . '%')
              ->orWhere('address', 'like', '%' . $query . '%');
        })->paginate(12);
        
        return view('search', compact('properties', 'request'));
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
            'country' => 'required|string',
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
                
                foreach ($images as  $image) {
                       
                        $path = $image->store('property-images', 'public');
                        $propertyImage = PropertyImage::create([
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


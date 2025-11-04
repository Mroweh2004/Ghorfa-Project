<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Amenity;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['images', 'unit', 'amenities', 'reviews'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Apply filters if provided
        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function($q) use ($location) {
                $q->where('country', 'like', "%$location%")
                  ->orWhere('city', 'like', "%$location%")
                  ->orWhere('address', 'like', "%$location%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->has('property_type')) {
            $query->whereIn('property_type', (array)$request->input('property_type'));
        }

        if ($request->has('listing_type')) {
            $query->whereIn('listing_type', (array)$request->input('listing_type'));
        }

        $properties = $query->get();
        $amenities = Amenity::all();
        $rules = Rule::all();

        // Debug: Log properties count
        \Log::info('MapController: Found ' . $properties->count() . ' properties with coordinates');
        
        return view('map', compact('properties', 'amenities', 'rules', 'request'));
    }

    public function geocode(Request $request)
    {
        $address = $request->input('address');
        $apiKey = config('services.google.maps_server_key');

        if (!$address || !$apiKey) {
            return response()->json(['error' => 'Address or API key missing'], 400);
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return response()->json([
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address']
                ]);
            }

            return response()->json(['error' => 'Geocoding failed'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Geocoding service error'], 500);
        }
    }
}
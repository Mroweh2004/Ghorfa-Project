<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_server_key');
    }

    /**
     * Geocode an address to get latitude and longitude
     *
     * @param string $address
     * @return array|null
     */
    public function geocode(string $address): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $this->apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address']
                ];
            }

            Log::warning('Geocoding failed', [
                'address' => $address,
                'status' => $data['status'] ?? 'unknown'
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Geocoding service error', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Geocode multiple addresses in batch
     *
     * @param array $addresses
     * @return array
     */
    public function geocodeBatch(array $addresses): array
    {
        $results = [];
        
        foreach ($addresses as $address) {
            $results[$address] = $this->geocode($address);
        }

        return $results;
    }

    /**
     * Reverse geocode coordinates to get address
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $this->apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return [
                    'formatted_address' => $data['results'][0]['formatted_address'],
                    'address_components' => $data['results'][0]['address_components']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Reverse geocoding service error', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

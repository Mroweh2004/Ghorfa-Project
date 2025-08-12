<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser    = User::where('email', 'admin@example.com')->first();
        $regularUser  = User::where('email', 'user@example.com')->first();

        $beirutProperties = [
            [
                'title'        => 'Modern Apartment in Downtown Beirut',
                'description'  => 'Beautiful modern apartment in the heart of Beirut with sea view.',
                'property_type'=> 'Apartment',
                'listing_type' => 'Rent',
                'country'      => 'Lebanon',
                'city'         => 'Beirut',
                'address'      => 'Downtown Beirut',
                'price'        => 1500,
                'area_m3'      => 120,
                'room_nb'      => 3,
                'bathroom_nb'  => 2,
                'bedroom_nb'   => 2,
                'user_id'      => $adminUser?->id,
            ],
            [
                'title'        => 'Luxury Villa in Achrafieh',
                'description'  => 'Spacious villa with garden in one of Beirut\'s most prestigious neighborhoods.',
                'property_type'=> 'Villa',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Beirut',
                'address'      => 'Achrafieh',
                'price'        => 850000,
                'area_m3'      => 300,
                'room_nb'      => 6,
                'bathroom_nb'  => 4,
                'bedroom_nb'   => 4,
                'user_id'      => $regularUser?->id,
            ],
            [
                'title'        => 'Cozy Studio in Hamra',
                'description'  => 'Perfect studio apartment for students or young professionals.',
                'property_type'=> 'Apartment',
                'listing_type' => 'Rent',
                'country'      => 'Lebanon',
                'city'         => 'Beirut',
                'address'      => 'Hamra Street',
                'price'        => 800,
                'area_m3'      => 45,
                'room_nb'      => 1,
                'bathroom_nb'  => 1,
                'bedroom_nb'   => 1,
                'user_id'      => $regularUser?->id,
            ],
        ];

        $baalbekProperties = [
            [
                'title'        => 'Traditional House in Baalbek',
                'description'  => 'Authentic Lebanese house with traditional architecture.',
                'property_type'=> 'House',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Baalbek',
                'address'      => 'Old City',
                'price'        => 250000,
                'area_m3'      => 200,
                'room_nb'      => 5,
                'bathroom_nb'  => 3,
                'bedroom_nb'   => 3,
                'user_id'      => $regularUser?->id,
            ],
            [
                'title'        => 'Farm House in Baalbek',
                'description'  => 'Beautiful farm house with large garden and agricultural land.',
                'property_type'=> 'House',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Baalbek',
                'address'      => 'Ras Baalbek',
                'price'        => 350000,
                'area_m3'      => 400,
                'room_nb'      => 7,
                'bathroom_nb'  => 4,
                'bedroom_nb'   => 5,
                'user_id'      => $regularUser?->id,
            ],
            [
                'title'        => 'Modern Apartment in Baalbek',
                'description'  => 'Newly built apartment with modern amenities.',
                'property_type'=> 'Apartment',
                'listing_type' => 'Rent',
                'country'      => 'Lebanon',
                'city'         => 'Baalbek',
                'address'      => 'New City',
                'price'        => 500,
                'area_m3'      => 100,
                'room_nb'      => 3,
                'bathroom_nb'  => 2,
                'bedroom_nb'   => 2,
                'user_id'      => $regularUser?->id,
            ],
            [
                'title'        => 'Villa with Mountain View',
                'description'  => 'Luxurious villa with stunning mountain views.',
                'property_type'=> 'Villa',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Baalbek',
                'address'      => 'Mountain Area',
                'price'        => 450000,
                'area_m3'      => 350,
                'room_nb'      => 6,
                'bathroom_nb'  => 4,
                'bedroom_nb'   => 4,
                'user_id'      => $regularUser?->id,
            ],
        ];

        $tyreProperties = [
            [
                'title'        => 'Beachfront Apartment in Tyre',
                'description'  => 'Stunning apartment with direct access to the beach.',
                'property_type'=> 'Apartment',
                'listing_type' => 'Rent',
                'country'      => 'Lebanon',
                'city'         => 'Tyre',
                'address'      => 'Beach Road',
                'price'        => 1200,
                'area_m3'      => 150,
                'room_nb'      => 4,
                'bathroom_nb'  => 2,
                'bedroom_nb'   => 3,
                'user_id'      => $regularUser?->id ?? 2, // fallback ID if you want
            ],
            [
                'title'        => 'Traditional House in Old Tyre',
                'description'  => 'Charming house in the historic part of Tyre.',
                'property_type'=> 'House',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Tyre',
                'address'      => 'Old City',
                'price'        => 300000,
                'area_m3'      => 250,
                'room_nb'      => 5,
                'bathroom_nb'  => 3,
                'bedroom_nb'   => 3,
                'user_id'      => $adminUser?->id ?? 1, // fallback ID if you want
            ],
        ];

        $saidaProperty = [
            [
                'title'        => 'Luxury Apartment in Saida',
                'description'  => 'High-end apartment with sea view in Saida.',
                'property_type'=> 'Apartment',
                'listing_type' => 'Sale',
                'country'      => 'Lebanon',
                'city'         => 'Saida',
                'address'      => 'Seafront',
                'price'        => 280000,
                'area_m3'      => 180,
                'room_nb'      => 4,
                'bathroom_nb'  => 3,
                'bedroom_nb'   => 3,
                'user_id'      => $regularUser?->id ?? 2,
            ],
        ];

        $allProperties = array_merge(
            $beirutProperties,
            $baalbekProperties,
            $tyreProperties,
            $saidaProperty
        );

        foreach ($allProperties as $propertyData) {
            Property::create($propertyData);
        }
    }
}

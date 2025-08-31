<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
            'is_landlord' => true,
            'phone_nb' => '70000001',
            'profile_image' => null,
            'date_of_birth' => '1990-01-01',
            'address' => 'Beirut, Lebanon'
        ]);

        User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'is_landlord' => false,
            'phone_nb' => '70000002',
            'profile_image' => null,
            'date_of_birth' => '1995-05-15',
            'address' => 'Tripoli, Lebanon'
        ]);
    }
} 
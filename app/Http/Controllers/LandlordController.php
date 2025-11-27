<?php

namespace App\Http\Controllers;

use App\Models\LandlordApplication;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $properties = Property::where('user_id', $user->id)
            ->with(['images', 'amenities', 'rules'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_properties' => Property::where('user_id', $user->id)->count(),
            'active_listings' => Property::where('user_id', $user->id)->count(),
            'total_views' => 0,
            'total_likes' => DB::table('property_likes')
                ->join('properties', 'property_likes.property_id', '=', 'properties.id')
                ->where('properties.user_id', $user->id)
                ->count(),
        ];

        return view('landlord.dashboard', compact('properties', 'stats'));
    }

    public function showApplyForm()
    {
        $user = Auth::user();

        if ($user->role === 'landlord' || $user->role === 'admin') {
            return redirect()->route('landlord.dashboard')
                ->with('info', 'You are already a landlord.');
        }

        $existingApplication = LandlordApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return redirect()->route('landlord.dashboard')
                ->with('info', 'You have a pending landlord application. Please wait for admin approval.');
        }

        return view('landlord.apply');
    }

    public function submitApplication(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'landlord' || $user->role === 'admin') {
            return redirect()->route('landlord.dashboard')
                ->with('info', 'You are already a landlord.');
        }

        $existingApplication = LandlordApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return redirect()->route('landlord.apply')
                ->with('error', 'You already have a pending application.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:255',
            'trade_license' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';

        LandlordApplication::create($validated);

        return redirect()->route('landlord.dashboard')
            ->with('success', 'Your landlord application has been submitted successfully! We will review it and get back to you soon.');
    }

    public function properties()
    {
        $user = Auth::user();
        $properties = Property::where('user_id', $user->id)
            ->with(['images', 'amenities', 'rules', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('landlord.properties', compact('properties'));
    }
}

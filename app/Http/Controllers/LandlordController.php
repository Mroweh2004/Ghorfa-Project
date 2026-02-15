<?php

namespace App\Http\Controllers;

use App\Models\LandlordApplication;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\CreatesNotifications;

class LandlordController extends Controller
{
    use CreatesNotifications;
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get properties grouped by status
        $approvedProperties = Property::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['images' => function($query) {
                $query->orderBy('is_primary', 'desc')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingProperties = Property::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['images' => function($query) {
                $query->orderBy('is_primary', 'desc')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $rejectedProperties = Property::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->with(['images' => function($query) {
                $query->orderBy('is_primary', 'desc')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get transaction requests for landlord's properties
        $propertyIds = Property::where('user_id', $user->id)->pluck('id');
        $transactionRequests = Transaction::whereIn('property_id', $propertyIds)
            ->with(['user', 'property'])
            ->where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('contract_generated_at');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get transactions awaiting actions (contract approved, payment pending, etc)
        $activeTransactions = Transaction::whereIn('property_id', $propertyIds)
            ->with(['user', 'property'])
            ->whereIn('status', ['confirmed', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_properties' => Property::where('user_id', $user->id)->count(),
            'active_listings' => Property::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending_properties' => $pendingProperties->count(),
            'total_views' => 0,
            'total_likes' => DB::table('property_likes')
                ->join('properties', 'property_likes.property_id', '=', 'properties.id')
                ->where('properties.user_id', $user->id)
                ->count(),
            'pending_requests' => $transactionRequests->count(),
            'active_transactions' => $activeTransactions->count(),
        ];

        return view('landlord.dashboard', compact('approvedProperties', 'pendingProperties', 'rejectedProperties', 'stats', 'transactionRequests', 'activeTransactions'));
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

        $application = LandlordApplication::create($validated);
        
        // Create notification for all admins about new pending application
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->createNotification(
                $admin,
                'pending',
                'New Landlord Application',
                $user->name . ' has submitted a landlord application. Please review it.',
                $application
            );
        }

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

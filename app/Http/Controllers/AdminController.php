<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('role', '!=', 'admin')->get();
        $pendingApplications = LandlordApplication::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.dashboard', compact('users', 'pendingApplications'));
    }

    public function deleteUser(User $user)
    {
        if ($user->role !== 'admin') {
            $user->delete();
            return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully');
        }
        return redirect()->route('admin.dashboard')->with('error', 'Cannot delete admin user');
    }

    public function approveLandlordApplication(LandlordApplication $application)
    {
        DB::transaction(function () use ($application) {
            $application->user->update(['role' => 'landlord']);
            $application->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Landlord application approved successfully!');
    }

    public function rejectLandlordApplication(Request $request, LandlordApplication $application)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Landlord application rejected.');
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordApplication;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\CreatesNotifications;

class AdminController extends Controller
{
    use CreatesNotifications;
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_properties' => Property::count(),
            'pending_applications' => LandlordApplication::where('status', 'pending')->count(),
            'approved_applications' => LandlordApplication::where('status', 'approved')->count(),
            'rejected_applications' => LandlordApplication::where('status', 'rejected')->count(),
            'total_likes' => DB::table('property_likes')->count(),
            'total_reviews' => DB::table('reviews')->count(),
        ];

        // Data
        $users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $pendingApplications = LandlordApplication::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent activity
        $recentUsers = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProperties = Property::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('users', 'pendingApplications', 'stats', 'recentUsers', 'recentProperties'));
    }

    public function deleteUser(User $user)
    {
        if ($user->role !== 'admin') {
            $user->delete();
            return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully');
        }
        return redirect()->route('admin.dashboard')->with('error', 'Cannot delete admin user');
    }

    public function getPendingApplications()
    {
        $pendingApplications = LandlordApplication::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'user_name' => $application->user->name,
                    'user_email' => $application->user->email,
                    'phone' => $application->phone ?? $application->user->phone_nb,
                    'applied_at' => $application->created_at->diffForHumans(),
                    'created_at' => $application->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'applications' => $pendingApplications,
            'count' => $pendingApplications->count(),
        ]);
    }

    public function approveLandlordApplication(Request $request, LandlordApplication $application)
    {
        try {
            DB::transaction(function () use ($application) {
                $application->user->update(['role' => 'landlord']);
                $application->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
                
                $this->createNotification(
                    $application->user,
                    'approve',
                    'Landlord Application Approved!',
                    'Congratulations! Your landlord application has been approved. You can now list properties.',
                    $application
                );
            });

            $isAjax = $request->expectsJson() 
                || $request->ajax() 
                || $request->wantsJson() 
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Landlord application approved successfully!',
                ]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Landlord application approved successfully!');
        } catch (\Exception $e) {
            Log::error('Error approving landlord application: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);

            $isAjax = $request->expectsJson() 
                || $request->ajax() 
                || $request->wantsJson() 
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve application: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to approve application.');
        }
    }

    public function rejectLandlordApplication(Request $request, LandlordApplication $application)
    {
        try {
            $adminNotes = $request->input('admin_notes');
            
            if (empty($adminNotes) || trim($adminNotes) === '') {
                $adminNotes = null;
            }

            $application->update([
                'status' => 'rejected',
                'admin_notes' => $adminNotes,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
            
            $this->createNotification(
                $application->user,
                'reject',
                'Landlord Application Rejected',
                'Your landlord application has been rejected. ' . ($adminNotes ? 'Note: ' . $adminNotes : 'Please contact support for more information.'),
                $application
            );

            $isAjax = $request->expectsJson() 
                || $request->ajax() 
                || $request->wantsJson() 
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Landlord application rejected.',
                ]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Landlord application rejected.');
        } catch (\Exception $e) {
            Log::error('Error rejecting landlord application: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);

            $isAjax = $request->expectsJson() 
                || $request->ajax() 
                || $request->wantsJson() 
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject application: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to reject application.');
        }
    }
} 
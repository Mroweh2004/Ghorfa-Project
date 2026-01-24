<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordApplication;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\CreatesNotifications;
use App\Traits\LogsActivity;
use App\Models\Activity;

class AdminController extends Controller
{
    use CreatesNotifications, LogsActivity;
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_properties' => Property::count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'approved_properties' => Property::where('status', 'approved')->count(),
            'pending_applications' => LandlordApplication::where('status', 'pending')->count(),
            'approved_applications' => LandlordApplication::where('status', 'approved')->count(),
            'rejected_applications' => LandlordApplication::where('status', 'rejected')->count(),
            'total_likes' => DB::table('property_likes')->count(),
            'total_reviews' => DB::table('reviews')->count(),
        ];

        // Data
        $users = User::where('role', '!=', 'admin')
            ->where('role', 'client')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $landlords = User::where('role', '!=', 'admin')
            ->where('role', 'landlord')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingApplications = LandlordApplication::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Pending properties for approval
        $pendingProperties = Property::with(['user', 'images' => function($query) {
                $query->orderBy('is_primary', 'desc')->limit(1);
            }])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        
        // Recent activity
        $recentUsers = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProperties = Property::with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = Activity::with(['user', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.dashboard', compact('users', 'landlords', 'pendingApplications', 'stats', 'recentUsers', 'recentProperties', 'pendingProperties', 'recentActivities'));
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

                // Log activity
                $this->logActivity(
                    'application_approved',
                    "Landlord application from {$application->user->name} was approved",
                    $application,
                    ['application_id' => $application->id, 'user_id' => $application->user->id, 'user_name' => $application->user->name]
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

    public function approveProperty(Request $request, Property $property)
    {
        try {
            DB::transaction(function () use ($property) {
                $property->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);
                
                // Notify the landlord that their property has been approved
                try {
                    $this->createNotification(
                        $property->user,
                        'approve',
                        'Property Approved!',
                        'Congratulations! Your property "' . $property->title . '" has been approved by an admin and is now published and visible to all users.',
                        $property
                    );
                    Log::info('Property approval notification sent', [
                        'property_id' => $property->id,
                        'landlord_id' => $property->user_id,
                        'landlord_name' => $property->user->name
                    ]);
                } catch (\Exception $notificationError) {
                    // Log activity
                    $this->logActivity(
                        'property_approved',
                        "Property '{$property->title}' was approved",
                        $property,
                        ['property_id' => $property->id, 'property_title' => $property->title, 'landlord_id' => $property->user_id]
                    );

                    Log::error('Failed to send property approval notification', [
                        'property_id' => $property->id,
                        'landlord_id' => $property->user_id,
                        'error' => $notificationError->getMessage()
                    ]);
                    // Don't fail the transaction if notification fails
                }
            });

            $isAjax = $request->expectsJson() 
                || $request->ajax() 
                || $request->wantsJson() 
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Property approved successfully!',
                ]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Property approved successfully!');
        } catch (\Exception $e) {
            Log::error('Error approving property: ' . $e->getMessage(), [
                'property_id' => $property->id,
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
                    'message' => 'Failed to approve property: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to approve property.');
        }
    }

    public function rejectProperty(Request $request, Property $property)
    {
        try {
            $adminNotes = $request->input('admin_notes');
            
            if (empty($adminNotes) || trim($adminNotes) === '') {
                $adminNotes = null;
            }

            DB::transaction(function () use ($property, $adminNotes) {
                $property->update([
                    'status' => 'rejected',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);
                
                // Notify the landlord that their property has been rejected
                try {
                    $rejectionMessage = 'Your property "' . $property->title . '" has been rejected by an admin.';
                    if ($adminNotes) {
                        $rejectionMessage .= ' Reason: ' . $adminNotes;
                    } else {
                        $rejectionMessage .= ' Please contact support for more information or review your property details and resubmit.';
                    }
                    
                    $this->createNotification(
                        $property->user,
                        'reject',
                        'Property Rejected',
                        $rejectionMessage,
                        $property
                    );
                    Log::info('Property rejection notification sent', [
                        'property_id' => $property->id,
                        'landlord_id' => $property->user_id,
                        'landlord_name' => $property->user->name,
                        'admin_notes' => $adminNotes
                    ]);
                } catch (\Exception $notificationError) {
                    Log::error('Failed to send property rejection notification', [
                        'property_id' => $property->id,
                        'landlord_id' => $property->user_id,
                        'error' => $notificationError->getMessage()
                    ]);
                    // Don't fail the transaction if notification fails
                }

                // Log activity
                $this->logActivity(
                    'property_rejected',
                    "Property '{$property->title}' was rejected",
                    $property,
                    ['property_id' => $property->id, 'property_title' => $property->title, 'landlord_id' => $property->user_id, 'admin_notes' => $adminNotes]
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
                    'message' => 'Property rejected.',
                ]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Property rejected.');
        } catch (\Exception $e) {
            Log::error('Error rejecting property: ' . $e->getMessage(), [
                'property_id' => $property->id,
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
                    'message' => 'Failed to reject property: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to reject property.');
        }
    }
} 
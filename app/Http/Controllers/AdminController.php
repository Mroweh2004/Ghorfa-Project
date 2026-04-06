<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordApplication;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\CreatesNotifications;
use App\Traits\LogsActivity;
use App\Models\Activity;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    use CreatesNotifications, LogsActivity;

    private const SESSION_KEYS = [
        'applications' => 'admin_seen_applications_at',
        'landlords' => 'admin_seen_landlords_at',
        'users' => 'admin_seen_users_at',
        'properties' => 'admin_seen_pending_properties_at',
    ];

    public function dashboard()
    {
        // "New" counts: only items since last time admin opened that section (null = never seen = all count as new)
        $seenApplications = Session::get(self::SESSION_KEYS['applications']);
        $seenLandlords = Session::get(self::SESSION_KEYS['landlords']);
        $seenUsers = Session::get(self::SESSION_KEYS['users']);
        $seenProperties = Session::get(self::SESSION_KEYS['properties']);

        $stats = [
            'new_users' => $seenUsers
                ? User::where('role', 'client')->where('created_at', '>', $seenUsers)->count()
                : User::where('role', 'client')->count(),
            'new_landlords' => $seenLandlords
                ? User::where('role', 'landlord')->where('created_at', '>', $seenLandlords)->count()
                : User::where('role', 'landlord')->count(),
            'new_pending_properties' => $seenProperties
                ? Property::where('status', 'pending')->where('created_at', '>', $seenProperties)->count()
                : Property::where('status', 'pending')->count(),
            'new_pending_applications' => $seenApplications
                ? LandlordApplication::where('status', 'pending')->where('created_at', '>', $seenApplications)->count()
                : LandlordApplication::where('status', 'pending')->count(),
        ];

        // Data
        $users = User::where('role', '!=', 'admin')
            ->where('role', 'client')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $landlords = User::where('role', '!=', 'admin')
            ->where('role', 'landlord')
            ->with('landlordApplication')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingApplications = LandlordApplication::with(['user', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Pending properties for approval
        $pendingProperties = Property::with(['landlord', 'images' => function($query) {
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

        $recentProperties = Property::with('landlord')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = Activity::with(['user', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $overview = [
            'clients' => User::where('role', 'client')->count(),
            'landlords' => User::where('role', 'landlord')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'users_total' => User::where('role', '!=', 'admin')->count(),
            'properties_total' => Property::count(),
            'properties_approved' => Property::where('status', 'approved')->count(),
            'properties_pending' => Property::where('status', 'pending')->count(),
            'properties_rejected' => Property::where('status', 'rejected')->count(),
            'pending_applications' => LandlordApplication::where('status', 'pending')->count(),
            'transactions_pending' => Transaction::where('status', 'pending')->count(),
            'transactions_active' => Transaction::whereIn('status', ['confirmed'])->count(),
            'transactions_completed' => Transaction::where('status', 'completed')->count(),
            'reviews_total' => Review::count(),
        ];

        $chartData = $this->buildAdminChartData(14);

        $transactionsList = Transaction::query()
            ->with(['user', 'property.landlord'])
            ->orderByDesc('updated_at')
            ->paginate(20, ['*'], 'txns');

        return view('admin.dashboard', compact(
            'users',
            'landlords',
            'pendingApplications',
            'stats',
            'recentUsers',
            'recentProperties',
            'pendingProperties',
            'recentActivities',
            'overview',
            'chartData',
            'transactionsList'
        ));
    }

    /**
     * Daily series and breakdowns for admin dashboard charts (Chart.js).
     */
    private function buildAdminChartData(int $days): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $labels = [];
        $dateKeys = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $labels[] = $d->format('M j');
            $dateKeys[] = $d->format('Y-m-d');
        }

        $seriesForRole = function (string $role) use ($start, $end, $dateKeys): array {
            $byDay = User::query()
                ->where('role', $role)
                ->whereBetween('created_at', [$start, $end])
                ->get(['created_at'])
                ->groupBy(fn (User $u) => $u->created_at->format('Y-m-d'))
                ->map->count();

            $out = [];
            foreach ($dateKeys as $key) {
                $out[] = (int) ($byDay[$key] ?? 0);
            }

            return $out;
        };

        $propertiesByDay = Property::query()
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at'])
            ->groupBy(fn (Property $p) => $p->created_at->format('Y-m-d'))
            ->map->count();

        $propertiesSeries = [];
        foreach ($dateKeys as $key) {
            $propertiesSeries[] = (int) ($propertiesByDay[$key] ?? 0);
        }

        $applicationsByDay = LandlordApplication::query()
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at'])
            ->groupBy(fn (LandlordApplication $a) => $a->created_at->format('Y-m-d'))
            ->map->count();

        $applicationsSeries = [];
        foreach ($dateKeys as $key) {
            $applicationsSeries[] = (int) ($applicationsByDay[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                'clients' => $seriesForRole('client'),
                'landlords' => $seriesForRole('landlord'),
                'properties' => $propertiesSeries,
                'applications' => $applicationsSeries,
            ],
            'propertyStatus' => [
                'labels' => ['Approved', 'Pending', 'Rejected'],
                'data' => [
                    Property::where('status', 'approved')->count(),
                    Property::where('status', 'pending')->count(),
                    Property::where('status', 'rejected')->count(),
                ],
            ],
            'listingTypes' => [
                'labels' => ['Rent', 'Sale'],
                'data' => [
                    Property::where('status', 'approved')
                        ->whereRaw('LOWER(COALESCE(listing_type, ?)) = ?', ['', 'rent'])
                        ->count(),
                    Property::where('status', 'approved')
                        ->whereRaw('LOWER(COALESCE(listing_type, ?)) IN (?, ?, ?)', ['', 'sale', 'buy', 'sell'])
                        ->count(),
                ],
            ],
        ];
    }

    /**
     * Mark a dashboard section as seen so "new" counts for that section go to zero.
     */
    public function markSectionSeen(Request $request)
    {
        $section = $request->input('section');
        if (!isset(self::SESSION_KEYS[$section])) {
            return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }
        Session::put(self::SESSION_KEYS[$section], now()->toDateTimeString());
        return response()->json(['success' => true]);
    }

    public function showUser(User $user)
    {
        $user->load([
            'landlordApplication.reviewer',
            'properties' => fn ($q) => $q->select('id', 'user_id', 'title', 'status', 'created_at')->latest()->limit(10),
        ]);

        $propertyCount = Property::where('user_id', $user->id)->count();
        $reviewCount = Review::where('user_id', $user->id)->count();

        return view('admin.user-show', compact('user', 'propertyCount', 'reviewCount'));
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
                    'user_show_url' => route('admin.users.show', $application->user),
                    'user_name' => $application->user->name,
                    'user_email' => $application->user->email,
                    'phone' => $application->phone ?? $application->user->phone_nb,
                    'document_type' => $application->document_type,
                    'document_number' => $application->verificationNumber(),
                    'front_url' => $application->document_front_path
                        ? asset('storage/'.$application->document_front_path)
                        : null,
                    'back_url' => $application->document_back_path
                        ? asset('storage/'.$application->document_back_path)
                        : null,
                    'face_url' => $application->face_photo_path
                        ? asset('storage/'.$application->face_photo_path)
                        : null,
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
        $isAjax = $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest'
            || $request->header('Accept') === 'application/json';

        if (! $application->isPending()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'This application is not pending approval.',
                ], 422);
            }

            return back()->with('error', 'This application is not pending approval.');
        }

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

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Landlord application approved successfully!',
                ]);
            }

            return back()->with('success', 'Landlord application approved successfully!');
        } catch (\Exception $e) {
            Log::error('Error approving landlord application: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve application: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to approve application.');
        }
    }

    public function rejectLandlordApplication(Request $request, LandlordApplication $application)
    {
        $isAjax = $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest'
            || $request->header('Accept') === 'application/json';

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

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Landlord application rejected.',
                ]);
            }

            return back()->with('success', 'Landlord application rejected.');
        } catch (\Exception $e) {
            Log::error('Error rejecting landlord application: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject application: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to reject application.');
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
                    if ($property->user) {
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
                    }
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
                    
                    if ($property->user) {
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
                    }
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
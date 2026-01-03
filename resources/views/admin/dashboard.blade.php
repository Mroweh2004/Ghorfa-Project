@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<div class="admin-dashboard">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_users'] }}</h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_landlords'] }}</h3>
                <p>Landlords</p>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_properties'] }}</h3>
                <p>Properties</p>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['pending_applications'] }}</h3>
                <p>Pending Applications</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <a href="#applications-section" class="action-card">
                <i class="fas fa-file-alt"></i>
                <span>Review Applications</span>
            </a>
            <a href="#users-section" class="action-card">
                <i class="fas fa-user-cog"></i>
                <span>Manage Users</span>
            </a>
            <a href="#recent-activity" class="action-card">
                <i class="fas fa-history"></i>
                <span>Recent Activity</span>
            </a>
        </div>
    </div>

    <!-- Main Content Sections -->
    <div class="dashboard-content" data-pending-applications-route="{{ route('admin.pending-applications') }}">
        
        <!-- Pending Applications Section -->
        <div id="applications-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-file-alt"></i>
                    Pending Landlord Applications
                    @if($stats['pending_applications'] > 0)
                        <span class="badge badge-warning">{{ $stats['pending_applications'] }}</span>
                    @endif
                </h2>
            </div>
            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pendingApplications->count() > 0)
                            @foreach($pendingApplications as $application)
                            <tr data-application-id="{{ $application->id }}">
                                <td>{{ $application->user->name }}</td>
                                <td>{{ $application->user->email }}</td>
                                <td>{{ $application->phone ?? $application->user->phone_nb ?? 'N/A' }}</td>
                                <td>{{ $application->created_at->diffForHumans() }}</td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-success approve-btn" 
                                        data-application-id="{{ $application->id }}"
                                        onclick="handleApprove({{ $application->id }})"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger reject-btn" 
                                        data-application-id="{{ $application->id }}"
                                        onclick="handleReject({{ $application->id }})"
                                    >
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="empty-state-cell">
                                    <i class="fas fa-check-circle"></i>
                                    No pending applications
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Management Section -->
        <div id="users-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-user-cog"></i>
                    User Management
                </h2>
            </div>
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-state-cell">
                                <i class="fas fa-users"></i>
                                No users found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div id="recent-activity" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Recent Activity
                </h2>
            </div>
            <div class="activity-grid">
                <div class="activity-card">
                    <h3><i class="fas fa-user-plus"></i> Recent Users</h3>
                    <ul class="activity-list">
                        @forelse($recentUsers as $user)
                        <li>
                            <span class="activity-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="activity-content">
                                <strong>{{ $user->name }}</strong>
                                <span class="activity-meta">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                        </li>
                        @empty
                        <li class="empty-activity">No recent users</li>
                        @endforelse
                    </ul>
                </div>

                <div class="activity-card">
                    <h3><i class="fas fa-building"></i> Recent Properties</h3>
                    <ul class="activity-list">
                        @forelse($recentProperties as $property)
                        <li>
                            <span class="activity-icon">
                                <i class="fas fa-home"></i>
                            </span>
                            <div class="activity-content">
                                <strong>{{ $property->title }}</strong>
                                <span class="activity-meta">by {{ $property->user->name }} • {{ $property->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="empty-activity">No recent properties</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection 
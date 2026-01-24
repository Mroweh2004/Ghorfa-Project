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

        <!-- Landlord Management Section -->
        <div id="landlords-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-user-cog"></i>
                    Landlord Management
                </h2>
            </div>
            <div class="landlords-table">
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
                        @forelse($landlords as $landlord)
                        <tr>
                            <td>{{ $landlord->id }}</td>
                            <td>{{ $landlord->name }}</td>
                            <td>{{ $landlord->email }}</td>
                            <td>
                                <span class="role-badge role-{{ $landlord->role }}">
                                    {{ ucfirst($landlord->role) }}
                                </span>
                            </td>
                            <td>{{ $landlord->created_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('admin.users.delete', $landlord->id) }}" method="POST" style="display: inline;">
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
                    {{ $landlords->links() }}
                </div>
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

        <!-- Pending Properties Section -->
        <div id="properties-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-building"></i>
                    Pending Properties for Approval
                    @if($stats['pending_properties'] > 0)
                        <span class="badge badge-warning">{{ $stats['pending_properties'] }}</span>
                    @endif
                </h2>
            </div>
            <div class="properties-table">
                <table>
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Landlord</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pendingProperties->count() > 0)
                            @foreach($pendingProperties as $property)
                            <tr data-property-id="{{ $property->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @if($property->images->first())
                                            <img src="{{ asset('storage/' . $property->images->first()->path) }}" alt="{{ $property->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                        @else
                                            <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-home" style="color: #94a3b8;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $property->title }}</strong>
                                            <div style="font-size: 0.875rem; color: #64748b;">{{ $property->property_type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $property->user->name }}</td>
                                <td>{{ $property->city }}, {{ $property->country }}</td>
                                <td>${{ number_format($property->price) }}/month</td>
                                <td>{{ $property->created_at->diffForHumans() }}</td>
                                <td>
                                    <a 
                                        href="{{ route('properties.show', $property->id) }}" 
                                        target="_blank"
                                        class="btn btn-info"
                                        style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;"
                                    >
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-success approve-property-btn" 
                                        data-property-id="{{ $property->id }}"
                                        onclick="handleApproveProperty({{ $property->id }})"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger reject-property-btn" 
                                        data-property-id="{{ $property->id }}"
                                        onclick="handleRejectProperty({{ $property->id }})"
                                    >
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <i class="fas fa-check-circle"></i>
                                    No pending properties
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div id="recent-activity" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Recent Activities
                </h2>
            </div>
            <ul class="activity-list">
                @if($recentActivities->count() > 0)
                    @foreach($recentActivities as $activity)
                        <li>
                            <div class="activity-icon">
                                @if($activity->type === 'property_created')
                                    <i class="fas fa-plus-circle text-success"></i>
                                @elseif($activity->type === 'property_updated')
                                    <i class="fas fa-edit text-info"></i>
                                @elseif($activity->type === 'property_deleted')
                                    <i class="fas fa-trash text-danger"></i>
                                @elseif($activity->type === 'property_approved')
                                    <i class="fas fa-check-circle text-success"></i>
                                @elseif($activity->type === 'property_rejected')
                                    <i class="fas fa-times-circle text-danger"></i>
                                @elseif($activity->type === 'application_approved')
                                    <i class="fas fa-user-check text-success"></i>
                                @elseif($activity->type === 'application_rejected')
                                    <i class="fas fa-user-times text-danger"></i>
                                @else
                                    <i class="fas fa-circle text-primary"></i>
                                @endif
                            </div>
                            <div class="activity-content">
                                <div class="activity-description">
                                    {{ $activity->description }}
                                </div>
                                <div class="activity-meta">
                                    @if($activity->user)
                                        <span class="activity-user">
                                            <i class="fas fa-user"></i> {{ $activity->user->name }}
                                        </span>
                                    @endif
                                    <span class="activity-time">
                                        <i class="fas fa-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                @else
                    <li class="empty-activity">
                        <i class="fas fa-history"></i>
                        <span>No activities yet</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/admin.js') }}"></script>
@endpush 
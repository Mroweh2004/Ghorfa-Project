@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="dashboard-content">
        @if($pendingApplications->count() > 0)
        <div class="applications-section" style="margin-bottom: 2rem;">
            <h2>Pending Landlord Applications ({{ $pendingApplications->count() }})</h2>
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
                        @foreach($pendingApplications as $application)
                        <tr>
                            <td>{{ $application->user->name }}</td>
                            <td>{{ $application->user->email }}</td>
                            <td>{{ $application->phone ?? $application->user->phone_nb }}</td>
                            <td>{{ $application->created_at->diffForHumans() }}</td>
                            <td style="display: flex; gap: 0.5rem;">
                                <form action="{{ route('admin.landlord.approve', $application->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this landlord application?')">Approve</button>
                                </form>
                                <form action="{{ route('admin.landlord.reject', $application->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this landlord application?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="users-section">
            <h2>User Management</h2>
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
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: {{ $user->role === 'landlord' ? '#10b981' : ($user->role === 'admin' ? '#ef4444' : '#6b7280') }}; color: white; font-size: 0.875rem;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at }}</td>
                            <td>
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 
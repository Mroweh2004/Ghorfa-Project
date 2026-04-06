<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <title>Admin Dashboard</title>
</head>
<body class="admin-body">
    @include('partials.nav')

    <div class="admin-wrapper">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                
                <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">MENU</div>
                    <ul class="nav-menu">
                        <li class="nav-item active">
                            <a href="#dashboard-section" class="nav-link">
                                <i class="fas fa-th-large"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#users-section" class="nav-link">
                                <i class="fas fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#landlords-section" class="nav-link">
                                <i class="fas fa-user-tie"></i>
                                <span>Landlords</span>
                                @include('components.nav-badge', ['key' => 'new_pending_applications'])
                                @include('components.nav-badge', ['key' => 'new_landlords'])
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#properties-section" class="nav-link">
                                <i class="fas fa-building"></i>
                                <span>Pending Properties</span>
                                @include('components.nav-badge', ['key' => 'new_pending_properties'])
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#transactions-section" class="nav-link">
                                <i class="fas fa-handshake"></i>
                                <span>Transactions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.amenities.index') }}" class="nav-link">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Amenities</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.rules.index') }}" class="nav-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Rules</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#recent-activity" class="nav-link">
                                <i class="fas fa-history"></i>
                                <span>Activity</span>
                                @include('components.nav-badge', ['key' => 'activities'])
                            </a>
                        </li>
                    </ul>
                </div>

                
        </aside>

        <div class="admin-main">
            <div class="admin-mobile-sidebar-bar">
                <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Open admin menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <main class="admin-content">
                <div class="admin-dashboard-search" role="search">
                    <label class="admin-search-sr-only" for="adminSearch">Search the visible dashboard section</label>
                    <div class="admin-dashboard-search-inner">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="search" id="adminSearch" name="admin_search" autocomplete="off" placeholder="Search current section…">
                        <kbd class="admin-search-kbd" title="Focus search">Ctrl K</kbd>
                    </div>
                </div>
<div class="admin-dashboard">
    <div class="dashboard-content" data-pending-applications-route="{{ route('admin.pending-applications') }}" data-mark-section-seen-url="{{ route('admin.mark-section-seen') }}">

        <div id="dashboard-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </h2>
            </div>
            <p class="dashboard-lead">New counts reset when you open each area from the menu. User and review totals are below; listings, application queue, and transaction summaries live in their sidebar sections.</p>

            <h3 class="dashboard-subtitle"><i class="fas fa-bolt"></i> Needs attention (new)</h3>
            <div class="stats-grid stats-grid--dashboard">
                <div class="stat-card stat-card-primary" data-stat-section="users">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-count">{{ $stats['new_users'] }}</h3>
                        <p>New Users</p>
                    </div>
                </div>

                <div class="stat-card stat-card-success" data-stat-section="landlords">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-count">{{ $stats['new_landlords'] }}</h3>
                        <p>New Landlords</p>
                    </div>
                </div>

                <div class="stat-card stat-card-info" data-stat-section="properties">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-count">{{ $stats['new_pending_properties'] }}</h3>
                        <p>New Pending Properties</p>
                    </div>
                </div>

                <div class="stat-card stat-card-warning" data-stat-section="applications">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-count">{{ $stats['new_pending_applications'] }}</h3>
                        <p>New Pending Applications</p>
                    </div>
                </div>
            </div>

            <h3 class="dashboard-subtitle"><i class="fas fa-chart-pie"></i> Platform totals</h3>
            <div class="admin-overview-sections">
                <section class="admin-overview-group" aria-labelledby="overview-users-heading">
                    <h4 class="admin-overview-group__title" id="overview-users-heading">
                        <i class="fas fa-users"></i> Users &amp; roles
                    </h4>
                    <div class="admin-overview-grid">
                        <div class="overview-tile">
                            <span class="overview-tile__icon"><i class="fas fa-user-friends"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['clients']) }}</span>
                            <span class="overview-tile__label">Clients</span>
                        </div>
                        <div class="overview-tile">
                            <span class="overview-tile__icon"><i class="fas fa-user-tie"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['landlords']) }}</span>
                            <span class="overview-tile__label">Landlords</span>
                        </div>
                        <div class="overview-tile">
                            <span class="overview-tile__icon"><i class="fas fa-user-shield"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['admins']) }}</span>
                            <span class="overview-tile__label">Admins</span>
                        </div>
                        <div class="overview-tile">
                            <span class="overview-tile__icon"><i class="fas fa-users"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['users_total']) }}</span>
                            <span class="overview-tile__label">Members (excl. admin)</span>
                        </div>
                    
                        <div class="overview-tile">
                            <span class="overview-tile__icon"><i class="fas fa-star"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['reviews_total']) }}</span>
                            <span class="overview-tile__label">Total reviews</span>
                        </div>
                    </div>
                </section>

            </div>

            <h3 class="dashboard-subtitle"><i class="fas fa-chart-line"></i> Trends (last {{ count($chartData['labels']) }} days)</h3>
            <div class="admin-charts-grid">
                <div class="admin-chart-card admin-chart-card--wide">
                    <div class="admin-chart-card__head">
                        <h4><i class="fas fa-chart-area"></i> Activity</h4>
                        <p class="admin-chart-card__hint">New clients, landlords, property listings, and landlord applications per day</p>
                    </div>
                    <div class="admin-chart-canvas-wrap">
                        <canvas id="adminChartActivity" aria-label="Daily activity chart"></canvas>
                    </div>
                </div>
                <div class="admin-chart-card">
                    <div class="admin-chart-card__head">
                        <h4><i class="fas fa-chart-pie"></i> Listings by status</h4>
                    </div>
                    <div class="admin-chart-canvas-wrap admin-chart-canvas-wrap--donut">
                        <canvas id="adminChartPropertyStatus" aria-label="Property status distribution"></canvas>
                    </div>
                </div>
                <div class="admin-chart-card">
                    <div class="admin-chart-card__head">
                        <h4><i class="fas fa-tags"></i> Approved listings: rent vs sale</h4>
                    </div>
                    <div class="admin-chart-canvas-wrap">
                        <canvas id="adminChartListingTypes" aria-label="Listing type breakdown"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Landlords: pending applications + directory (one nav item); each block is its own panel -->
        <div id="landlords-section" class="content-section landlords-hub" data-mark-sections="applications landlords">
            <div id="applications-section" class="landlords-subsection landlords-panel applications-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-file-alt"></i>
                        Pending Landlord Applications
                        @if($stats['new_pending_applications'] > 0)
                            <span class="badge badge-warning section-badge" data-badge-section="applications">{{ $stats['new_pending_applications'] }}</span>
                        @endif
                    </h2>
                </div>
                <section class="admin-overview-group admin-section-stats" aria-labelledby="section-applications-stats-heading">
                    <h4 class="admin-overview-group__title" id="section-applications-stats-heading">
                        <i class="fas fa-file-signature"></i> Application queue
                    </h4>
                    <div class="admin-overview-grid">
                        <div class="overview-tile overview-tile--accent">
                            <span class="overview-tile__icon"><i class="fas fa-file-signature"></i></span>
                            <span class="overview-tile__value">{{ number_format($overview['pending_applications']) }}</span>
                            <span class="overview-tile__label">Pending applications</span>
                        </div>
                    </div>
                </section>
                <div class="applications-table">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Document</th>
                                <th>Number</th>
                                <th>Photos</th>
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
                                    <td>
                                        @if($application->document_type === 'national_id')
                                            National ID
                                        @elseif($application->document_type === 'trade_license')
                                            Trade license
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $application->verificationNumber() ?? '—' }}</td>
                                    <td>
                                        @if($application->document_front_path && $application->document_back_path)
                                            <a href="{{ asset('storage/'.$application->document_front_path) }}" target="_blank" rel="noopener noreferrer">Front</a>
                                            <span class="admin-app-doc-sep">·</span>
                                            <a href="{{ asset('storage/'.$application->document_back_path) }}" target="_blank" rel="noopener noreferrer">Back</a>
                                            @if($application->face_photo_path)
                                                <span class="admin-app-doc-sep">·</span>
                                                <a href="{{ asset('storage/'.$application->face_photo_path) }}" target="_blank" rel="noopener noreferrer">Face</a>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $application->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a
                                            href="{{ route('admin.users.show', $application->user) }}"
                                            class="btn btn-info"
                                        >
                                            <i class="fas fa-eye" aria-hidden="true"></i> View
                                        </a>
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
                                    <td colspan="8" class="empty-state-cell">
                                        <i class="fas fa-check-circle"></i>
                                        No pending applications
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="landlords-directory" class="landlords-subsection landlords-panel">
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
                                <th>ID doc &amp; face</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($landlords as $landlord)
                            @php $landlordApp = $landlord->landlordApplication; @endphp
                            <tr>
                                <td>{{ $landlord->id }}</td>
                                <td>{{ $landlord->name }}</td>
                                <td>{{ $landlord->email }}</td>
                                <td class="admin-landlord-verification-cell">
                                    <div class="admin-verification-thumbs" role="group" aria-label="Verification thumbnails">
                                        @if($landlordApp && $landlordApp->document_front_path)
                                            <a href="{{ route('admin.users.show', $landlord) }}" class="admin-v-thumb" title="National ID (front) — full profile">
                                                <img src="{{ asset('storage/'.$landlordApp->document_front_path) }}" alt="" loading="lazy" width="56" height="56">
                                            </a>
                                        @else
                                            <span class="admin-v-thumb admin-v-thumb--empty" title="No ID document on file">—</span>
                                        @endif
                                        @if($landlordApp && $landlordApp->face_photo_path)
                                            <a href="{{ route('admin.users.show', $landlord) }}" class="admin-v-thumb" title="Face photo — full profile">
                                                <img src="{{ asset('storage/'.$landlordApp->face_photo_path) }}" alt="" loading="lazy" width="56" height="56">
                                            </a>
                                        @else
                                            <span class="admin-v-thumb admin-v-thumb--empty" title="No face photo on file">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $landlord->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $landlord) }}" class="btn btn-info btn-sm admin-view-user-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; margin-right: 0.35rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.users.delete', $landlord) }}" method="POST" style="display: inline;">
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
        </div>

    <!-- User Management Section -->
    <div id="users-section" class="content-section" data-section-name="users">
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
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm admin-view-user-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; margin-right: 0.35rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" style="display: inline;">
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
        <div id="properties-section" class="content-section" data-section-name="properties">
            <div class="section-header">
                <h2>
                    <i class="fas fa-building"></i>
                    Pending Properties for Approval
                    @if($stats['new_pending_properties'] > 0)
                        <span class="badge badge-warning section-badge" data-badge-section="properties">{{ $stats['new_pending_properties'] }}</span>
                    @endif
                </h2>
            </div>
            <section class="admin-overview-group admin-section-stats" aria-labelledby="section-listings-stats-heading">
                <h4 class="admin-overview-group__title" id="section-listings-stats-heading">
                    <i class="fas fa-building"></i> Listings overview
                </h4>
                <div class="admin-overview-grid">
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-building"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['properties_total']) }}</span>
                        <span class="overview-tile__label">All properties</span>
                    </div>
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-check-circle"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['properties_approved']) }}</span>
                        <span class="overview-tile__label">Approved listings</span>
                    </div>
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-hourglass-half"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['properties_pending']) }}</span>
                        <span class="overview-tile__label">Pending approval</span>
                    </div>
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-ban"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['properties_rejected']) }}</span>
                        <span class="overview-tile__label">Rejected listings</span>
                    </div>
                </div>
            </section>
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
                                <td>{{ optional($property->landlord)->name ?? 'Unknown' }}</td>
                                <td>{{ $property->city }}, {{ $property->country }}</td>
                                <td>
                                    ${{ number_format($property->price) }}
                                    @if(($property->listing_type ?? null) === 'rent')
                                        /{{ $property->price_duration ?? 'month' }}
                                    @endif
                                </td>
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

        <div id="transactions-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-handshake"></i>
                    Transactions
                </h2>
            </div>
            <p class="dashboard-lead dashboard-lead--compact">Counts across all deals on the platform. Open individual deals from user profiles or property flows as needed.</p>
            <section class="admin-overview-group admin-section-stats" aria-labelledby="section-transactions-stats-heading">
                <h4 class="admin-overview-group__title" id="section-transactions-stats-heading">
                    <i class="fas fa-chart-bar"></i> Status overview
                </h4>
                <div class="admin-overview-grid">
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-handshake"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['transactions_pending']) }}</span>
                        <span class="overview-tile__label">Pending</span>
                    </div>
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-bolt"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['transactions_active']) }}</span>
                        <span class="overview-tile__label">Confirmed</span>
                    </div>
                    <div class="overview-tile">
                        <span class="overview-tile__icon"><i class="fas fa-flag-checkered"></i></span>
                        <span class="overview-tile__value">{{ number_format($overview['transactions_completed']) }}</span>
                        <span class="overview-tile__label">Completed</span>
                    </div>
                </div>
            </section>

            <h3 class="dashboard-subtitle"><i class="fas fa-list"></i> All transactions</h3>
            <p class="dashboard-lead dashboard-lead--compact">Buyer or renter ↔ landlord for each property. Use <strong>Open</strong> for the full deal workspace (admin can view any transaction).</p>
            <div class="applications-table admin-transactions-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Buyer / renter</th>
                            <th>Landlord</th>
                            <th>Property</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactionsList as $txn)
                            @php
                                $buyer = $txn->user;
                                $landlord = $txn->property?->landlord;
                                $typeLabel = match ($txn->type) {
                                    'buy' => 'Buy',
                                    'rent' => 'Rent',
                                    'refund' => 'Refund',
                                    default => ucfirst((string) $txn->type),
                                };
                                $statusLabel = match ($txn->status) {
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'confirmed' => 'Confirmed',
                                    'completed' => 'Completed',
                                    'cancelled_by_buyer' => 'Cancelled (buyer)',
                                    'cancelled_by_seller' => 'Cancelled (seller)',
                                    'refunded' => 'Refunded',
                                    default => ucfirst(str_replace('_', ' ', (string) $txn->status)),
                                };
                            @endphp
                            <tr>
                                <td>{{ $txn->id }}</td>
                                <td>
                                    @if($buyer)
                                        <a href="{{ route('admin.users.show', $buyer) }}">{{ $buyer->name }}</a>
                                        <div class="admin-txn-sub">{{ $buyer->email }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($landlord)
                                        <a href="{{ route('admin.users.show', $landlord) }}">{{ $landlord->name }}</a>
                                        <div class="admin-txn-sub">{{ $landlord->email }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($txn->property)
                                        <span class="admin-txn-prop-title">{{ \Illuminate\Support\Str::limit($txn->property->title, 40) }}</span>
                                        <div class="admin-txn-sub">#{{ $txn->property->id }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="admin-txn-type admin-txn-type--{{ $txn->type }}">{{ $typeLabel }}</span></td>
                                <td><span class="admin-txn-amount">{{ $txn->currency ? $txn->currency.' ' : '$' }}{{ number_format((float) $txn->price, 2) }}</span></td>
                                <td><span class="txn-status txn-status--{{ $txn->status }}">{{ $statusLabel }}</span></td>
                                <td>
                                    @if($txn->paid)
                                        <span class="txn-paid-yes">Yes</span>
                                    @else
                                        <span class="txn-paid-no">No</span>
                                    @endif
                                </td>
                                <td>{{ $txn->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('transactions.show', $txn) }}" class="btn btn-info btn-sm admin-view-user-btn" target="_blank" rel="noopener noreferrer" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="empty-state-cell">
                                    <i class="fas fa-handshake"></i>
                                    No transactions yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactionsList->hasPages())
                <div class="pagination-wrapper">
                    {{ $transactionsList->fragment('transactions-section')->links() }}
                </div>
            @endif
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
            </main>
        </div>
    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        window.__ADMIN_CHART_DATA__ = @json($chartData);
    </script>
    <script src="{{ asset('js/admin/admin.js') }}"></script>
    @auth
    <script src="{{ asset('js/notifications.js') }}"></script>
    @endauth
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('collapsed');
        });

        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('mobile-open');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const d = window.__ADMIN_CHART_DATA__;
            if (!d || typeof Chart === 'undefined') return;

            const lineCtx = document.getElementById('adminChartActivity');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: d.labels,
                        datasets: [
                            {
                                label: 'New clients',
                                data: d.series.clients,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                fill: false,
                                tension: 0.35,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'New landlords',
                                data: d.series.landlords,
                                borderColor: '#059669',
                                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                                fill: false,
                                tension: 0.35,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'New listings',
                                data: d.series.properties,
                                borderColor: '#0891b2',
                                backgroundColor: 'rgba(8, 145, 178, 0.08)',
                                fill: false,
                                tension: 0.35,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Applications',
                                data: d.series.applications,
                                borderColor: '#d97706',
                                backgroundColor: 'rgba(217, 119, 6, 0.08)',
                                fill: false,
                                tension: 0.35,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 10, usePointStyle: true } },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(148, 163, 184, 0.25)' },
                            },
                            x: {
                                grid: { display: false },
                                ticks: { maxRotation: 45, minRotation: 0 },
                            },
                        },
                    },
                });
            }

            const donutCtx = document.getElementById('adminChartPropertyStatus');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: d.propertyStatus.labels,
                        datasets: [
                            {
                                data: d.propertyStatus.data,
                                backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } },
                        },
                    },
                });
            }

            const barCtx = document.getElementById('adminChartListingTypes');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: d.listingTypes.labels,
                        datasets: [
                            {
                                label: 'Approved listings',
                                data: d.listingTypes.data,
                                backgroundColor: ['#0ea5e9', '#8b5cf6'],
                                borderRadius: 6,
                                maxBarThickness: 48,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(148, 163, 184, 0.25)' },
                            },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }
        });
    </script>
</body>
</html>

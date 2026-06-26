@extends('layouts.app')
@section('title', 'Landlord Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/landlord/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/landlord-tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/transaction-request.css') }}">
@endpush

@section('content')
<div class="main-container">
<aside id="landlordSidebar" class="landlord-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" width="32px">
            <span>Ghorfa Landlord</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" title="Collapse / Expand">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">MENU</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#requests-section" class="nav-link">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Requests</span>
                        @if($stats['new_pending_requests'] > 0)
                            <span class="nav-badge section-badge" data-badge-section="requests">{{ $stats['new_pending_requests'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#active-section" class="nav-link">
                        <i class="fas fa-handshake"></i>
                        <span>Active Transactions</span>
                        @if($stats['new_active_transactions'] > 0)
                            <span class="nav-badge section-badge" data-badge-section="active">{{ $stats['new_active_transactions'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#published-section" class="nav-link">
                        <i class="fas fa-check-circle"></i>
                        <span>Published</span>
                        @if($stats['new_published'] > 0)
                            <span class="nav-badge section-badge" data-badge-section="published">{{ $stats['new_published'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pending-section" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <span>Pending</span>
                        @if($stats['new_pending_properties'] > 0)
                            <span class="nav-badge section-badge" data-badge-section="pending">{{ $stats['new_pending_properties'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#rejected-section" class="nav-link">
                        <i class="fas fa-times-circle"></i>
                        <span>Rejected</span>
                        @if($stats['new_rejected'] > 0)
                            <span class="nav-badge nav-badge-danger section-badge" data-badge-section="rejected">{{ $stats['new_rejected'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>

<div class="landlord-sidebar-backdrop" id="landlordSidebarBackdrop" aria-hidden="true"></div>

<!-- Main Content Area -->
<main class="dashboard-main">
    <button type="button" class="landlord-mobile-nav-toggle" id="landlordMobileNavToggle" aria-controls="landlordSidebar" aria-expanded="false" aria-label="Open dashboard menu">
        <i class="fas fa-bars" aria-hidden="true"></i>
        <span>Menu</span>
    </button>
    <!-- Header -->
    <header class="landlord-header">
        <div class="header-title">
            <h1>Landlord Dashboard</h1>
            <p>Manage your properties and track your listings</p>
            <p class="header-stat-subtle">
                <i class="fas fa-heart" aria-hidden="true"></i>
                <span>{{ $stats['total_likes'] }} total likes</span>
            </p>
        </div>
        <div class="header-character">
            <img src="{{ asset('images/character/tie.png') }}" alt="Dashboard" class="dashboard-character">
        </div>
    </header>

    <!-- Content Area -->
    <div class="landlord-content" data-mark-section-seen-url="{{ route('landlord.mark-section-seen') }}">
        @if(session('success'))
            <div class="landlord-flash landlord-flash--success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="landlord-flash landlord-flash--error" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->has('resubmit_notes'))
            <div class="landlord-flash landlord-flash--error" role="alert">{{ $errors->first('resubmit_notes') }}</div>
        @endif
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_properties'] }}</h3>
                    <p>Total Properties</p>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['active_listings'] }}</h3>
                    <p>Published Properties</p>
                </div>
            </div>

            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['pending_properties'] }}</h3>
                    <p>Pending Approval</p>
                </div>
            </div>

            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $activeTransactions->count() }}</h3>
                    <p>Active Transactions</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Sections -->

            <!-- Rental/Purchase Requests Section -->
            <div id="requests-section" class="content-section" data-section-name="requests">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-envelope-open-text"></i>
                        Rental/Purchase Requests
                        <span class="badge badge-info">{{ $transactionRequests->count() }}</span>
                    </h2>
                </div>
                <div class="landlord-table-toolbar" role="search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input
                        type="search"
                        class="landlord-table-search"
                        placeholder="Search requests..."
                        aria-label="Search rental/purchase requests"
                        data-table-search-input="requests"
                    >
                </div>
                <div class="requests-table-wrapper landlord-dashboard-table-panel">
                    <table class="requests-table" data-table-search-table="requests">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Property</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactionRequests as $transaction)
                            <tr>
                                <td>
                                    <div class="request-buyer-cell">
                                        @if($transaction->user->profile_image)
                                            <img src="{{ Storage::url($transaction->user->profile_image) }}" alt="{{ $transaction->user->name }}" class="buyer-avatar">
                                        @else
                                            <div class="buyer-avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $transaction->user->name }}</strong>
                                            <small>{{ $transaction->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $transaction->property->title }}</strong>
                                </td>
                                <td>
                                    @if($transaction->type === 'rent')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-calendar-check"></i> Rental
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-handshake"></i> Purchase
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-pending">Pending Review</span>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="request-actions">
                                        <button class="btn btn-sm btn-primary" onclick="openRequestDetails({{ $transaction->id }})">
                                            <i class="fas fa-eye"></i> Review
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <div class="dashboard-empty-state">
                                        <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No requests" class="empty-dashboard-character">
                                        <p class="text-muted">No rental or purchase requests at the moment.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            

            <!-- Active Transactions Section -->
            <div id="active-section" class="content-section" data-section-name="active">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-handshake"></i>
                        Active Transactions
                        <span class="badge badge-success">{{ $activeTransactions->count() }}</span>
                    </h2>
                </div>
                <div class="landlord-table-toolbar" role="search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input
                        type="search"
                        class="landlord-table-search"
                        placeholder="Search transactions..."
                        aria-label="Search active transactions"
                        data-table-search-input="active"
                    >
                </div>
                <div class="transactions-table-wrapper landlord-dashboard-table-panel">
                    <table class="transactions-table" data-table-search-table="active">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Property</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeTransactions as $transaction)
                            <tr>
                                <td>
                                    <div class="transaction-buyer-cell">
                                        @if($transaction->user->profile_image)
                                            <img src="{{ Storage::url($transaction->user->profile_image) }}" alt="{{ $transaction->user->name }}" class="buyer-avatar">
                                        @else
                                            <div class="buyer-avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <strong>{{ $transaction->user->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $transaction->property->title }}</td>
                                <td>
                                    @if($transaction->type === 'rent')
                                        <span class="badge badge-primary">Rental</span>
                                    @else
                                        <span class="badge badge-success">Purchase</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status === 'confirmed' && !$transaction->paid)
                                        <span class="status-badge status-confirmed">Awaiting Payment</span>
                                    @elseif($transaction->paid)
                                        <span class="status-badge status-paid">Payment Received</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($transaction->price, 2) }}</strong>
                                    <small class="text-muted">{{ $transaction->currency }}</small>
                                </td>
                                <td>
                                    <div class="transaction-actions">
                                        @if($transaction->status === 'confirmed' && !$transaction->paid)
                                            <button class="btn btn-sm btn-success" onclick="confirmPaymentModal({{ $transaction->id }})">
                                                <i class="fas fa-check"></i> Confirm Payment
                                            </button>
                                        @elseif($transaction->paid)
                                            <button class="btn btn-sm btn-primary" onclick="completeTransactionModal({{ $transaction->id }})">
                                                <i class="fas fa-flag-checkered"></i> Complete
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="requestRefundModal({{ $transaction->id }})">
                                                <i class="fas fa-undo"></i> Refund
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <div class="dashboard-empty-state">
                                        <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No transactions" class="empty-dashboard-character">
                                        <p class="text-muted">No active transactions at the moment.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            

        <!-- Published Properties Section -->
        
        <div id="published-section" class="content-section" data-section-name="published">
            <div class="section-header">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Published Properties
                    <span class="badge badge-success">{{ $approvedProperties->count() }}</span>
                </h2>
            </div>
            <div class="landlord-table-toolbar" role="search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input
                    type="search"
                    class="landlord-table-search"
                    placeholder="Search published properties..."
                    aria-label="Search published properties"
                    data-table-search-input="published"
                >
            </div>
            <div class="properties-table-wrapper landlord-dashboard-table-panel">
                <table class="properties-table" data-table-search-table="published">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr>
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">
                                    ${{ number_format($property->price) }}
                                    @if(($property->listing_type ?? null) === 'rent')
                                        /{{ $property->price_duration ?? 'month' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-approved">Published</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <div class="dashboard-empty-state">
                                        <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No published properties" class="empty-dashboard-character">
                                        <p class="text-muted">You don't have any published properties yet. Once a listing is approved, it will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Properties Section (always present so #pending-section nav works) -->
        <div id="pending-section" class="content-section" data-section-name="pending">
            <div class="section-header">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Approval
                    <span class="badge badge-warning">{{ $pendingProperties->count() }}</span>
                </h2>
            </div>
            <div class="landlord-table-toolbar" role="search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input
                    type="search"
                    class="landlord-table-search"
                    placeholder="Search pending properties..."
                    aria-label="Search pending properties"
                    data-table-search-input="pending"
                >
            </div>
            <div class="properties-table-wrapper landlord-dashboard-table-panel">
                <table class="properties-table" data-table-search-table="pending">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr class="table-row-pending">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">
                                    ${{ number_format($property->price) }}
                                    @if(($property->listing_type ?? null) === 'rent')
                                        /{{ $property->price_duration ?? 'month' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-pending">Pending</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <div class="dashboard-empty-state">
                                        <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No pending properties" class="empty-dashboard-character">
                                        <p class="text-muted">You don't have any listings waiting for approval right now.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejected Properties Section -->
        <div id="rejected-section" class="content-section" data-section-name="rejected">
            <div class="section-header">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Rejected Properties
                    <span class="badge badge-danger">{{ $rejectedProperties->count() }}</span>
                </h2>
            </div>
            <div class="landlord-table-toolbar" role="search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input
                    type="search"
                    class="landlord-table-search"
                    placeholder="Search rejected properties..."
                    aria-label="Search rejected properties"
                    data-table-search-input="rejected"
                >
            </div>
            <div class="properties-table-wrapper landlord-dashboard-table-panel">
                <table class="properties-table" data-table-search-table="rejected">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr class="table-row-rejected">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">
                                    ${{ number_format($property->price) }}
                                    @if(($property->listing_type ?? null) === 'rent')
                                        /{{ $property->price_duration ?? 'month' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-rejected">Rejected</span>
                                </td>
                                <td>
                                    <div class="table-actions table-actions--rejected">
                                        <button type="button" class="btn-icon btn-icon--primary" title="Rejection details" onclick="openRejectionDetails({{ $property->id }})">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit listing">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <div class="dashboard-empty-state">
                                        <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No rejected properties" class="empty-dashboard-character">
                                        <p class="text-muted">You don't have any rejected properties. Listings that are not approved will show up here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        @if($approvedProperties->count() == 0 && $pendingProperties->count() == 0 && $rejectedProperties->count() == 0)
        <div class="content-section">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>No Properties Yet</h3>
                <p>You haven't listed any properties yet. Start by adding your first property!</p>
                <a href="{{ route('list-property') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> List Your First Property
                </a>
            </div>
        </div>
        @endif
            </div>
        </div>
    </div>
</main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    const sections = document.querySelectorAll('.content-section');

    function scrollSectionIntoView(sectionEl) {
        if (!sectionEl) return;
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function showSection(sectionId, markAsSeen) {
        sections.forEach(s => {
            if (s.id === sectionId) {
                s.style.display = '';
                s.classList.add('active');
            } else {
                s.style.display = 'none';
                s.classList.remove('active');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href') || '';
            if (href === '#' + sectionId) {
                link.classList.add('active');
            }
        });

        if (history.replaceState) {
            history.replaceState(null, null, '#' + sectionId);
        } else {
            window.location.hash = sectionId;
        }

        if (markAsSeen) {
            const targetSection = document.getElementById(sectionId);
            const sectionName = targetSection && targetSection.getAttribute('data-section-name');
            if (sectionName) markSectionSeen(sectionName);
        }
    }

    function markSectionSeen(sectionName) {
        const url = document.querySelector('[data-mark-section-seen-url]') && document.querySelector('[data-mark-section-seen-url]').dataset.markSectionSeenUrl;
        if (!url) return;
        const token = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
        if (!token) return;
        const formData = new FormData();
        formData.append('_token', token);
        formData.append('section', sectionName);
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: formData
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) {
                const badge = document.querySelector('.section-badge[data-badge-section="' + sectionName + '"]');
                if (badge) badge.style.display = 'none';
            }
        }).catch(function() {});
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    showSection(targetId, true);
                    scrollSectionIntoView(targetSection);
                    if (window.matchMedia('(max-width: 640px)').matches) {
                        window.setTimeout(function () {
                            scrollSectionIntoView(targetSection);
                        }, 380);
                    }
                }
            }
        });
    });

    (function initVisibleSection() {
        let initial = window.location.hash ? window.location.hash.substring(1) : null;
        if (!initial) {
            for (const link of navLinks) {
                const href = link.getAttribute('href') || '';
                if (href.startsWith('#')) {
                    initial = href.substring(1);
                    break;
                }
            }
        }

        const sectionExists = initial && document.getElementById(initial);
        if (sectionExists) {
            showSection(initial, false);
            if (window.location.hash) {
                scrollSectionIntoView(document.getElementById(initial));
            }
        } else if (sections.length > 0) {
            showSection(sections[0].id, false);
        }
    })();

    // Transaction action modals
    function setLandlordModalOpen(open) {
        document.body.classList.toggle('landlord-modal-open', open);
    }

    function openRequestDetails(transactionId) {
        var block = document.getElementById('request-details-block-' + transactionId);
        var modal = document.getElementById('requestDetailsModal');
        if (!modal) return;
        if (block) {
            document.getElementById('requestDetailsContent').innerHTML = block.innerHTML;
        }
        modal.style.display = 'flex';
        setLandlordModalOpen(true);
    }

    function closeRequestDetailsModal() {
        var modal = document.getElementById('requestDetailsModal');
        if (!modal) return;
        modal.style.display = 'none';
        setLandlordModalOpen(false);
    }

    function openRejectionDetails(propertyId) {
        var block = document.getElementById('rejection-details-block-' + propertyId);
        var mount = document.getElementById('rejectionDetailsContent');
        var modal = document.getElementById('rejectionDetailsModal');
        if (block && mount && modal) {
            mount.innerHTML = block.innerHTML;
            modal.style.display = 'flex';
            setLandlordModalOpen(true);
        }
    }

    function closeRejectionDetailsModal() {
        var modal = document.getElementById('rejectionDetailsModal');
        if (!modal) return;
        modal.style.display = 'none';
        setLandlordModalOpen(false);
    }

    function confirmPaymentModal(transactionId) {
        if (confirm('Are you sure you have received the payment from the buyer?')) {
            confirmPayment(transactionId);
        }
    }

    function completeTransactionModal(transactionId) {
        if (confirm('Mark this transaction as completed?')) {
            completeTransaction(transactionId);
        }
    }

    function requestRefundModal(transactionId) {
        const reason = prompt('Enter reason for refund:');
        if (reason !== null) {
            requestRefund(transactionId, reason);
        }
    }

    function confirmPayment(transactionId) {
        fetch(`/transactions/${transactionId}/confirm-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Payment confirmed successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function completeTransaction(transactionId) {
        fetch(`/transactions/${transactionId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Transaction completed successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function requestRefund(transactionId, reason) {
        fetch(`/transactions/${transactionId}/request-refund`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            alert('Refund requested successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function generateAndSendContract(transactionId) {
        if (!confirm('Generate the contract and send it to the buyer?')) {
            return;
        }

        const contractPath = prompt('Enter contract file path or leave empty for the default:');
        if (contractPath === null) {
            return;
        }

        fetch(`/transactions/${transactionId}/generate-contract`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ contract_path: contractPath || `contracts/transaction_${transactionId}.pdf` })
            })
            .then(response => response.json())
            .then(data => {
                alert('Contract generated and sent to buyer!');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating the contract.');
            });
    }

    function downloadTransactionReport(transactionId) {
        window.open(`/transactions/${transactionId}/download-report`, '_blank');
    }

    function exportRequestDetailsPdf(transactionId) {
        var url = '/landlord/transactions/' + transactionId + '/export-pdf';
        fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/pdf' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Export failed');
                }
                var disposition = response.headers.get('Content-Disposition') || '';
                var match = disposition.match(/filename[^;=\n]*=(?:UTF-8''|")?([^";\n]+)/i);
                var filename = match ? match[1].replace(/"/g, '') : ('request-' + transactionId + '.pdf');
                return response.blob().then(function (blob) {
                    return { blob: blob, filename: filename };
                });
            })
            .then(function (file) {
                var link = document.createElement('a');
                link.href = URL.createObjectURL(file.blob);
                link.download = file.filename;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                setTimeout(function () {
                    URL.revokeObjectURL(link.href);
                    link.remove();
                }, 100);
            })
            .catch(function () {
                alert('Could not export the PDF. Please try again.');
            });
    }
    // expose functions to global scope so inline `onclick` handlers work
    window.openRequestDetails = openRequestDetails;
    window.closeRequestDetailsModal = closeRequestDetailsModal;
    window.confirmPaymentModal = confirmPaymentModal;
    window.completeTransactionModal = completeTransactionModal;
    window.requestRefundModal = requestRefundModal;
    window.confirmPayment = confirmPayment;
    window.completeTransaction = completeTransaction;
    window.requestRefund = requestRefund;
    window.generateAndSendContract = generateAndSendContract;
    window.downloadTransactionReport = downloadTransactionReport;
    window.exportRequestDetailsPdf = exportRequestDetailsPdf;
    window.openRejectionDetails = openRejectionDetails;
    window.closeRejectionDetailsModal = closeRejectionDetailsModal;

    @if($errors->has('resubmit_notes') && session('resubmit_failed_property_id'))
    (function () {
        var rid = {{ (int) session('resubmit_failed_property_id') }};
        if (rid) {
            showSection('rejected-section', false);
            openRejectionDetails(rid);
        }
    })();
    @endif

    window.addEventListener('keydown', function(e){
        if(e.key === 'Escape') {
            closeRequestDetailsModal();
            closeRejectionDetailsModal();
        }
    });

    // Table search (client-side filtering)
    function setupDashboardTableSearch(key) {
        const input = document.querySelector('[data-table-search-input="' + key + '"]');
        const table = document.querySelector('table[data-table-search-table="' + key + '"]');
        if (!input || !table) return;

        const rows = table.querySelectorAll('tbody tr');
        if (!rows || rows.length === 0) return;

        function getEmptyRow() {
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].querySelector('.empty-state-cell')) return rows[i];
            }
            return null;
        }

        const emptyRow = getEmptyRow();

        input.addEventListener('input', function() {
            const q = (input.value || '').toLowerCase().trim();

            if (!q) {
                rows.forEach(tr => { tr.style.display = ''; });
                return;
            }

            let anyVisible = false;
            rows.forEach(tr => {
                const hasEmptyCell = !!tr.querySelector('.empty-state-cell');
                if (hasEmptyCell) {
                    tr.style.display = 'none';
                    return;
                }

                const rowText = (tr.textContent || '').toLowerCase();
                const matches = rowText.indexOf(q) !== -1;
                tr.style.display = matches ? '' : 'none';
                if (matches) anyVisible = true;
            });

            if (emptyRow) {
                emptyRow.style.display = anyVisible ? 'none' : '';
            }
        });
    }

    ['requests', 'active', 'published', 'pending', 'rejected'].forEach(setupDashboardTableSearch);
});
</script>

<script>
// Sidebar: desktop collapse + phone drawer
document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('landlordSidebar');
  const toggle = document.getElementById('sidebarToggle');
  const container = document.querySelector('.main-container');
  const mobileToggle = document.getElementById('landlordMobileNavToggle');
  const backdrop = document.getElementById('landlordSidebarBackdrop');
  if (!sidebar || !toggle || !container) return;

  const mqMobile = window.matchMedia('(max-width: 640px)');

  function isMobileNav() {
    return mqMobile.matches;
  }

  function applyDesktopCollapsedFromStorage() {
    const collapsed = localStorage.getItem('landlordSidebarCollapsed') === '1';
    sidebar.classList.toggle('collapsed', collapsed);
    container.classList.toggle('sidebar-collapsed', collapsed);
  }

  function syncSidebarForViewport() {
    if (isMobileNav()) {
      sidebar.classList.remove('collapsed');
      container.classList.remove('sidebar-collapsed');
    } else {
      applyDesktopCollapsedFromStorage();
    }
  }

  function setDrawerOpen(open) {
    container.classList.toggle('nav-drawer-open', open);
    document.body.classList.toggle('landlord-drawer-open', open);
    if (backdrop) {
      backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    if (mobileToggle) {
      mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      mobileToggle.setAttribute('aria-label', open ? 'Close dashboard menu' : 'Open dashboard menu');
    }
  }

  function closeMobileDrawer() {
    setDrawerOpen(false);
  }

  function toggleMobileDrawer() {
    setDrawerOpen(!container.classList.contains('nav-drawer-open'));
  }

  syncSidebarForViewport();

  toggle.addEventListener('click', function (e) {
    if (isMobileNav()) {
      e.preventDefault();
      toggleMobileDrawer();
      return;
    }
    sidebar.classList.toggle('collapsed');
    container.classList.toggle('sidebar-collapsed');
    localStorage.setItem('landlordSidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
  });

  mobileToggle?.addEventListener('click', function () {
    toggleMobileDrawer();
  });

  backdrop?.addEventListener('click', closeMobileDrawer);

  sidebar.querySelectorAll('.nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (isMobileNav()) {
        window.setTimeout(closeMobileDrawer, 320);
      }
    });
  });

  mqMobile.addEventListener('change', function () {
    closeMobileDrawer();
    syncSidebarForViewport();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && container.classList.contains('nav-drawer-open')) {
      closeMobileDrawer();
    }
  });
});
</script>

{{-- Pre-rendered request details (one per transaction), hidden; JS copies into modal on open --}}
@foreach($transactionRequests as $transaction)
<div id="request-details-block-{{ $transaction->id }}" class="request-details-block" aria-hidden="true">
    @include('landlord.partials.request-details-content', ['transaction' => $transaction])
</div>
@endforeach
@foreach($activeTransactions as $transaction)
@if(!$transactionRequests->contains('id', $transaction->id))
<div id="request-details-block-{{ $transaction->id }}" class="request-details-block" aria-hidden="true">
    @include('landlord.partials.request-details-content', ['transaction' => $transaction])
</div>
@endif
@endforeach

<!-- Request Details Modal -->
<div
    id="requestDetailsModal"
    class="modal-overlay landlord-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="requestDetailsModalTitle"
    onclick="if(event.target === this) closeRequestDetailsModal()"
>
    <div class="landlord-modal__dialog modal-content modal-content--request" role="document" onclick="event.stopPropagation()">
        <header class="modal-header landlord-modal__header">
            <div class="landlord-modal__header-text">
                <h2 id="requestDetailsModalTitle">Request Details</h2>
                <p class="landlord-modal__subtitle">Review buyer and transaction information</p>
            </div>
            <button type="button" class="modal-close landlord-modal__close" onclick="closeRequestDetailsModal()" aria-label="Close request details">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </header>
        <div id="requestDetailsContent" class="landlord-modal__body"></div>
    </div>
</div>

@foreach($rejectedProperties as $property)
<div id="rejection-details-block-{{ $property->id }}" class="rejection-details-block" aria-hidden="true">
    @include('landlord.partials.rejected-property-details', ['property' => $property])
</div>
@endforeach

<!-- Rejected property: details + resubmit -->
<div
    id="rejectionDetailsModal"
    class="modal-overlay landlord-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="rejectionDetailsModalTitle"
    onclick="if(event.target === this) closeRejectionDetailsModal()"
>
    <div class="landlord-modal__dialog modal-content modal-content--rejection" role="document" onclick="event.stopPropagation()">
        <header class="modal-header landlord-modal__header">
            <div class="landlord-modal__header-text">
                <h2 id="rejectionDetailsModalTitle">Rejected listing</h2>
                <p class="landlord-modal__subtitle">Review feedback and resubmit your property</p>
            </div>
            <button type="button" class="modal-close landlord-modal__close" onclick="closeRejectionDetailsModal()" aria-label="Close rejected listing details">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </header>
        <div id="rejectionDetailsContent" class="landlord-modal__body"></div>
    </div>
</div>

@endpush


@extends('layouts.app')
@section('title', 'Landlord Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landlord-dashboard.css') }}">
@endpush

@section('content')
<div class="landlord-dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Landlord Dashboard</h1>
        <p>Manage your properties and track your listings</p>
    </div>

    <div class="dashboard-layout">
        <!-- Left Sidebar Navigation -->
        <aside class="landlord-sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('list-property') }}" class="nav-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Property</span>
                </a>
                <a href="#published-section" class="nav-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Published Properties</span>
                    @if($approvedProperties->count() > 0)
                        <span class="nav-badge">{{ $approvedProperties->count() }}</span>
                    @endif
                </a>
                <a href="#pending-section" class="nav-item">
                    <i class="fas fa-clock"></i>
                    <span>Pending Approval</span>
                    @if($pendingProperties->count() > 0)
                        <span class="nav-badge">{{ $pendingProperties->count() }}</span>
                    @endif
                </a>
                @if($rejectedProperties->count() > 0)
                <a href="#rejected-section" class="nav-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Rejected Properties</span>
                    <span class="nav-badge nav-badge-danger">{{ $rejectedProperties->count() }}</span>
                </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="dashboard-main">
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
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['total_likes'] }}</h3>
                        <p>Total Likes</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Sections -->
            <div class="dashboard-content">
        
        <!-- Published Properties Section -->
        @if($approvedProperties->count() > 0)
        <div id="published-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Published Properties
                    <span class="badge badge-success">{{ $approvedProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
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
                        @foreach($approvedProperties as $property)
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
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Pending Properties Section -->
        @if($pendingProperties->count() > 0)
        <div id="pending-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Approval
                    <span class="badge badge-warning">{{ $pendingProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
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
                        @foreach($pendingProperties as $property)
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
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Rejected Properties Section -->
        @if($rejectedProperties->count() > 0)
        <div id="rejected-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Rejected Properties
                    <span class="badge badge-danger">{{ $rejectedProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
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
                        @foreach($rejectedProperties as $property)
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
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
                                <td>
                                    <span class="status-badge status-rejected">Rejected</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit & Resubmit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

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
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.content-section');
    
    // Handle click on nav items
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Update active state
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                }
            }
        });
    });

    // Update active nav item on scroll
    function updateActiveNav() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });

        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === '#' + current) {
                item.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav(); // Initial check
});
</script>
@endpush


@extends('layouts.app')
@section('title', 'Landlord Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/landlord-dashboard.css') }}">
@endpush

@section('content')
<main>
    <div class="profile-container">
        <div class="profile-header" style="margin-bottom: 2rem;">
            <h1>Landlord Dashboard</h1>
            <p>Manage your properties and track your listings</p>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2rem; margin: 0; color: #2563eb;">{{ $stats['total_properties'] }}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280;">Total Properties</p>
            </div>
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2rem; margin: 0; color: #10b981;">{{ $stats['active_listings'] }}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280;">Active Listings</p>
            </div>
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2rem; margin: 0; color: #f59e0b;">{{ $stats['total_likes'] }}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280;">Total Likes</p>
            </div>
        </div>

        <div class="profile-card profile-card-shadow">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>My Properties</h2>
                <a href="{{ route('list-property') }}" class="btn btn-primary" style="text-decoration: none; padding: 0.5rem 1rem;">+ Add Property</a>
            </div>

            @if($properties->count() > 0)
                <div class="properties-list">
                    @foreach($properties as $property)
                        <div class="property-item" style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e5e7eb; align-items: center;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 0.5rem 0;">
                                    <a href="{{ route('properties.show', $property->id) }}" style="text-decoration: none; color: #1f2937;">
                                        {{ $property->title }}
                                    </a>
                                </h3>
                                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">
                                    {{ $property->address }}, {{ $property->city }}, {{ $property->country }}
                                </p>
                                <p style="margin: 0.5rem 0 0 0; color: #2563eb; font-weight: 600;">
                                    ${{ number_format($property->price) }}/month
                                </p>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-secondary" style="text-decoration: none; padding: 0.5rem 1rem;">Edit</a>
                                <a href="{{ route('properties.show', $property->id) }}" class="btn btn-primary" style="text-decoration: none; padding: 0.5rem 1rem;">View</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 1.5rem;">
                    {{ $properties->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem;">
                    <p style="color: #6b7280; margin-bottom: 1rem;">You haven't listed any properties yet.</p>
                    <a href="{{ route('list-property') }}" class="btn btn-primary" style="text-decoration: none; padding: 0.75rem 1.5rem;">List Your First Property</a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection


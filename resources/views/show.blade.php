@extends('layouts.app')
@section('title', $property->title)
@section('content')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">

<div class="property-container">
    <h1>{{ $property->title }}</h1>
    <div class="property-information">
        <strong>Type:</strong> {{ $property->property_type }}<br>
        <strong>Listing:</strong> {{ $property->listing_type }}<br>
        <strong>Price:</strong> ${{ number_format($property->price, 2) }}<br>
        <strong>Address:</strong> {{ $property->address }}, {{ $property->city }}, {{ $property->country }}<br>
        <strong>Area:</strong> {{ $property->area_m3 }} m²<br>
        <strong>Rooms:</strong> {{ $property->room_nb }}<br>
        <strong>Bedrooms:</strong> {{ $property->bedroom_nb }}<br>
        <strong>Bathrooms:</strong> {{ $property->bathroom_nb }}<br>
        <strong>Description:</strong> {{ $property->description }}
        <p>Posted {{ $property->created_at->diffForHumans() }}</p>
    </div>
    <div class="property-images-grid">
        @foreach($property->images as $image)
            <img src="{{ Storage::url($image->path) }}" class="property-image" alt="Property Image">
        @endforeach
    </div>
    
    <div class="property-actions">
        @auth
            <button onclick="history.back()" class="back-button">Back</button>
            @if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id)
                <a href="{{ route('properties.edit', $property) }}" class="edit-btn">Edit Property</a>
                <form class="delete-form" action="{{ route('properties.destroy', $property->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">Delete Property</button>
                </form>
            @endif
        @endauth
        @guest
            <button onclick="history.back()" class="back-button">Back</button>
        @endguest
    </div>
</div>

@endsection 
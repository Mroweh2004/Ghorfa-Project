@extends('layouts/app')

@section('content')
<script src="{{asset('js/search.js')}}"></script>
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<main>
@auth
<div class="listing">
    <div class="listings-grid">
        @foreach($properties as $property)
            <div class="listing-card">
                <div class="listing-image">
                    <img src="{{ $property->images->first() ? Storage::url($property->images->first()->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267' }}" alt="{{ $property->title }}">
                    <span class="listing-tag">For {{ $property->listing_type }}</span>
                    @if($property->user_id === Auth::user()->id)
                        <button class="setting-btn"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                        <ul class="setting-list">
                            <li><a href="{{route('properties.show', $property->id) }}">View</a></li>
                            <li><a href="{{ route('properties.edit', $property->id) }}">Edit</a></li>
                            <li>
                                <form action="{{ route('properties.destroy', $property->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">Delete</button>
                                </form>
                            </li>
                        </ul>
                    @endif
                    <button class="favorite-btn"><i class="fa-regular fa-heart"></i></button>
                </div>
                <div class="listing-content">
                    <div class="listing-price">{{ $property->price }}$/month</div>
                    <h3>{{ $property->title }}</h3>
                    <p class="listing-location">
                        <i class="fas fa-map-marker-alt"></i> 
                        {{ $property->address }}, {{ $property->city }}, {{ $property->country }}
                    </p>
                    <div class="listing-features">
                        <span><i class="fas fa-home"></i> {{ $property->property_type }}</span>
                        @if($property->room_nb)
                            <span><i class="fa-solid fa-person-shelter"></i> {{ $property->room_nb }} Room</span>
                        @endif
                        @if($property->bedroom_nb)
                            <span><i class="fas fa-bed"></i> {{ $property->bedroom_nb }} Bedrooms</span>
                        @endif
                        @if($property->bathroom_nb)
                            <span><i class="fas fa-bath"></i> {{ $property->bathroom_nb }} Bathrooms</span>
                        @endif
                        @if($property->area_m3)
                            <span><i class="fas fa-ruler-combined"></i> {{ $property->area_m3 }}m²</span>
                        @endif
                    </div>
                </div>
                <div class="listing-meta">
                        <span class="available-from">Listed {{ $property->created_at->diffForHumans() }}</span>
                        <a href="{{ route('properties.show', $property->id) }}" class="btn-secondary">View Details</a>
                </div>
                    
            </div>
        @endforeach
    </div>
        <div class="pagination">
            @if ($properties->hasPages())
                @if ($properties->onFirstPage())
                    <button class="pagination-btn" disabled>Previous</button>
                @else
                    <a href="{{ $properties->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                    @if ($page == $properties->currentPage())
                        <button class="pagination-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($properties->hasMorePages())
                    <a href="{{ $properties->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                    <button class="pagination-btn" disabled>Next</button>
                @endif
            @endif
        </div>
    </div>
    </div>
</main>

@endauth
@guest
<h1 style="color:red;">Please login first!</h1>
@endguest
@endsection

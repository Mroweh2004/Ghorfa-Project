@extends('layouts.app')
@section('title', 'Search')
@section('content')
<link rel="stylesheet" href="{{asset('css/search.css')}}">
<script src="{{asset('js/search.js')}}"></script>
<body>
    <main class="search-page">
        <section class="search-filters">
            <div class="filter-container">
                <form action="{{ route('filter-search') }}" method="GET">
                <div class="filter-group">
                    <h3>Location</h3>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" id="location" name="location" placeholder="Enter city or area..." value="{{ request('location') }}">
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Price Range</h3>
                    <div class="price-range">
                        <div class="search-input">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="number" id="min-price" name="min-price" placeholder="Min" min="0" value="{{ request('min-price') }}" oninput="validatePriceRange()">
                        </div>
                        <span>-</span>
                        <div class="search-input">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="number" id="max-price" name="max-price" placeholder="Max" min="0" value="{{ request('max-price') }}" oninput="validatePriceRange()">
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Property Type</h3>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="property_type[]" value="apartment" {{ is_array(request('property_type')) && in_array('apartment', request('property_type')) ? 'checked' : '' }}> Appartment
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="house" {{ is_array(request('property_type')) && in_array('house', request('property_type')) ? 'checked' : '' }}> House
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="dorm" {{ is_array(request('property_type')) && in_array('dorm', request('property_type')) ? 'checked' : '' }}> Dorm
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="other" {{ is_array(request('property_type')) && in_array('other', request('property_type')) ? 'checked' : '' }}> Other
                        </label>
                    </div>
                </div>
                <div class="filter-group">
                    <h3>Listing Type</h3>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="listing_type[]" value="Rent" {{ is_array(request('listing_type')) && in_array('Rent', request('listing_type')) ? 'checked' : '' }}> For Rent
                        </label>
                        <label>
                            <input type="checkbox" name="listing_type[]" value="Sale" {{ is_array(request('listing_type')) && in_array('Sale', request('listing_type')) ? 'checked' : '' }}> For Sale
                        </label>
                    </div>
                </div>


                <div class="filter-group">
                    <h3>Amenities</h3>
                    <div class="checkbox-group">
                        @foreach ($amenities as $amenity )
                        <label>
                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ is_array(request('amenities')) && in_array($amenity->id, request('amenities')) ? 'checked' : '' }}> {{ $amenity->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Rules</h3>
                    <div class="checkbox-group">
                        @foreach ($rules as $rule )
                            <label>
                            <input type="checkbox" name="rules[]" value="{{ $rule->name }}" {{ is_array(request('rules')) && in_array($rule->name, request('rules')) ? 'checked' : '' }}> {{ $rule->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <button class="other-list-btn apply-filters">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </section>
        
        <section class="search-results">
            <button class="search-show-btn"><i class="fas fa-search"></i> Show Filters</button>
            <div class="results-header">
                <div class="results-count">
                    <h2>{{ $properties->total() }} Rooms Found</h2>
                    <p>in Lebanon</p>
                </div>
                <div class="results-sort">
                    <button class="filter-toggle-btn">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                    <select id="sort-options">
                        <option value="recommended">Recommended</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest</option>
                        <option value="latest">Latest</option>
                    </select>
                </div>
            </div>

            <div class="listings-grid">
                @foreach($properties as $property)
                <div class="listing-card" data-price="{{ $property->price }}" data-created="{{ $property->created_at->timestamp }}">
                    <div class="listing-image">
                        <img src="{{ $property->images->first() ? Storage::url($property->images->first()->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267' }}" alt="{{ $property->title }}">
                        <span class="listing-tag">For {{ $property->listing_type }}</span>
                        <button class="setting-btn"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                        <ul class="setting-list">
                            <li><a href="{{route('properties.show', $property->id) }}">View</a></li>
                            @if(auth()->check())
                                @if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id)
                                    <li><a href="{{ route('properties.edit', $property->id) }}">Edit</a></li>
                                    <li>
                                        <form action="{{ route('properties.destroy', $property->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">Delete</button>
                                        </form>
                                    </li>
                                @endif
                            @endif
                        </ul>
                        @auth
                            <button 
                                class="favorite-btn like-btn" 
                                data-property-id="{{ $property->id }}"
                                data-liked="{{ $property->isLikedBy(auth()->id()) ? 'true' : 'false' }}"
                            >
                                <i class="fa-{{ $property->isLikedBy(auth()->id()) ? 'solid' : 'regular' }} fa-heart"></i>
                            </button>
                            <span class="like-count" id="like-count-{{ $property->id }}" style="display: none;">{{ $property->likedBy()->count() }}</span>
                        @else
                            <button class="favorite-btn" onclick="window.location.href='{{ route('login') }}'">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        @endauth
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
                            <span><i class="fa-solid fa-person-shelter"></i> {{ $property->room_nb }} Room</span>
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
                            <a href="{{ route('properties.show', $property->id) }}" class="view-btn">View Details</a>
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
        </section>
    </main>
@endsection
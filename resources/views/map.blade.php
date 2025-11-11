@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/map.css') }}">

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Property Map</h1>
        <p class="text-gray-600">Explore properties on an interactive map</p>
    </div>

    <!-- Map Statistics -->
    <div class="map-stats">
        <div class="stats-number">{{ $properties->count() }}</div>
        <div class="stats-label">Properties Found</div>
    </div>

    <!-- Search Filters -->
    <div class="map-filters">
        <form method="GET" action="{{ route('map') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="{{ $request->input('location') }}" placeholder="City, Country, or Address">
                </div>
                
                <div class="filter-group">
                    <label for="min_price">Min Price</label>
                    <input type="number" id="min_price" name="min_price" value="{{ $request->input('min_price') }}" placeholder="Minimum price">
                </div>
                
                <div class="filter-group">
                    <label for="max_price">Max Price</label>
                    <input type="number" id="max_price" name="max_price" value="{{ $request->input('max_price') }}" placeholder="Maximum price">
                </div>
                
                <div class="filter-group">
                    <label for="property_type">Property Type</label>
                    <select id="property_type" name="property_type[]" multiple>
                        <option value="apartment" {{ in_array('apartment', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Apartment</option>
                        <option value="house" {{ in_array('house', (array)$request->input('property_type', [])) ? 'selected' : '' }}>House</option>
                        <option value="villa" {{ in_array('villa', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Villa</option>
                        <option value="studio" {{ in_array('studio', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Studio</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="listing_type">Listing Type</label>
                    <select id="listing_type" name="listing_type[]" multiple>
                        <option value="sale" {{ in_array('sale', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For Sale</option>
                        <option value="rent" {{ in_array('rent', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For Rent</option>
                    </select>
                </div>
            </div>
            
            <div class="filter-row">
                <button type="submit" class="btn-filter">Apply Filters</button>
                <button type="button" class="btn-clear" onclick="clearFilters()">Clear All</button>
            </div>
        </form>
    </div>

    <div class="map-container">
        <div id="map"></div>
    </div>
</div>

<script src="{{ asset('js/MapClickService.js') }}"></script>
<script src="{{ asset('js/map.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initMap&libraries=places"></script>

<script>
    initializeProperties(@json($properties));
    
    window.mapConfig = {
        reverseGeocodeEndpoint: '{{ route("map.reverse-geocode") }}',
        storageUrl: '{{ asset("storage") }}',
        placeholderUrl: '{{ asset("img/background.jpg") }}',
        mapRoute: '{{ route("map") }}'
    };

    setTimeout(function() {
        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API failed to load');
            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Google Maps API failed to load. Please check your API key and internet connection.</div>';
            }
        }
    }, 5000);
</script>
@endsection
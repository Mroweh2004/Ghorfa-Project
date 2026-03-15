@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/map.css') }}">
@endpush

@section('content')
<div class="map-page-wrapper">
  <div class="map-container-main">
    <!-- Enhanced Header with Character -->
    <div class="map-hero-section">
      <div class="map-hero-content">
        <div class="map-header-content">
          <span class="map-badge">
            <i class="fas fa-map-marked-alt"></i> Interactive Map
          </span>
          <h1 class="map-title">Property Map</h1>
          <p class="map-subtitle">Explore properties on an interactive map and find your perfect location</p>
        </div>
        <div class="map-character-container">
          <img src="{{ asset('images/character/map-navigating.png') }}" alt="Navigate!" class="map-character-image">
        </div>
      </div>

      <!-- Enhanced Statistics Card -->
      <div class="map-stats-enhanced">
        <div class="stat-item">
          <div class="stat-icon">
            <i class="fas fa-home"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ $properties->count() }}</div>
            <div class="stat-label">Properties Found</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Map Layout: Enhanced Filters on Left, Map on Right -->
    <div class="map-layout-enhanced">
      <!-- Enhanced Search Filters - Left Side -->
      <div class="map-filters-enhanced">
        <div class="filters-header">
          <h3><i class="fas fa-filter"></i> Filter Properties</h3>
          <p>Refine your search</p>
        </div>

        <form method="GET" action="{{ route('map') }}" id="filterForm">
          <div class="filter-group-enhanced">
            <label for="location">
              <i class="fas fa-map-marker-alt"></i> Location
            </label>
            <div class="input-with-icon">
              <input type="text" id="location" name="location" value="{{ $request->input('location') }}" placeholder="City, Country, or Address">
              <i class="fas fa-search input-icon"></i>
            </div>
          </div>
          
          <div class="filter-row">
            <div class="filter-group-enhanced">
              <label for="min_price">
                <i class="fas fa-dollar-sign"></i> Min Price
              </label>
              <input type="number" id="min_price" name="min_price" value="{{ $request->input('min_price') }}" placeholder="Min">
            </div>
            
            <div class="filter-group-enhanced">
              <label for="max_price">
                <i class="fas fa-dollar-sign"></i> Max Price
              </label>
              <input type="number" id="max_price" name="max_price" value="{{ $request->input('max_price') }}" placeholder="Max">
            </div>
          </div>
          
          <div class="filter-group-enhanced">
            <label for="property_type">
              <i class="fas fa-building"></i> Property Type
            </label>
            <select id="property_type" name="property_type[]" multiple>
              <option value="apartment" {{ in_array('apartment', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Apartment</option>
              <option value="house" {{ in_array('house', (array)$request->input('property_type', [])) ? 'selected' : '' }}>House</option>
              <option value="villa" {{ in_array('villa', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Villa</option>
              <option value="studio" {{ in_array('studio', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Studio</option>
              <option value="dorm" {{ in_array('dorm', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Dorm</option>
            </select>
            <small class="filter-hint">Hold Ctrl/Cmd to select multiple</small>
          </div>
          
          <div class="filter-group-enhanced">
            <label for="listing_type">
              <i class="fas fa-tag"></i> Listing Type
            </label>
            <select id="listing_type" name="listing_type[]" multiple>
              <option value="sale" {{ in_array('sale', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For Sale</option>
              <option value="rent" {{ in_array('rent', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For Rent</option>
            </select>
            <small class="filter-hint">Hold Ctrl/Cmd to select multiple</small>
          </div>
          
          <div class="filter-actions-enhanced">
            <button type="submit" class="btn-apply">
              <i class="fas fa-check"></i> Apply Filters
            </button>
            <button type="button" class="btn-reset" onclick="clearFilters()">
              <i class="fas fa-redo"></i> Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Enhanced Map Container - Right Side -->
      <div class="map-container-enhanced">
        <div id="map"></div>
        <div class="map-overlay-info">
          <i class="fas fa-info-circle"></i> Click on markers to view property details
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/MapClickService.js') }}"></script>
<script src="{{ asset('js/map.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initMap&libraries=places"></script>

<script>
    initializeProperties(@json($properties));
    
    window.mapConfig = {
        reverseGeocodeEndpoint: '{{ route("map.reverse-geocode") }}',
        storageUrl: '{{ asset("storage") }}',
        placeholderUrl: '{{ asset("img/no_image.jpg") }}',
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
@endpush
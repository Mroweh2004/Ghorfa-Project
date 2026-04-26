@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/map.css') }}">
@endpush

@section('content')
<main class="map-page" id="map-page">
  <div class="map-page-wrapper">
    <div class="map-container-main">
      <header class="map-hero-panel" aria-labelledby="map-page-title">
        <div class="map-hero-panel__intro">
          <span class="map-badge">
            <i class="fas fa-map-marked-alt" aria-hidden="true"></i> Interactive map
          </span>
          <h1 class="map-title" id="map-page-title">Property map</h1>
          <p class="map-subtitle">Pan, zoom, and tap markers to explore listings that match your filters.</p>
        </div>
        <div class="map-hero-panel__illustration" aria-hidden="true">
          <img src="{{ asset('images/character/map-navigating.png') }}" alt="" class="map-character-image" width="200" height="200">
        </div>
        <div class="map-hero-panel__metrics" aria-label="Results summary">
          <div class="map-metric">
            <span class="map-metric__icon"><i class="fas fa-home" aria-hidden="true"></i></span>
            <div class="map-metric__text">
              <span class="map-metric__value">{{ $properties->count() }}</span>
              <span class="map-metric__label">Properties on this view</span>
            </div>
          </div>
        </div>
      </header>

      <div class="map-layout-enhanced">
        <aside class="map-filters-enhanced" aria-label="Map filters">
          <div class="filters-header">
            <h2 class="filters-heading"><i class="fas fa-filter" aria-hidden="true"></i> Filters</h2>
            <p class="filters-lead">Narrow results, then apply to refresh the map.</p>
          </div>

          <form method="GET" action="{{ route('map') }}" id="filterForm">
            <div class="filter-group-enhanced">
              <label for="location">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i> Location
              </label>
              <div class="input-with-icon">
                <input type="text" id="location" name="location" value="{{ $request->input('location') }}" placeholder="City, country, or address" autocomplete="off">
                <i class="fas fa-search input-icon" aria-hidden="true"></i>
              </div>
            </div>

            <div class="filter-row">
              <div class="filter-group-enhanced">
                <label for="min_price">
                  <i class="fas fa-dollar-sign" aria-hidden="true"></i> Min price
                </label>
                <input type="number" id="min_price" name="min_price" value="{{ $request->input('min_price') }}" placeholder="Min" min="0" step="1">
              </div>

              <div class="filter-group-enhanced">
                <label for="max_price">
                  <i class="fas fa-dollar-sign" aria-hidden="true"></i> Max price
                </label>
                <input type="number" id="max_price" name="max_price" value="{{ $request->input('max_price') }}" placeholder="Max" min="0" step="1">
              </div>
            </div>

            <div class="filter-group-enhanced">
              <label for="property_type">
                <i class="fas fa-building" aria-hidden="true"></i> Property type
              </label>
              <select id="property_type" name="property_type[]" multiple title="Property types">
                <option value="apartment" {{ in_array('apartment', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Apartment</option>
                <option value="house" {{ in_array('house', (array)$request->input('property_type', [])) ? 'selected' : '' }}>House</option>
                <option value="villa" {{ in_array('villa', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Villa</option>
                <option value="studio" {{ in_array('studio', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Studio</option>
                <option value="dorm" {{ in_array('dorm', (array)$request->input('property_type', [])) ? 'selected' : '' }}>Dorm</option>
              </select>
              <small class="filter-hint">Hold Ctrl (Windows) or ⌘ (Mac) to select multiple</small>
            </div>

            <div class="filter-group-enhanced">
              <label for="listing_type">
                <i class="fas fa-tag" aria-hidden="true"></i> Listing type
              </label>
              <select id="listing_type" name="listing_type[]" multiple title="Listing types">
                <option value="sale" {{ in_array('sale', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For sale</option>
                <option value="rent" {{ in_array('rent', (array)$request->input('listing_type', [])) ? 'selected' : '' }}>For rent</option>
              </select>
              <small class="filter-hint">Hold Ctrl (Windows) or ⌘ (Mac) to select multiple</small>
            </div>

            <div class="filter-actions-enhanced">
              <button type="submit" class="btn-apply">
                <i class="fas fa-check" aria-hidden="true"></i> Apply filters
              </button>
              <button type="button" class="btn-reset" onclick="clearFilters()">
                <i class="fas fa-redo" aria-hidden="true"></i> Reset
              </button>
            </div>
          </form>
        </aside>

        <section class="map-map-section" aria-label="Property map">
          <div class="map-container-enhanced">
            <div id="map" role="application" aria-label="Google map of properties"></div>
            <p class="map-overlay-info">
              <i class="fas fa-info-circle" aria-hidden="true"></i>
              <span>Click a pin for details and links</span>
            </p>
          </div>
        </section>
      </div>
    </div>
  </div>
</main>
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
                mapElement.innerHTML = '<div class="map-api-error">Google Maps failed to load. Check your API key and connection, then refresh.</div>';
            }
        }
    }, 5000);
</script>
@endpush

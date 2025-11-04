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

    <!-- Map Container -->
    <div class="map-container">
        <div id="map"></div>
        <!-- Click to Get Coordinates Toggle -->
        <div style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <button id="toggleClickMode" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                📍 Click to Get Coordinates
            </button>
            <div id="coordinatesDisplay" style="margin-top: 10px; font-size: 12px; display: none;">
                <div><strong>Lat:</strong> <span id="displayLat">-</span></div>
                <div><strong>Lng:</strong> <span id="displayLng">-</span></div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/MapClickService.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initMap&libraries=places"></script>

<!-- Fallback for Google Maps API -->
<script>
    // Set a timeout to check if Google Maps loaded
    setTimeout(function() {
        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API failed to load');
            document.getElementById('map').innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Google Maps API failed to load. Please check your API key and internet connection.</div>';
        }
    }, 5000);
</script>

<script>
let map;
let markers = [];
let infoWindow;
let mapClickService;

// Properties data from Laravel
const properties = @json($properties);

function initMap() {
    console.log('initMap called');
    console.log('Properties data:', properties);
    
    const mapElement = document.getElementById("map");
    console.log('Map element:', mapElement);
    
    if (!mapElement) {
        console.error('Map element not found!');
        return;
    }
    
    // Initialize map centered on Lebanon
    map = new google.maps.Map(mapElement, {
        center: { lat: 33.894917, lng: 35.503083 },
        zoom: 10,
        mapTypeId: 'roadmap',
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ]
    });

    console.log('Map initialized:', map);

    infoWindow = new google.maps.InfoWindow();

    // Add markers for each property
    addPropertyMarkers();

    // Add search box
    addSearchBox();

    // Initialize MapClickService
    initializeMapClickService();
}

function initializeMapClickService() {
    // Initialize the click service
    mapClickService = new MapClickService(map, {
        showMarker: true,
        showInfoWindow: true,
        enableReverseGeocoding: true,
        reverseGeocodeEndpoint: '{{ route("map.reverse-geocode") }}'
    });

    // Register callback to update display
    mapClickService.onClick((coordinates) => {
        updateCoordinatesDisplay(coordinates);
        console.log('Coordinates clicked:', coordinates);
    });

    // Setup toggle button
    const toggleButton = document.getElementById('toggleClickMode');
    const coordsDisplay = document.getElementById('coordinatesDisplay');
    
    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            if (mapClickService.isEnabled) {
                mapClickService.disable();
                toggleButton.textContent = '📍 Click to Get Coordinates';
                toggleButton.style.background = '#3b82f6';
                coordsDisplay.style.display = 'none';
            } else {
                mapClickService.enable();
                toggleButton.textContent = '✓ Click Mode Active';
                toggleButton.style.background = '#10b981';
                coordsDisplay.style.display = 'block';
            }
        });
    }
}

function updateCoordinatesDisplay(coordinates) {
    const latDisplay = document.getElementById('displayLat');
    const lngDisplay = document.getElementById('displayLng');
    
    if (latDisplay) {
        latDisplay.textContent = coordinates.latitude.toFixed(6);
    }
    if (lngDisplay) {
        lngDisplay.textContent = coordinates.longitude.toFixed(6);
    }
}

function addPropertyMarkers() {
    console.log('Adding property markers, total properties:', properties.length);
    properties.forEach(property => {
        console.log('Processing property:', property.title, 'Lat:', property.latitude, 'Lng:', property.longitude);
        if (property.latitude && property.longitude) {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(property.latitude), lng: parseFloat(property.longitude) },
                map: map,
                title: property.title,
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="18" fill="#3b82f6" stroke="white" stroke-width="2"/>
                            <text x="20" y="26" text-anchor="middle" fill="white" font-family="Arial" font-size="16" font-weight="bold">🏠</text>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(40, 40),
                    anchor: new google.maps.Point(20, 20)
                }
            });

            // Create info window content
            const infoContent = createInfoWindowContent(property);
            
            marker.addListener('click', () => {
                infoWindow.setContent(infoContent);
                infoWindow.open(map, marker);
            });

            markers.push(marker);
        }
    });

    // Fit map to show all markers
    if (markers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => bounds.extend(marker.getPosition()));
        map.fitBounds(bounds);
    }
}

function createInfoWindowContent(property) {
    const primaryImage = property.images && property.images.length > 0 
        ? property.images.find(img => img.is_primary) || property.images[0]
        : null;
    
    const imageUrl = primaryImage 
        ? `{{ asset('storage/') }}/${primaryImage.path}`
        : '{{ asset("img/placeholder.jpg") }}';

    return `
        <div class="property-info">
            ${primaryImage ? `<img src="${imageUrl}" alt="${property.title}">` : ''}
            <div class="property-title">${property.title}</div>
            <div class="property-price">$${property.price.toLocaleString()} ${property.unit ? property.unit.symbol : ''}</div>
            <div class="property-location">${property.city}, ${property.country}</div>
            <div class="property-details">
                ${property.bedroom_nb ? property.bedroom_nb + ' bed' : ''} • 
                ${property.bathroom_nb ? property.bathroom_nb + ' bath' : ''} • 
                ${property.area_m3 ? property.area_m3 + ' m²' : ''}
            </div>
            <div class="property-actions">
                <a href="/properties/${property.id}" class="btn-view">View Details</a>
                <button class="btn-like" onclick="toggleLike(${property.id})">❤️ Like</button>
            </div>
        </div>
    `;
}

function addSearchBox() {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Search for a location...';
    input.style.cssText = `
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 15px;
        padding: 10px;
        text-overflow: ellipsis;
        width: 300px;
        margin: 10px;
    `;

    const searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', () => {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const bounds = new google.maps.LatLngBounds();
        places.forEach(place => {
            if (!place.geometry || !place.geometry.location) return;
            bounds.extend(place.geometry.location);
        });
        map.fitBounds(bounds);
    });
}

function clearFilters() {
    document.getElementById('filterForm').reset();
    window.location.href = '{{ route("map") }}';
}

function toggleLike(propertyId) {
    // This would typically make an AJAX call to like/unlike the property
    console.log('Toggle like for property:', propertyId);
    // You can implement the actual like functionality here
}

// Handle window resize
window.addEventListener('resize', () => {
    if (map) {
        google.maps.event.trigger(map, 'resize');
    }
});
</script>
@endsection
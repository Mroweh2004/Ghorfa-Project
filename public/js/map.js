// Map initialization and management
let map;
let markers = [];
let infoWindow;
let mapClickService;

// Properties data will be passed from Blade template
let properties = [];

// Initialize properties data from window object (set by Blade template)
function initializeProperties(data) {
    properties = data;
}

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

    // Initialize MapClickService if available
    if (typeof MapClickService !== 'undefined') {
        initializeMapClickService();
    }
}

function initializeMapClickService() {
    // Check if reverseGeocodeEndpoint is available (set by Blade template)
    const reverseGeocodeEndpoint = window.mapConfig?.reverseGeocodeEndpoint;
    
    if (!reverseGeocodeEndpoint) {
        console.warn('Reverse geocode endpoint not configured');
        return;
    }

    // Initialize the click service
    mapClickService = new MapClickService(map, {
        showMarker: true,
        showInfoWindow: true,
        enableReverseGeocoding: true,
        reverseGeocodeEndpoint: reverseGeocodeEndpoint
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
                toggleButton.classList.remove('active');
                if (coordsDisplay) {
                    coordsDisplay.style.display = 'none';
                }
            } else {
                mapClickService.enable();
                toggleButton.textContent = '✓ Click Mode Active';
                toggleButton.classList.add('active');
                if (coordsDisplay) {
                    coordsDisplay.style.display = 'block';
                }
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
    
    // Get storage URL and placeholder from window config (set by Blade template)
    const storageUrl = window.mapConfig?.storageUrl || '';
    const placeholderUrl = window.mapConfig?.placeholderUrl || '';

    const imageUrl = primaryImage 
        ? `${storageUrl}/${primaryImage.path}`
        : placeholderUrl;

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
    input.className = 'map-search-input';
    input.style.cssText = `
        background-color: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-size: 15px;
        padding: 12px 16px;
        text-overflow: ellipsis;
        width: 300px;
        margin: 12px;
        transition: all 0.3s ease;
        font-family: inherit;
    `;
    
    input.addEventListener('focus', function() {
        this.style.borderColor = '#667eea';
        this.style.boxShadow = '0 0 0 4px rgba(102, 126, 234, 0.1), 0 4px 12px rgba(0,0,0,0.1)';
    });
    
    input.addEventListener('blur', function() {
        this.style.borderColor = '#e5e7eb';
        this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    });

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
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.reset();
    }
    const mapRoute = window.mapConfig?.mapRoute || '/map';
    window.location.href = mapRoute;
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

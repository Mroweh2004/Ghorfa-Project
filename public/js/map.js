let map;
let markers = [];
let infoWindow;

// Properties data from Laravel
const properties = <?php echo json_encode($properties, 15, 512) ?>;

function initMap() {
    // Initialize map centered on Lebanon
    map = new google.maps.Map(document.getElementById("map"), {
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

    infoWindow = new google.maps.InfoWindow();

    // Add markers for each property
    addPropertyMarkers();

    // Add search box
    addSearchBox();
}

function addPropertyMarkers() {
    properties.forEach(property => {
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
        ? `<?php echo e(asset('storage/')); ?>/${primaryImage.path}`
        : '<?php echo e(asset("img/placeholder.jpg")); ?>';

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
    window.location.href = '<?php echo e(route("map")); ?>';
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
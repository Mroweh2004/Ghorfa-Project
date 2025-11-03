<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Development Tutorial</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .tutorial-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .map-container {
            position: relative;
            width: 100%;
            height: 80vh;
            margin: 20px 0;
            border: 2px solid #ddd;
            background-color: #f0f0f0;
            border-radius: 8px;
        }
        
        #map {
            height: 100%;
            width: 100%;
        }
        
        .controls {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 10px 0;
        }
        
        h2 { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        h3 { color: #374151; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>🗺️ Google Maps Development Tutorial</h1>
    
    <div class="tutorial-section">
        <h2>📚 Lesson 1: Basic Map Initialization</h2>
        <p>This tutorial will teach you how to work with Google Maps in your Laravel application.</p>
        
        <h3>Key Concepts:</h3>
        <ul>
            <li><strong>API Key:</strong> Stored in <code>.env</code> as <code>GOOGLE_MAPS_BROWSER_KEY</code></li>
            <li><strong>Callback Function:</strong> <code>initMap()</code> is called when the API loads</li>
            <li><strong>Map Object:</strong> Created with <code>new google.maps.Map(element, options)</code></li>
            <li><strong>Coordinates:</strong> Latitude (lat) and Longitude (lng) as numbers</li>
        </ul>
    </div>

    <!-- Map Container - This is where the map will appear -->
    <div class="map-container">
        <div id="map"></div>
    </div>

    <!-- Control Buttons for Testing -->
    <div class="controls">
        <button class="btn btn-primary" onclick="addMarker()">📍 Add Marker</button>
        <button class="btn btn-primary" onclick="addCircle()">⭕ Add Circle</button>
        <button class="btn btn-primary" onclick="addPolyline()">📏 Add Polyline</button>
        <button class="btn btn-primary" onclick="addInfoWindow()">💬 Add Info Window</button>
        <button class="btn btn-secondary" onclick="changeMapType()">🗺️ Change Map Type</button>
        <button class="btn btn-secondary" onclick="getCurrentLocation()">📍 Get My Location</button>
        <button class="btn btn-secondary" onclick="clearMarkers()">🗑️ Clear All</button>
    </div>

    <div class="tutorial-section">
        <h2>📝 Code Explanation</h2>
        
        <h3>1. Loading Google Maps API</h3>
        <div class="code-block">
            &lt;script async src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY&callback=initMap"&gt;&lt;/script&gt;
        </div>
        <p><strong>Key Points:</strong></p>
        <ul>
            <li><code>async</code>: Loads the script asynchronously (doesn't block page rendering)</li>
            <li><code>callback=initMap</code>: Calls the <code>initMap()</code> function when API is ready</li>
            <li><code>libraries=places</code>: Add this if you need Places API (autocomplete, search, etc.)</li>
        </ul>

        <h3>2. Map Initialization Options</h3>
        <div class="code-block">
        const map = new google.maps.Map(mapElement, {
            center: { lat: 33.894917, lng: 35.503083 },  // Starting position (Beirut, Lebanon)
            zoom: 10,                                    // Zoom level (1-20, higher = more zoomed in)
            mapTypeId: 'roadmap',                       // Map style: 'roadmap', 'satellite', 'hybrid', 'terrain'
            disableDefaultUI: false,                     // Hide/show default controls
            zoomControl: true,                          // Show zoom buttons
            mapTypeControl: true,                       // Show map type selector
            streetViewControl: false,                    // Show street view button
            fullscreenControl: true                     // Show fullscreen button
        });
        </div>
    </div>

    <script>
        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        // Store map and markers globally so all functions can access them
        let map;
        let markers = [];
        let circles = [];
        let polylines = [];
        let infoWindow;
        let currentInfoWindow = null;

        // ============================================
        // LESSON 1: BASIC MAP INITIALIZATION
        // ============================================
        /**
         * This function is called automatically when Google Maps API loads
         * It's specified in the callback parameter of the script tag
         */
        function initMap() {
            console.log('✅ Google Maps API loaded successfully!');
            
            // Get the HTML element where the map will be displayed
            const mapElement = document.getElementById("map");
            
            // Safety check: Make sure the element exists
            if (!mapElement) {
                console.error('❌ Map element not found! Make sure you have <div id="map"></div>');
                return;
            }
            
            // ============================================
            // CREATE THE MAP OBJECT
            // ============================================
            // This is the core of Google Maps - creating a map instance
            map = new google.maps.Map(mapElement, {
                // Center: Where the map should start (Beirut, Lebanon coordinates)
                center: { lat: 33.894917, lng: 35.503083 },
                
                // Zoom: How close/far the view is (1 = world view, 20 = street level)
                zoom: 15,
                
                // Map Type: The visual style of the map
                mapTypeId: 'roadmap', // Options: 'roadmap', 'satellite', 'hybrid', 'terrain'
                
                // UI Controls: Customize what buttons appear
                zoomControl: true,           // Zoom in/out buttons
                mapTypeControl: true,        // Map type selector
                streetViewControl: true,    // Street View button (hidden)
                fullscreenControl: true,     // Fullscreen button
                
                // Map Styling: Customize appearance (optional)
                styles: [
                    {
                        featureType: 'poi',           // Points of Interest
                        elementType: 'geometry.stroke',
                        stylers: [{ visibility: 'on' }]  // Hide POI labels
                    }
                ]
            });
            
            console.log('✅ Map initialized:', map);
            
            // Initialize a reusable InfoWindow (popup that shows information)
            infoWindow = new google.maps.InfoWindow();
            
            // Optional: Add event listeners
            map.addListener('click', (event) => {
                console.log('📍 Map clicked at:', event.latLng.lat(), event.latLng.lng());
            });
        }

        // ============================================
        // LESSON 2: ADDING MARKERS
        // ============================================
        /**
         * Markers are pins that show locations on the map
         * They can be clicked to show information
         */
        function addMarker() {
            if (!map) {
                alert('Map not initialized yet!');
                return;
            }
            
            // Get center of current map view
            const center = map.getCenter();
            
            // Create a new marker
            const marker = new google.maps.Marker({
                position: {
                    lat: center.lat() ,  // Random position near center
                    lng: center.lng() 
                },
                map: map,                    // Which map to place it on
                title: 'Click me!',          // Tooltip text
                animation: google.maps.Animation.DROP,  // Animation: DROP or BOUNCE
                
                // Custom icon (optional) - you can use images, SVGs, or default
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            // Add click listener to marker
            marker.addListener('click', () => {
                // Close previous info window if open
                if (currentInfoWindow) {
                    currentInfoWindow.close();
                }
                
                // Create content for info window
                const content = `
                    <div style="padding: 10px;">
                        <h3 style="margin: 0 0 10px 0;">Marker #${markers.length + 1}</h3>
                        <p><strong>Latitude:</strong> ${marker.getPosition().lat().toFixed(6)}</p>
                        <p><strong>Longitude:</strong> ${marker.getPosition().lng().toFixed(6)}</p>
                        <button onclick="removeMarker(${markers.length})">Remove</button>
                    </div>
                `;
                
                // Open info window
                currentInfoWindow = new google.maps.InfoWindow({
                    content: content
                });
                currentInfoWindow.open(map, marker);
            });
            
            // Store marker in array for later reference
            markers.push(marker);
            
            console.log('✅ Marker added:', marker);
        }

        // ============================================
        // LESSON 3: ADDING CIRCLES
        // ============================================
        /**
         * Circles show areas on the map
         * Useful for showing radius, coverage areas, etc.
         */
        function addCircle() {
            if (!map) return;
            
            const center = map.getCenter();
            
            const circle = new google.maps.Circle({
                strokeColor: '#FF0000',      // Border color
                strokeOpacity: 0.8,          // Border transparency
                strokeWeight: 2,             // Border width
                fillColor: '#FF0000',        // Fill color
                fillOpacity: 0.35,           // Fill transparency
                map: map,
                center: center,              // Center point
                radius: 5000                 // Radius in meters (5km)
            });
            
            circles.push(circle);
            console.log('✅ Circle added:', circle);
        }

        // ============================================
        // LESSON 4: ADDING POLYLINES
        // ============================================
        /**
         * Polylines draw lines between points
         * Useful for routes, paths, boundaries
         */
        function addPolyline() {
            if (!map) return;
            
            const center = map.getCenter();
            
            // Create a triangle shape
            const triangleCoords = [
                { lat: center.lat() + 0.05, lng: center.lng() },
                { lat: center.lat() - 0.03, lng: center.lng() + 0.05 },
                { lat: center.lat() - 0.03, lng: center.lng() - 0.05 },
                { lat: center.lat() + 0.05, lng: center.lng() }  // Close the shape
            ];
            
            const polyline = new google.maps.Polyline({
                path: triangleCoords,
                geodesic: true,              // Follow Earth's curvature
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });
            
            polyline.setMap(map);
            polylines.push(polyline);
            console.log('✅ Polyline added:', polyline);
        }

        // ============================================
        // LESSON 5: INFO WINDOWS
        // ============================================
        /**
         * Info Windows are popups that show information
         * They can contain HTML content
         */
        function addInfoWindow() {
            if (!map) return;
            
            const center = map.getCenter();
            
            const content = `
                <div style="padding: 15px; max-width: 300px;">
                    <h3 style="margin: 0 0 10px 0; color: #3b82f6;">🏠 Property Information</h3>
                    <p><strong>Price:</strong> $250,000</p>
                    <p><strong>Location:</strong> Beirut, Lebanon</p>
                    <p><strong>Type:</strong> Apartment</p>
                    <p><strong>Size:</strong> 120 m²</p>
                    <button style="margin-top: 10px; padding: 8px 15px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        View Details
                    </button>
                </div>
            `;
            
            const infoWindow = new google.maps.InfoWindow({
                content: content,
                position: center
            });
            
            infoWindow.open(map);
            console.log('✅ Info Window opened');
        }

        // ============================================
        // LESSON 6: MAP TYPES
        // ============================================
        /**
         * Change the visual style of the map
         */
        let currentMapTypeIndex = 0;
        const mapTypes = ['roadmap', 'satellite', 'hybrid', 'terrain'];
        
        function changeMapType() {
            if (!map) return;
            
            currentMapTypeIndex = (currentMapTypeIndex + 1) % mapTypes.length;
            map.setMapTypeId(mapTypes[currentMapTypeIndex]);
            console.log('✅ Map type changed to:', mapTypes[currentMapTypeIndex]);
        }

        // ============================================
        // LESSON 7: GEOLOCATION API
        // ============================================
        /**
         * Get user's current location using browser's geolocation API
         */
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Success callback
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    // Center map on user location
                    map.setCenter(userLocation);
                    map.setZoom(15);
                    
                    // Add marker at user location
                    const marker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: 'You are here!',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                        }
                    });
                    
                    markers.push(marker);
                    
                    // Show info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px;">
                                <h3>📍 You are here!</h3>
                                <p>Lat: ${userLocation.lat.toFixed(6)}</p>
                                <p>Lng: ${userLocation.lng.toFixed(6)}</p>
                            </div>
                        `
                    });
                    infoWindow.open(map, marker);
                    
                    console.log('✅ Location found:', userLocation);
                },
                (error) => {
                    // Error callback
                    console.error('❌ Geolocation error:', error);
                    alert('Could not get your location. Make sure location services are enabled.');
                }
            );
        }

        // ============================================
        // HELPER FUNCTIONS
        // ============================================
        function removeMarker(index) {
            if (markers[index]) {
                markers[index].setMap(null);  // Remove from map
                markers.splice(index, 1);      // Remove from array
                console.log('✅ Marker removed');
            }
        }
        
        function clearMarkers() {
            // Remove all markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            
            // Remove all circles
            circles.forEach(circle => circle.setMap(null));
            circles = [];
            
            // Remove all polylines
            polylines.forEach(polyline => polyline.setMap(null));
            polylines = [];
            
            // Close info windows
            if (currentInfoWindow) {
                currentInfoWindow.close();
            }
            
            console.log('✅ All markers and shapes cleared');
        }

        // ============================================
        // LESSON 8: ADVANCED FEATURES
        // ============================================
        /**
         * Example: Fit map to show all markers
         */
        function fitBoundsToMarkers() {
            if (markers.length === 0) return;
            
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => {
                bounds.extend(marker.getPosition());
            });
            map.fitBounds(bounds);
        }

        /**
         * Example: Calculate distance between two points
         */
        function calculateDistance(point1, point2) {
            return google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng(point1.lat, point1.lng),
                new google.maps.LatLng(point2.lat, point2.lng)
            );
        }

        // ============================================
        // ERROR HANDLING
        // ============================================
        // Check if Google Maps API loaded successfully
        setTimeout(function() {
            if (typeof google === 'undefined' || !google.maps) {
                console.error('❌ Google Maps API failed to load');
                document.getElementById('map').innerHTML = `
                    <div style="padding: 20px; text-align: center; color: red;">
                        <h3>⚠️ Google Maps API Failed to Load</h3>
                        <p>Please check:</p>
                        <ul style="text-align: left; display: inline-block;">
                            <li>Your API key in .env file (GOOGLE_MAPS_BROWSER_KEY)</li>
                            <li>Internet connection</li>
                            <li>Browser console for errors</li>
                        </ul>
                    </div>
                `;
            }
        }, 5000);

        // ============================================
        // RESPONSIVE DESIGN
        // ============================================
        // Resize map when window is resized
        window.addEventListener('resize', () => {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        });
    </script>

    <!-- Load Google Maps API -->
    <!-- 
        IMPORTANT: 
        - The callback parameter tells Google to call initMap() when API is ready
        - Add &libraries=places if you need Places API (autocomplete, search)
        - Add &libraries=geometry if you need distance calculations
    -->
    <script async 
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initMap&libraries=geometry">
    </script>

    <div class="tutorial-section">
        <h2>💡 Common Patterns & Best Practices</h2>
        
        <h3>1. Passing Data from Laravel to JavaScript</h3>
        <div class="code-block">
@verbatim
// In your Blade template:
const properties = @json($properties);

// Then use it in JavaScript:
properties.forEach(property => {
    new google.maps.Marker({
        position: { lat: property.latitude, lng: property.longitude },
        map: map,
        title: property.title
    });
});
@endverbatim
        </div>

        <h3>2. Custom Marker Icons</h3>
        <div class="code-block">
@verbatim
// Using an image URL
icon: {
    url: '/images/marker.png',
    scaledSize: new google.maps.Size(40, 40),
    anchor: new google.maps.Point(20, 40)  // Bottom center
}

// Using SVG
icon: {
    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
        <svg>...</svg>
    `),
    scaledSize: new google.maps.Size(40, 40)
}
@endverbatim
        </div>

        <h3>3. Clustering Markers (for many markers)</h3>
        <div class="code-block">
@verbatim
// Use MarkerClusterer library for better performance
// Include: <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
const markerCluster = new markerClusterer.MarkerClusterer({
    map: map,
    markers: markers
});
@endverbatim
        </div>

        <h3>4. Adding Search Box (Places API)</h3>
        <div class="code-block">
@verbatim
const input = document.createElement('input');
input.type = 'text';
input.placeholder = 'Search location...';

const searchBox = new google.maps.places.SearchBox(input);
map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

searchBox.addListener('places_changed', () => {
    const places = searchBox.getPlaces();
    if (places.length === 0) return;
    
    const bounds = new google.maps.LatLngBounds();
    places.forEach(place => bounds.extend(place.geometry.location));
    map.fitBounds(bounds);
});
@endverbatim
        </div>
    </div>

    <div class="tutorial-section">
        <h2>🔗 Useful Resources</h2>
        <ul>
            <li><a href="https://developers.google.com/maps/documentation/javascript" target="_blank">Google Maps JavaScript API Documentation</a></li>
            <li><a href="https://developers.google.com/maps/documentation/javascript/reference" target="_blank">API Reference</a></li>
            <li><a href="https://developers.google.com/maps/documentation/javascript/examples" target="_blank">Code Examples</a></li>
            <li><a href="https://developers.google.com/maps/documentation/places/web-service" target="_blank">Places API</a></li>
        </ul>
    </div>
</body>
</html>
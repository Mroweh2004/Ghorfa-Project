/**
 * MapClickService - Service to get longitude and latitude by clicking on map
 * 
 * Usage:
 *   const clickService = new MapClickService(map);
 *   clickService.enable();
 *   clickService.onClick((coords) => {
 *     console.log('Clicked:', coords.latitude, coords.longitude);
 *   });
 */
class MapClickService {
    constructor(map, options = {}) {
        this.map = map;
        this.clickListener = null;
        this.marker = null;
        this.infoWindow = null;
        this.isEnabled = false;
        this.callbacks = [];
        
        // Options
        this.options = {
            showMarker: options.showMarker !== false, // Default: true
            showInfoWindow: options.showInfoWindow !== false, // Default: true
            markerIcon: options.markerIcon || null, // Custom marker icon
            enableReverseGeocoding: options.enableReverseGeocoding !== false, // Default: true
            reverseGeocodeEndpoint: options.reverseGeocodeEndpoint || '/map/reverse-geocode',
            ...options
        };
        
        // Create info window if needed
        if (this.options.showInfoWindow) {
            this.infoWindow = new google.maps.InfoWindow();
        }
    }

    /**
     * Enable click-to-get-coordinates functionality
     */
    enable() {
        if (this.isEnabled) {
            return;
        }

        this.isEnabled = true;
        this.clickListener = this.map.addListener('click', (event) => {
            this.handleMapClick(event.latLng);
        });
        
        // Change cursor to crosshair to indicate precise clicking - apply to both map options and map container
        this.map.setOptions({ cursor: 'crosshair' });
        
        // Also set cursor on map container element via CSS
        const mapContainer = this.map.getDiv();
        if (mapContainer) {
            mapContainer.style.cursor = 'crosshair';
            mapContainer.classList.add('clickable');
            // Set cursor on all child elements
            setTimeout(() => {
                const allElements = mapContainer.querySelectorAll('*');
                allElements.forEach(el => {
                    el.style.cursor = 'crosshair';
                });
            }, 50);
        }
    }

    /**
     * Disable click-to-get-coordinates functionality
     */
    disable() {
        if (!this.isEnabled) {
            return;
        }

        if (this.clickListener) {
            google.maps.event.removeListener(this.clickListener);
            this.clickListener = null;
        }

        // Remove marker if exists
        if (this.marker) {
            this.marker.setMap(null);
            this.marker = null;
        }

        // Close info window
        if (this.infoWindow) {
            this.infoWindow.close();
        }

        // Reset cursor - both map options and map container
        this.map.setOptions({ cursor: '' });
        
        // Reset cursor on map container element
        const mapContainer = this.map.getDiv();
        if (mapContainer) {
            mapContainer.style.cursor = '';
            mapContainer.classList.remove('clickable');
            // Reset cursor on all child elements
            const allElements = mapContainer.querySelectorAll('*');
            allElements.forEach(el => {
                el.style.cursor = '';
            });
        }
        
        this.isEnabled = false;
    }

    /**
     * Toggle the click functionality
     */
    toggle() {
        if (this.isEnabled) {
            this.disable();
        } else {
            this.enable();
        }
    }

    /**
     * Handle map click event
     * @param {google.maps.LatLng} latLng - The clicked location
     */
    async handleMapClick(latLng) {
        const latitude = latLng.lat();
        const longitude = latLng.lng();

        const coordinates = {
            latitude: latitude,
            longitude: longitude,
            address: null
        };

        // Show marker if enabled
        if (this.options.showMarker) {
            this.setMarker(latLng);
        }

        // Reverse geocode if enabled
        if (this.options.enableReverseGeocoding) {
            try {
                const address = await this.reverseGeocode(latitude, longitude);
                coordinates.address = address;
            } catch (error) {
                console.error('Reverse geocoding failed:', error);
            }
        }

        // Show info window if enabled
        if (this.options.showInfoWindow) {
            this.showInfoWindow(latLng, coordinates);
        }

        // Call all registered callbacks
        this.callbacks.forEach(callback => {
            try {
                callback(coordinates);
            } catch (error) {
                console.error('Callback error:', error);
            }
        });
    }

    /**
     * Set marker at clicked location
     * @param {google.maps.LatLng} latLng - The location
     */
    setMarker(latLng) {
        // Remove existing marker
        if (this.marker) {
            this.marker.setMap(null);
        }

        // Create new marker
        const markerOptions = {
            position: latLng,
            map: this.map,
            draggable: false,
            title: 'Selected Location'
        };

        // Add custom icon if provided
        if (this.options.markerIcon) {
            markerOptions.icon = this.options.markerIcon;
        }

        this.marker = new google.maps.Marker(markerOptions);

        // If marker is draggable, handle drag events
        if (markerOptions.draggable) {
            this.marker.addListener('dragend', (event) => {
                this.handleMapClick(event.latLng);
            });
        }
    }

    /**
     * Show info window with coordinates
     * @param {google.maps.LatLng} latLng - The location
     * @param {Object} coordinates - The coordinates object
     */
    showInfoWindow(latLng, coordinates) {
        const content = this.createInfoWindowContent(coordinates);
        this.infoWindow.setContent(content);
        this.infoWindow.open(this.map, this.marker || null);
    }

    /**
     * Create info window content HTML
     * @param {Object} coordinates - The coordinates object
     * @returns {string} HTML content
     */
    createInfoWindowContent(coordinates) {
        let html = `
            <div style="padding: 10px; min-width: 200px;">
                <h3 style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold;">Location Coordinates</h3>
                <div style="margin-bottom: 8px;">
                    <strong>Latitude:</strong> ${coordinates.latitude.toFixed(6)}
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Longitude:</strong> ${coordinates.longitude.toFixed(6)}
                </div>
        `;

        if (coordinates.address) {
            html += `
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                    <strong>Address:</strong><br>
                    <span style="font-size: 12px; color: #666;">${coordinates.address}</span>
                </div>
            `;
        }

        html += `
                <div style="margin-top: 10px;">
                    <button onclick="copyCoordinates(${coordinates.latitude}, ${coordinates.longitude})" 
                            style="padding: 5px 10px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Copy Coordinates
                    </button>
                </div>
            </div>
        `;

        return html;
    }

    /**
     * Reverse geocode coordinates to get address
     * @param {number} latitude - Latitude
     * @param {number} longitude - Longitude
     * @returns {Promise<string>} Formatted address
     */
    async reverseGeocode(latitude, longitude) {
        if (!this.options.reverseGeocodeEndpoint) {
            // Fallback to client-side reverse geocoding
            return this.clientSideReverseGeocode(latitude, longitude);
        }

        try {
            const response = await fetch(this.options.reverseGeocodeEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ latitude, longitude })
            });

            if (!response.ok) {
                throw new Error('Reverse geocoding request failed');
            }

            const data = await response.json();
            return data.address || data.formatted_address || 'Address not available';
        } catch (error) {
            console.error('Server-side reverse geocoding failed:', error);
            // Fallback to client-side
            return this.clientSideReverseGeocode(latitude, longitude);
        }
    }

    /**
     * Client-side reverse geocoding using Google Maps API
     * @param {number} latitude - Latitude
     * @param {number} longitude - Longitude
     * @returns {Promise<string>} Formatted address
     */
    clientSideReverseGeocode(latitude, longitude) {
        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode(
                { location: { lat: latitude, lng: longitude } },
                (results, status) => {
                    if (status === 'OK' && results[0]) {
                        resolve(results[0].formatted_address);
                    } else {
                        resolve('Address not available');
                    }
                }
            );
        });
    }

    /**
     * Register a callback to be called when map is clicked
     * @param {Function} callback - Callback function receiving coordinates object
     */
    onClick(callback) {
        if (typeof callback === 'function') {
            this.callbacks.push(callback);
        }
    }

    /**
     * Remove a callback
     * @param {Function} callback - Callback to remove
     */
    offClick(callback) {
        const index = this.callbacks.indexOf(callback);
        if (index > -1) {
            this.callbacks.splice(index, 1);
        }
    }

    /**
     * Get current coordinates if marker is set
     * @returns {Object|null} Coordinates object or null
     */
    getCoordinates() {
        if (!this.marker) {
            return null;
        }

        const position = this.marker.getPosition();
        return {
            latitude: position.lat(),
            longitude: position.lng()
        };
    }

    /**
     * Clear the marker and info window
     */
    clear() {
        if (this.marker) {
            this.marker.setMap(null);
            this.marker = null;
        }

        if (this.infoWindow) {
            this.infoWindow.close();
        }
    }
}

// Global function to copy coordinates to clipboard
function copyCoordinates(lat, lng) {
    const text = `${lat}, ${lng}`;
    navigator.clipboard.writeText(text).then(() => {
        alert('Coordinates copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Coordinates copied to clipboard!');
    });
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MapClickService;
}


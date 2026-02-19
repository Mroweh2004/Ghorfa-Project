@extends('layouts.app')

@section('title', 'Edit Property')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/list-property.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/list-property.js') }}"></script>
<script src="{{ asset('js/MapClickService.js') }}"></script>
@endpush

@section('content')
@php
  $backgroundImage = \App\Services\PropertyImageService::getImageAssetUrl($property);
@endphp
<section class="title-section" style="background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url('{{ $backgroundImage }}') center/cover;">
  <div class="hero-content">
    <div class="hero-text">
      <h1>Edit Property</h1>
      <p>Follow the steps to update your listing</p>
    </div>
    <div class="hero-character">
      <img src="{{ asset('images/character/tie.png') }}" alt="Let's update" class="character-image">
    </div>
  </div>
</section>

<section class="content-section">
  {{-- Progress Indicator --}}
  <div class="wizard-progress">
    <div class="wizard-helper-character">
      <img src="{{ asset('images/character/thinking.png') }}" alt="Guide" class="helper-avatar">
      <div class="helper-speech-bubble">Let's update!</div>
    </div>
    
    <div class="wizard-step active" data-step="1">
      <div class="step-number">1</div>
      <div class="step-title">Basic Info</div>
    </div>
    <div class="wizard-step" data-step="2">
      <div class="step-number">2</div>
      <div class="step-title">Location</div>
    </div>
    <div class="wizard-step" data-step="3">
      <div class="step-number">3</div>
      <div class="step-title">Details</div>
    </div>
    <div class="wizard-step" data-step="4">
      <div class="step-number">4</div>
      <div class="step-title">Features</div>
    </div>
    <div class="wizard-step" data-step="5">
      <div class="step-number">5</div>
      <div class="step-title">Images</div>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
  @endif

  <form
    class="listing-form"
    method="POST"
    action="{{ route('properties.update', $property->id) }}"
    enctype="multipart/form-data"
    novalidate
  >
    @csrf
    @method('PUT')

    @if($errors->any())
      <div class="alert alert-danger mb-4">
        <h4>Validation Errors:</h4>
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="form-content">
      {{-- STEP 1 --}}
      <div class="wizard-content" data-step="1">
        <div class="character-helper">
          <img src="{{ asset('images/character/tie.png') }}" alt="Guide" class="character-helper-image">
          <div class="character-helper-text">
            <h4>Update the basics</h4>
            <p>Revise your property title and description to keep your listing current and appealing.</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Basic Info</h1>

        <div class="form-input">
          <label for="title" class="inputs-label">Title</label>
          <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $property->title) }}"
            placeholder="e.g. Sunny 2BR apartment with sea view"
            maxlength="120"
            autocomplete="organization-title"
            required
          >
          <small>Keep it short and descriptive (max 120 characters).</small>
          @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-input">
          <label for="description" class="inputs-label">Description</label>
          <textarea
            id="description"
            name="description"
            placeholder="Tell guests what makes this place special: layout, view, nearby landmarks, and any house highlights..."
            minlength="30"
            maxlength="1200"
            required
          >{{ old('description', $property->description) }}</textarea>
          <small>Be specific: floor, orientation, surroundings, and any rules worth knowing.</small>
          @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
          <div class="form-input">
            <label for="property_type" class="inputs-label">Property Type</label>
            @php
              $propertyOptions = [
                ['value' => 'apartment', 'label' => 'Apartment'],
                ['value' => 'house', 'label' => 'House'],
                ['value' => 'villa', 'label' => 'Villa'],
                ['value' => 'dorm', 'label' => 'Dorm'],
                ['value' => 'other', 'label' => 'Other'],
              ];
              $currentType = old('property_type', $property->property_type);
            @endphp
            <select id="property_type" name="property_type" required>
              <option value="" disabled {{ $currentType ? '' : 'selected' }}>Choose a property type…</option>
              @foreach($propertyOptions as $option)
                <option
                  value="{{ $option['value'] }}"
                  {{ $currentType && strcasecmp($currentType, $option['value']) === 0 ? 'selected' : '' }}
                >
                  {{ $option['label'] }}
                </option>
              @endforeach
            </select>
            @error('property_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-input">
            <label for="listing_type" class="inputs-label">Listing Type</label>
            @php
              $currentListing = old('listing_type', $property->listing_type);
            @endphp
            <select id="listing_type" name="listing_type" required>
              <option value="" disabled {{ $currentListing ? '' : 'selected' }}>Is it for rent or for sale?</option>
              <option value="rent" {{ $currentListing && strcasecmp($currentListing, 'rent') === 0 ? 'selected' : '' }}>For Rent</option>
              <option value="sale" {{ $currentListing && strcasecmp($currentListing, 'sale') === 0 ? 'selected' : '' }}>For Sale</option>
            </select>
            @error('listing_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>
      </div>

      {{-- STEP 2 --}}
      <div class="wizard-content" data-step="2" style="display:none;">
        <div class="character-helper">
          <img src="{{ asset('images/character/phone.png') }}" alt="Guide" class="character-helper-image">
          <div class="character-helper-text">
            <h4>Update location</h4>
            <p>Verify your property location on the map. Move the pin if needed for accuracy.</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Location</h1>
        {{-- Hidden fields for coordinates --}}
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}">

        {{-- Map for selecting location --}}
        <div class="form-input">
          <label class="inputs-label">Select Location on Map</label>
          <div class="map-controls">
            <button type="button" id="enableMapClick">
              📍 Click on Map to Set Location
            </button>
            <span id="coordinatesStatus"></span>
          </div>
          <div id="property-location-map"></div>
          <small>Click on the map to set your property's exact location. This will automatically fill the coordinates.</small>
          @error('latitude') <small class="text-danger">{{ $message }}</small> @enderror
          @error('longitude') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>
      </div>

      {{-- STEP 3 --}}
      <div class="wizard-content" data-step="3" style="display:none;">
        <div class="character-helper">
          <img src="{{ asset('images/character/thinking.png') }}" alt="Guide" class="character-helper-image">
          <div class="character-helper-text">
            <h4>Review your pricing</h4>
            <p>Update pricing, size, and other key details to reflect any changes to your property.</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Details</h1>

        <div class="form-input">
          <label for="price" class="inputs-label">Price</label>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label for="unit" class="inputs-label" style="margin-bottom: 0;">Unit</label>
            <label for="rent_duration_units" class="inputs-label" style="margin-bottom: 0;">Accepted rent duration units</label>
          </div>
          <div class="row">
            <input
              type="number"
              id="price"
              name="price"
              value="{{ old('price', $property->price) }}"
              placeholder="e.g. 750 (monthly) or 145000 (sale)"
              inputmode="decimal"
              min="0"
              step="0.01"
              required
            >
            <select name="unit" id="unit">
              @php
                $selectedUnit = old('unit', $property->unit_id);
              @endphp
              @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ (string)$selectedUnit === (string)$unit->id ? 'selected' : '' }}>
                  {{ $unit->code }}
                </option>
              @endforeach
            </select>
            <select name="price_duration" id="price_duration">
              @php $duration = old('price_duration', $property->price_duration ?? 'month'); @endphp
              <option value="month" {{ $duration === 'month' ? 'selected' : '' }}>per month</option>
              <option value="week"  {{ $duration === 'week' ? 'selected' : '' }}>per week</option>
              <option value="day"   {{ $duration === 'day' ? 'selected' : '' }}>per day</option>
              <option value="year"  {{ $duration === 'year' ? 'selected' : '' }}>per year</option>
            </select>
            @php
              $defaultUnits = ['day', 'week', 'month', 'year'];
              $storedUnits = $property->rent_duration_units
                ? array_filter(explode(',', (string) $property->rent_duration_units))
                : $defaultUnits;
              if (empty($storedUnits)) $storedUnits = $defaultUnits;
              $rentUnits = old('rent_duration_units', $storedUnits);
              if (!is_array($rentUnits)) $rentUnits = [$rentUnits];
            @endphp
            <div id="rent_duration_units" class="rent-duration-grid" aria-label="Accepted rent duration units">
              <label class="rent-unit">
                <input type="checkbox" name="rent_duration_units[]" value="day" {{ in_array('day', $rentUnits) ? 'checked' : '' }}>
                <span class="rent-unit-text">day</span>
              </label>
              <label class="rent-unit">
                <input type="checkbox" name="rent_duration_units[]" value="week" {{ in_array('week', $rentUnits) ? 'checked' : '' }}>
                <span class="rent-unit-text">week</span>
              </label>
              <label class="rent-unit">
                <input type="checkbox" name="rent_duration_units[]" value="month" {{ in_array('month', $rentUnits) ? 'checked' : '' }}>
                <span class="rent-unit-text">month</span>
              </label>
              <label class="rent-unit">
                <input type="checkbox" name="rent_duration_units[]" value="year" {{ in_array('year', $rentUnits) ? 'checked' : '' }}>
                <span class="rent-unit-text">year</span>
              </label>
            </div>
          </div>
          <small>Enter a numeric value only (currency handled elsewhere).</small>
          <small><strong>Auto-calculated:</strong> we’ll compute the equivalent prices for day/week/month/year from your selected duration.</small>
          @error('price') <small class="text-danger">{{ $message }}</small> @enderror
          @error('price_duration') <small class="text-danger">{{ $message }}</small> @enderror
          @error('rent_duration_units') <small class="text-danger">{{ $message }}</small> @enderror
          @error('unit') <small class="text-danger">{{ $message }}</small> @enderror

          <div class="price-breakdown">
            <div class="price-breakdown-title">💰 Calculated prices</div>
            <small style="display: block; margin-bottom: 12px; color: #6b7280;">These prices are auto-calculated from your main price, but you can edit each one individually if needed.</small>
            <div class="price-breakdown-grid">
              <div class="price-breakdown-item">
                <label for="price_per_day">Per day</label>
                <input type="number" id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $property->price_per_day) }}" step="0.01" min="0" placeholder="Auto-calculated">
              </div>
              <div class="price-breakdown-item">
                <label for="price_per_week">Per week</label>
                <input type="number" id="price_per_week" name="price_per_week" value="{{ old('price_per_week', $property->price_per_week) }}" step="0.01" min="0" placeholder="Auto-calculated">
              </div>
              <div class="price-breakdown-item">
                <label for="price_per_month">Per month</label>
                <input type="number" id="price_per_month" name="price_per_month" value="{{ old('price_per_month', $property->price_per_month) }}" step="0.01" min="0" placeholder="Auto-calculated">
              </div>
              <div class="price-breakdown-item">
                <label for="price_per_year">Per year</label>
                <input type="number" id="price_per_year" name="price_per_year" value="{{ old('price_per_year', $property->price_per_year) }}" step="0.01" min="0" placeholder="Auto-calculated">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-input">
            <label for="area_m3" class="inputs-label">Area (m²)</label>
            <input
              type="number"
              id="area_m3"
              name="area_m3"
              value="{{ old('area_m3', $property->area_m3) }}"
              placeholder="e.g. 95"
              inputmode="decimal"
              min="0"
              step="0.1"
              required
            >
            @error('area_m3') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-input">
            <label for="room_nb" class="inputs-label">Rooms</label>
            <input
              type="number"
              id="room_nb"
              name="room_nb"
              value="{{ old('room_nb', $property->room_nb) }}"
              placeholder="e.g. 4"
              inputmode="numeric"
              min="0"
              required
            >
            @error('room_nb') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-input">
            <label for="bathroom_nb" class="inputs-label">Bathrooms</label>
            <input
              type="number"
              id="bathroom_nb"
              name="bathroom_nb"
              value="{{ old('bathroom_nb', $property->bathroom_nb) }}"
              placeholder="e.g. 2"
              inputmode="numeric"
              min="0"
              required
            >
            @error('bathroom_nb') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-input">
            <label for="bedroom_nb" class="inputs-label">Bedrooms</label>
            <input
              type="number"
              id="bedroom_nb"
              name="bedroom_nb"
              value="{{ old('bedroom_nb', $property->bedroom_nb) }}"
              placeholder="e.g. 3"
              inputmode="numeric"
              min="0"
              required
            >
            @error('bedroom_nb') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>
      </div>

      {{-- STEP 4 --}}
      <div class="wizard-content" data-step="4" style="display:none;">
        <div class="character-helper">
          <img src="{{ asset('images/character/wave-2.png') }}" alt="Guide" class="character-helper-image">
          <div class="character-helper-text">
            <h4>Update amenities & rules</h4>
            <p>Keep your amenities list and house rules current for potential tenants.</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Amenities & Rules</h1>

          {{-- Amenities as pills --}}
          <div class="form-input amenities-group">
          <h4 class="checkbox-label">Amenities</h4>
          @php
            $selectedAmenities = collect(old('amenities', $property->amenities->pluck('id')->toArray()))
              ->map(fn($v) => (int)$v)
              ->all();
          @endphp
          <div class="amenities-grid">
            @foreach($amenities as $amenity)
              <label class="amenity">
                <input
                  type="checkbox"
                  name="amenities[]"
                  value="{{ $amenity->id }}"
                  {{ in_array($amenity->id, $selectedAmenities, true) ? 'checked' : '' }}
                >
                <span class="amenity-text">{{ $amenity->name }}</span>
              </label>
            @endforeach
          </div>
          <small>Select all that apply (e.g., Wi-Fi, Parking, Elevator).</small>
        </div>

        {{-- Rules --}}
        <div class="form-input rules-group">
          <h4 class="checkbox-label">Rules</h4>
          @php
            $selectedRules = collect(old('rules', $property->rules->pluck('id')->toArray()))
              ->map(fn($v) => (int)$v)
              ->all();
          @endphp
          <div class="rule-grid">
            @foreach($rules as $rule)
              <label class="rule">
                <input
                  type="checkbox"
                  name="rules[]"
                  value="{{ $rule->id }}"
                  {{ in_array($rule->id, $selectedRules, true) ? 'checked' : '' }}
                >
                <span class="rule-text">{{ $rule->name }}</span>
              </label>
            @endforeach
          </div>
          <small>Common examples: No smoking, No pets, Quiet hours, ID required on check-in.</small>
        </div>
      </div>
      </div>

      {{-- STEP 5 --}}
      <div class="wizard-content" data-step="5" style="display:none;">
        <div class="character-helper">
          <img src="{{ asset('images/character/phone.png') }}" alt="Guide" class="character-helper-image">
          <div class="character-helper-text">
            <h4>Update your photos</h4>
            <p>Add new photos or remove old ones. Great visuals attract more tenants!</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Images</h1>

        @php
          $removedImageIds = collect(old('remove_images', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

          $existingImages = $property->images->map(function ($img) use ($removedImageIds) {
              return [
                  'id'         => $img->id,
                  'url'        => Storage::url($img->path),
                  'name'       => basename($img->path),
                  'is_primary' => (bool) $img->is_primary,
                  'removed'    => in_array($img->id, $removedImageIds, true),
              ];
          })->values();
        @endphp

        <div class="form-input">
          <label for="images" class="inputs-label">Manage Images</label>
          <div class="file-upload-container">
            <input
              type="file"
              id="images"
              name="images[]"
              accept="image/*"
              multiple
              class="file-input"
              aria-describedby="images_help"
            >
            <label for="images" class="file-label">
              <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
              Choose Images
            </label>
            <div id="images_help" class="file-info">
              Add new photos or remove existing ones. PNG or JPEG recommended.
            </div>
            <div
              id="image-previews"
              class="image-previews"
              aria-live="polite"
              data-existing-images='@json($existingImages)'
              data-remove-input-name="remove_images[]"
              data-remove-container-id="removed-images-container"
            ></div>
            <div
              id="removed-images-container"
              data-role="removed-images-container"
              style="display:none;"
            >
              @foreach($removedImageIds as $removedId)
                <input type="hidden" name="remove_images[]" value="{{ $removedId }}">
              @endforeach
            </div>
            @if($errors->has('images'))
              <div class="alert alert-danger mt-2">
                <small>Please upload at least one image.</small>
              </div>
            @endif
          </div>
          @error('images') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>
      </div>

      {{-- Navigation Buttons --}}
      <div class="wizard-navigation">
        <button type="button" class="wizard-btn wizard-prev" id="wizardPrev" style="display:none;">← Previous</button>
        <button type="button" class="wizard-btn wizard-next" id="wizardNext">Next →</button>
        <button type="submit" class="wizard-btn wizard-submit" id="wizardSubmit" style="display:none;">Update Property</button>
      </div>
    </div>
  </form>
</section>

{{-- Google Maps API and Map Initialization --}}
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initEditPropertyLocationMap&libraries=places"></script>

<script>
let editPropertyLocationMap;
let editPropertyMapClickService;

function initEditPropertyLocationMap() {
    // Initialize map centered on property location or default to Lebanon
    const mapElement = document.getElementById('property-location-map');
    
    if (!mapElement) {
        console.error('Property location map element not found');
        return;
    }

    // Get coordinates from old values or property values, or default to Lebanon
    const defaultLat = @json($property->latitude ?? 33.894917);
    const defaultLng = @json($property->longitude ?? 35.503083);
    const oldLat = parseFloat(document.getElementById('latitude')?.value) || defaultLat;
    const oldLng = parseFloat(document.getElementById('longitude')?.value) || defaultLng;

    // Initialize map
    editPropertyLocationMap = new google.maps.Map(mapElement, {
        center: { lat: oldLat, lng: oldLng },
        zoom: 13,
        mapTypeId: 'roadmap'
    });

    // If there are existing coordinates, show a marker
    if (document.getElementById('latitude')?.value && document.getElementById('longitude')?.value) {
        new google.maps.Marker({
            position: { lat: oldLat, lng: oldLng },
            map: editPropertyLocationMap,
            title: 'Current Location'
        });
        updateCoordinatesStatus(oldLat, oldLng, true);
    }

    // Initialize MapClickService
    editPropertyMapClickService = new MapClickService(editPropertyLocationMap, {
        showMarker: true,
        showInfoWindow: true,
        enableReverseGeocoding: true,
        reverseGeocodeEndpoint: '{{ route("map.reverse-geocode") }}'
    });

    // Register callback to update form fields
    editPropertyMapClickService.onClick((coordinates) => {
        // Update hidden form fields
        document.getElementById('latitude').value = coordinates.latitude;
        document.getElementById('longitude').value = coordinates.longitude;
        
        // Update status display
        updateCoordinatesStatus(coordinates.latitude, coordinates.longitude, true);
        
        console.log('Property location updated:', coordinates);
    });

    // Setup toggle button
    const enableButton = document.getElementById('enableMapClick');
    const coordsDisplay = document.getElementById('coordinatesStatus');
    
    if (enableButton) {
        enableButton.addEventListener('click', () => {
            if (editPropertyMapClickService.isEnabled) {
                editPropertyMapClickService.disable();
                enableButton.textContent = '📍 Click on Map to Set Location';
                enableButton.style.background = '#3b82f6';
                coordsDisplay.textContent = '';
            } else {
                editPropertyMapClickService.enable();
                enableButton.textContent = '✓ Click Mode Active - Click on Map';
                enableButton.style.background = '#10b981';
                coordsDisplay.textContent = 'Click anywhere on the map to set location';
                // Ensure cursor is pointer when enabled - set on map container
                editPropertyLocationMap.setOptions({ cursor: 'pointer' });
                const mapContainer = document.getElementById('property-location-map');
                if (mapContainer) {
                    mapContainer.style.cursor = 'pointer';
                }
            }
        });

        // Auto-enable on page load if coordinates are not set
        if (!document.getElementById('latitude')?.value || !document.getElementById('longitude')?.value) {
            // Enable click mode by default
            editPropertyMapClickService.enable();
            enableButton.textContent = '✓ Click Mode Active - Click on Map';
            enableButton.style.background = '#10b981';
            coordsDisplay.textContent = 'Click anywhere on the map to set location';
            // Ensure cursor is pointer when enabled - set on map container
            editPropertyLocationMap.setOptions({ cursor: 'pointer' });
            const mapContainer = document.getElementById('property-location-map');
            if (mapContainer) {
                mapContainer.style.cursor = 'pointer';
            }
        } else {
            // Show current coordinates status
            updateCoordinatesStatus(oldLat, oldLng, true);
        }
    }

    // Add search box for address
    addEditAddressSearchBox();
}

function updateCoordinatesStatus(lat, lng, isSet) {
    const statusSpan = document.getElementById('coordinatesStatus');
    if (statusSpan) {
        if (isSet) {
            statusSpan.textContent = `✓ Location set: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            statusSpan.style.color = '#10b981';
        } else {
            statusSpan.textContent = 'Location not set';
            statusSpan.style.color = '#6b7280';
        }
    }
}

function addEditAddressSearchBox() {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Search for address...';
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
    editPropertyLocationMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', () => {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const place = places[0];
        if (!place.geometry || !place.geometry.location) return;

        // Update map center
        editPropertyLocationMap.setCenter(place.geometry.location);
        editPropertyLocationMap.setZoom(16);

        // Simulate click at this location to set coordinates
        editPropertyMapClickService.handleMapClick(place.geometry.location);

        // Update address field if it's empty
        const addressField = document.getElementById('address');
        if (addressField && !addressField.value) {
            addressField.value = place.formatted_address || place.name;
        }
    });
}

// Handle form validation - ensure coordinates are set before submit (optional for edit)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.listing-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude')?.value;
            const lng = document.getElementById('longitude')?.value;
            
            // Note: For edit, coordinates are optional (will fallback to geocoding)
            // But if user intentionally cleared them, we can warn
            if (!lat || !lng) {
                const confirmed = confirm('No location coordinates set. The system will try to geocode from address. Continue anyway?');
                if (!confirmed) {
                    e.preventDefault();
                    const enableButton = document.getElementById('enableMapClick');
                    if (enableButton) {
                        enableButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (!editPropertyMapClickService || !editPropertyMapClickService.isEnabled) {
                            enableButton.click();
                        }
                        // Ensure cursor is set to pointer when enabled
                        if (editPropertyMapClickService && editPropertyMapClickService.isEnabled) {
                            editPropertyLocationMap.setOptions({ cursor: 'pointer' });
                            const mapContainer = document.getElementById('property-location-map');
                            if (mapContainer) {
                                mapContainer.style.cursor = 'pointer';
                            }
                        }
                    }
                    return false;
                }
            }
        });
    }
});
</script>
@endsection

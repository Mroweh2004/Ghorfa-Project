@extends('layouts.app')
@section('title', 'list-space')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/list-property.css') }}">
@endpush

@section('content')
<section class="title-section">
  <div class="hero-content">
    <div class="hero-text">
      <h1>List Your Space</h1>
      <p>Follow the steps to create your listing</p>
    </div>
    <div class="hero-character">
      <img src="{{ asset('images/character/wave-1.png') }}" alt="Welcome" class="character-image">
    </div>
  </div>
</section>

<section class="content-section">
  {{-- Progress Indicator --}}
  <div class="wizard-progress">
    <div class="wizard-helper-character">
      <img src="{{ asset('images/character/thinking.png') }}" alt="Guide" class="helper-avatar">
      <div class="helper-speech-bubble">Let's get started!</div>
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

  <form class="listing-form" method="POST" action="{{ route('submit-listing') }}" enctype="multipart/form-data" novalidate>
    @csrf
    
    {{-- Debug info --}}
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
            <h4>Let's start with the basics!</h4>
            <p>Tell us about your property. A great title and description help tenants find your listing faster.</p>
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
            value="{{ old('title') }}"
            placeholder="e.g. Sunny 2BR apartment with sea view"
            maxlength="120"
            autocomplete="organization-title"
            required
          >
          <small>Keep it short & descriptive (max 120 characters).</small>
          @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-input">
          <label for="description" class="inputs-label">Description</label>
          <textarea
            id="description"
            name="description"
            placeholder="Tell guests what makes this place special: layout, view, nearby landmarks, and any house highlights…"
            minlength="30"
            maxlength="1200"
            required
          >{{ old('description') }}</textarea>
          <small>Be specific: floor, orientation, surroundings, and any rules worth knowing.</small>
          @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="row">
          <div class="form-input">
            <label for="property_type" class="inputs-label">Property Type</label>
            <select id="property_type" name="property_type" required>
              <option value="" disabled {{ old('property_type') ? '' : 'selected' }}>Choose a property type…</option>
              <option value="apartment" {{ old('property_type') === 'apartment' ? 'selected' : '' }}>Apartment</option>
              <option value="house"     {{ old('property_type') === 'house' ? 'selected' : '' }}>House</option>
              <option value="dorm"      {{ old('property_type') === 'dorm' ? 'selected' : '' }}>Dorm</option>
              <option value="other"     {{ old('property_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('property_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-input">
            <label for="listing_type" class="inputs-label">Listing Type</label>
            <select id="listing_type" name="listing_type" required>
              <option value="" disabled {{ old('listing_type') ? '' : 'selected' }}>Is it for rent or for sale?</option>
              <option value="rent" {{ old('listing_type') === 'rent' ? 'selected' : '' }}>For Rent</option>
              <option value="sale" {{ old('listing_type') === 'sale' ? 'selected' : '' }}>For Sale</option>
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
            <h4>Where's your property?</h4>
            <p>Click on the map to pin your exact location. The more precise, the easier it is for tenants to find you!</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Location</h1>
        {{-- Hidden fields for coordinates --}}
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

        {{-- Map for selecting location --}}
        <div class="form-input">
          <label class="inputs-label">Select Location on Map</label>
          <div style="position: relative;" data-reverse-geocode-endpoint="{{ route('map.reverse-geocode') }}">
            <div id="property-location-map"></div>
            <button type="button" id="enableMapClick" class="map-control-button">
              📍 Enable Map Click
            </button>
            <span id="coordinatesStatus" class="map-status-overlay"></span>
          </div>
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
            <h4>Now for the details!</h4>
            <p>Set your pricing, size, and property specifics. Don't forget to choose accepted rent durations!</p>
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
                value="{{ old('price') }}"
                placeholder="e.g. 750 (monthly) or 145000 (sale)"
                inputmode="decimal"
                min="0"
                step="0.01"
                required
              >
              <select name="unit" id="unit">
                @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('unit') == $unit->id ? 'selected' : '' }}>{{ $unit->code }}</option>
                @endforeach
              </select>
              <select name="price_duration" id="price_duration">
                @php $duration = old('price_duration', 'month'); @endphp
                <option value="month" {{ $duration === 'month' ? 'selected' : '' }}>per month</option>
                <option value="week"  {{ $duration === 'week' ? 'selected' : '' }}>per week</option>
                <option value="day"   {{ $duration === 'day' ? 'selected' : '' }}>per day</option>
                <option value="year"  {{ $duration === 'year' ? 'selected' : '' }}>per year</option>
              </select>
              @php
                $defaultUnits = ['day', 'week', 'month', 'year'];
                $rentUnits = old('rent_duration_units', $defaultUnits);
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
            <small><strong>Auto-calculated:</strong> we'll compute the equivalent prices for day/week/month/year from your selected duration.</small>
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
                  <input type="number" id="price_per_day" name="price_per_day" value="{{ old('price_per_day') }}" step="0.01" min="0" placeholder="Auto-calculated">
                </div>
                <div class="price-breakdown-item">
                  <label for="price_per_week">Per week</label>
                  <input type="number" id="price_per_week" name="price_per_week" value="{{ old('price_per_week') }}" step="0.01" min="0" placeholder="Auto-calculated">
                </div>
                <div class="price-breakdown-item">
                  <label for="price_per_month">Per month</label>
                  <input type="number" id="price_per_month" name="price_per_month" value="{{ old('price_per_month') }}" step="0.01" min="0" placeholder="Auto-calculated">
                </div>
                <div class="price-breakdown-item">
                  <label for="price_per_year">Per year</label>
                  <input type="number" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" step="0.01" min="0" placeholder="Auto-calculated">
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
                value="{{ old('area_m3') }}"
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
                value="{{ old('room_nb') }}"
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
                value="{{ old('bathroom_nb') }}"
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
                value="{{ old('bedroom_nb') }}"
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
            <h4>What makes your place special?</h4>
            <p>Select amenities and set house rules. These help tenants understand what to expect!</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Amenities & Rules</h1>
          
          <div class="form-input amenities-group">
          <h4 class="checkbox-label">Amenities</h4>
          <div class="amenities-grid">
            @foreach($amenities as $amenity)
              <label class="amenity">
                <input
                  type="checkbox"
                  name="amenities[]"
                  value="{{ $amenity->id }}"
                  {{ (is_array(old('amenities')) && in_array($amenity->id, old('amenities'))) || (is_array(request('amenities')) && in_array($amenity->id, request('amenities'))) ? 'checked' : '' }}
                >
                <span class="amenity-text">{{ $amenity->name }}</span>
              </label>
            @endforeach
          </div>
          <small>Select all that apply (e.g., Wi-Fi, Parking, Elevator).</small>
        </div>

        {{-- Rules (same pills) --}}
        <div class="form-input rules-group">
          <h4 class="checkbox-label">Rules</h4>
          <div class="rule-grid">
            @foreach($rules as $rule)
              <label class="rule">
                <input
                  type="checkbox"
                  name="rules[]"
                  value="{{ $rule->id }}"
                  {{ (is_array(old('rules')) && in_array($rule->id, old('rules'))) || (is_array(request('rules')) && in_array($rule->id, request('rules'))) ? 'checked' : '' }}
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
            <h4>Almost there! Add photos</h4>
            <p>Great photos make all the difference! Upload at least 3 high-quality images to showcase your property.</p>
          </div>
        </div>
        
        <div class="inside-form-section">
          <h1 class="form-section-title">Images</h1>

        <div class="form-input">
          <label for="images" class="inputs-label">Upload Images</label>
          <div class="file-upload-container">
            <input
              type="file"
              id="images"
              name="images[]"
              accept="image/*"
              multiple
              class="file-input"
              aria-describedby="images_help"
              value="{{ old('images') }}"
            >
            <label for="images" class="file-label">
              <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
              Choose Images
            </label>
            <div id="images_help" class="file-info">
              Add at least 4 clear photos (cover, living room, bedrooms, bathrooms). PNG or JPEG recommended.
            </div>
            <div id="image-previews" class="image-previews" aria-live="polite"></div>
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
        <button type="submit" class="wizard-btn wizard-submit" id="wizardSubmit" style="display:none;">Submit Listing</button>
      </div>
    </div>
  </form>
</section>
@endsection

@push('scripts')
{{-- Google Maps API and Map Initialization --}}
<script src="{{ asset('js/MapClickService.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_browser_key') }}&callback=initPropertyLocationMap&libraries=places"></script>
<script src="{{ asset('js/list-property.js') }}"></script>
@endpush

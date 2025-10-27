@extends('layouts.app')

@section('title', 'Edit Property')
@section('content')
@php
  $backgroundImage = \App\Services\PropertyImageService::getImageAssetUrl($property);
@endphp
<link rel="stylesheet" href="{{ asset('css/list-property.css') }}">
<script src="{{ asset('js/list-property.js') }}"></script>
<section class="title-section" style="background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url('{{ $backgroundImage }}') center/cover;">
  <div class="content-title">
    <h1>Edit Property</h1>
    <p>Update your property details</p>
  </div>
</section>

<section class="content-section">
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
      {{-- ================= BASIC INFO ================= --}}
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
            <option value="" disabled {{ $currentType ? '' : 'selected' }}>Choose a property type...</option>
            @foreach($propertyOptions as $option)
              <option
                value="{{ $option['value'] }}"
                {{ $currentType && strcasecmp($currentType, $option['value']) === 0 ? 'selected' : '' }}
              >
                {{ $option['label'] }}
              </option>
            @endforeach
          </select>
          <small>Select the closest fit.</small>
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

      {{-- ================= LOCATION ================= --}}
      <div class="inside-form-section">
        <h1 class="form-section-title">Location</h1>

        @php
          $countryValue = old('country', $property->country);
        @endphp
        <div class="form-input">
          <label for="country" class="inputs-label">Country</label>
          <select
            id="country"
            name="country"
            placeholder="Select country"
            style="width: 100%;"
            data-placeholder="Search or select a country..."
            data-old-value="{{ $countryValue }}"
            aria-label="Country"
            required
          >
            <option value="">Select Country</option>
            @if($countryValue)
              <option value="{{ $countryValue }}" selected>{{ $countryValue }}</option>
            @endif
          </select>
          <small>Start typing to search your country.</small>
          @error('country') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-input">
          <label for="city" class="inputs-label">City</label>
          <input
            type="text"
            id="city"
            name="city"
            value="{{ old('city', $property->city) }}"
            placeholder="e.g. Beirut"
            autocomplete="address-level2"
            required
          >
          @error('city') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-input">
          <label for="address" class="inputs-label">Address</label>
          <input
            type="text"
            id="address"
            name="address"
            value="{{ old('address', $property->address) }}"
            placeholder="Street, building, floor, apartment number"
            autocomplete="street-address"
            required
          >
          <small>Do not include sensitive info you do not want public.</small>
          @error('address') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>

      {{-- ================= DETAILS ================= --}}
      <div class="inside-form-section">
        <h1 class="form-section-title">Details</h1>

        <div class="form-input">
          <label for="price" class="inputs-label">Price</label>
          <label for="unit">Unit</label>
          <div>
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
          </div>
          <small>Enter a numeric value only (currency handled elsewhere).</small>
          @error('price') <small class="text-danger">{{ $message }}</small> @enderror
          @error('unit') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

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
          <label for="room_nb" class="inputs-label">Number of Rooms</label>
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
          <label for="bathroom_nb" class="inputs-label">Number of Bathrooms</label>
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
          <label for="bedroom_nb" class="inputs-label">Number of Bedrooms</label>
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

      {{-- ================= IMAGES ================= --}}
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

      {{-- ================= SUBMIT ================= --}}
      <div class="form-control">
        <button type="submit" aria-label="Update your listing">Update Property</button>
      </div>
    </div>
  </form>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Edit Property')
@section('content')
<link rel="stylesheet" href="{{ asset('css/list-property.css') }}">
<script src="{{ asset('js/list-property.js') }}"></script>
<section class="title-section">
    <div class="content-title">
        <h1>Edit Property</h1>
        <p>Update your property details</p>
    </div>
</section>

<section class="content-section">
    {{-- Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="listing-form" method="POST" action="{{ route('properties.update', $property->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-content">

            {{-- BASIC INFO --}}
            <div class="inside-form-section">
                <h1 class="form-section-title">Basic Info</h1>

                <div class="form-input">
                    <label for="title" class="inputs-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        placeholder="e.g., Bright 2BR apartment with sea view"
                        value="{{ old('title', $property->title) }}"
                        required
                    >
                </div>

                <div class="form-input">
                    <label for="description" class="inputs-label">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Describe your place, the neighborhood, nearby services, and any special rules…"
                        required
                    >{{ old('description', $property->description) }}</textarea>
                </div>

                <div class="form-input">
                    <label for="property_type" class="inputs-label">Property Type</label>
                    <select name="property_type" id="property_type" required>
                        @php
                            $types = ['Apartment','House','Villa','Dorm','Other'];
                            $currentType = old('property_type', $property->property_type);
                        @endphp
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- DETAILS --}}
            <div class="inside-form-section">
                <h1 class="form-section-title">Details</h1>

                <div class="form-input">
                    <label for="price" class="inputs-label">Price</label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        placeholder="e.g., 750"
                        value="{{ old('price', $property->price) }}"
                        min="0" step="0.01" required
                    >
                    <small>Enter the total price in your platform’s currency.</small>
                </div>

                <div class="form-input">
                    <label for="area_m3" class="inputs-label">Area (m²)</label>
                    <input
                        type="number"
                        name="area_m3"
                        id="area_m3"
                        placeholder="e.g., 120"
                        value="{{ old('area_m3', $property->area_m3) }}"
                        min="0" step="0.1" required
                    >
                </div>

                <div class="form-input">
                    <label for="room_nb" class="inputs-label">Number of Rooms</label>
                    <input
                        type="number"
                        name="room_nb"
                        id="room_nb"
                        placeholder="e.g., 5"
                        value="{{ old('room_nb', $property->room_nb) }}"
                        min="0" required
                    >
                </div>

                <div class="form-input">
                    <label for="bedroom_nb" class="inputs-label">Number of Bedrooms</label>
                    <input
                        type="number"
                        name="bedroom_nb"
                        id="bedroom_nb"
                        placeholder="e.g., 3"
                        value="{{ old('bedroom_nb', $property->bedroom_nb) }}"
                        min="0" required
                    >
                </div>

                <div class="form-input">
                    <label for="bathroom_nb" class="inputs-label">Number of Bathrooms</label>
                    <input
                        type="number"
                        name="bathroom_nb"
                        id="bathroom_nb"
                        placeholder="e.g., 2"
                        value="{{ old('bathroom_nb', $property->bathroom_nb) }}"
                        min="0" required
                    >
                </div>

                {{-- AMENITIES --}}
                <div class="form-input amenities-group">
                    <h4 class="checkbox-label">Amenities</h4>
                    <div class="amenities-grid">
                        @php
                            $selectedAmenities = collect(old('amenities', $property->amenities->pluck('id')->toArray()))->map(fn($v)=>(int)$v)->all();
                        @endphp
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
                </div>

                {{-- RULES --}}
                <div class="form-input rules-group">
                    <h4 class="checkbox-label">Rules</h4>
                    <div class="rule-grid">
                        @php
                            $selectedRules = collect(old('rules', $property->rules->pluck('id')->toArray()))->map(fn($v)=>(int)$v)->all();
                        @endphp
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
                </div>
            </div>

            {{-- IMAGES --}}
            <div class="inside-form-section">
                <h1 class="form-section-title">Images</h1>

                {{-- Existing images (read-only preview) --}}
                @if($property->images->count())
                    <div class="form-input" style="margin-bottom:8px;">
                        <label class="inputs-label">Current Images</label>
                        <div class="property-images-grid">
                            @foreach($property->images as $img)
                                <img class="property-image" src="{{ Storage::disk('public')->url($img->path) }}" alt="Property image">
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-input">
                    <label for="images" class="inputs-label">Add More Images</label>
                    <div class="file-upload-container">
                        <input
                            type="file"
                            name="images[]"
                            id="images"
                            accept="image/*"
                            multiple
                            class="file-input"
                        >
                        <label for="images" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Choose Images
                        </label>
                        <div class="file-info">You can select multiple images (JPG, PNG, GIF, WebP). Max 2MB each.</div>
                    </div>

                    {{-- Live preview container for newly picked files --}}
                    <div id="imagePreview" class="thumbs-wrap" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(90px,1fr));gap:10px;margin-top:10px;"></div>
                </div>
            </div>

            <div class="form-control">
                <button type="submit">Update Property</button>
            </div>
        </div>
    </form>
</section>
@endsection

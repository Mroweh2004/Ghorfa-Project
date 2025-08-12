@extends('layouts/app')

@section('content')
<link rel="stylesheet" href="{{asset('css/list-property.css')}}">

<section class="title-section">
    <div class="content-title">
        <h1>Edit Property</h1>
        <p>Update your property details</p>
    </div>
</section>

<section class="content-section">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form class="listing-form" method="POST" action="{{ route('properties.update', $property->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-content">
            <div class="inside-form-section">
                <h1 class="form-section-title">Basic Info</h1>
                <div class="form-input">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $property->title) }}" required>
                </div>

                <div class="form-input">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" required>{{ old('description', $property->description) }}</textarea>
                </div>

                <div class="form-input">
                    <label for="property_type">Property Type</label>
                    <select name="property_type" id="property_type" required>
                        <option value="Apartment" {{ $property->property_type == 'Apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="House" {{ $property->property_type == 'House' ? 'selected' : '' }}>House</option>
                        <option value="Villa" {{ $property->property_type == 'Villa' ? 'selected' : '' }}>Villa</option>
                    </select>
                </div>
            </div>

            <div class="inside-form-section">
                <h1 class="form-section-title">Details</h1>
                <div class="form-input">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $property->price) }}" min="0" step="0.01" required>
                </div>
                <div class="form-input">
                    <label for="area_m3">Area (m²)</label>
                    <input type="number" name="area_m3" id="area_m3" value="{{ old('area_m3', $property->area_m3) }}" min="0" step="0.1" required>
                </div>
                <div class="form-input">
                    <label for="room_nb">Number of Rooms</label>
                    <input type="number" name="room_nb" id="room_nb" value="{{ old('room_nb', $property->room_nb) }}" min="0" required>
                </div>
                <div class="form-input">
                    <label for="bedroom_nb">Number of Bedrooms</label>
                    <input type="number" name="bedroom_nb" id="bedroom_nb" value="{{ old('bedroom_nb', $property->bedroom_nb) }}" min="0" required>
                </div>
                <div class="form-input">
                    <label for="bathroom_nb">Number of Bathrooms</label>
                    <input type="number" name="bathroom_nb" id="bathroom_nb" value="{{ old('bathroom_nb', $property->bathroom_nb) }}" min="0" required>
                </div>
            </div>

            <div class="inside-form-section">
                <h1 class="form-section-title">Images</h1>
                <div class="form-input">
                    <label for="images">Upload Images</label>
                    <div class="file-upload-container">
                        <input type="file" name="images[]" id="images" accept="image/*" multiple class="file-input">
                        <label for="images" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Choose Images
                        </label>
                        <div class="file-info">You can select multiple images</div>
                    </div>
                </div>
            </div>

            <div class="form-control">
                <button type="submit">Update Property</button>
            </div>
        </div>
    </form>
</section>
@endsection 
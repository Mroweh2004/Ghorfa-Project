@extends('layouts.app')
@section('title', 'list-space')
@section('content')
<link rel="stylesheet" href="{{asset('css/list-property.css')}}">

<section class="title-section">
    <div class="content-title">
        <h1>List Your Space</h1>
        <p>Specify your property details properly</p>
    </div>
</section>

<section class="content-section">
    <form class="listing-form" method="POST" action="{{ route('submit-listing') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-content">
            <div class="inside-form-section">
                <h1 class="form-section-title">Basic Info</h1>
                <div class="form-input">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" placeholder="ex: Great apartment with special look at the sea" required>
                </div>

                <div class="form-input">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" placeholder="..." required></textarea>
                </div>

                <div class="form-input">
                    <label for="property_type">Property Type</label>
                    <select name="property_type" id="property_type" required>
                        <option value="">Select Type</option>
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="dorm">Dorm</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-input">
                    <label for="listing_type">Listing Type</label>
                    <select name="listing_type" id="listing_type" required>
                        <option value="">Select Type</option>
                        <option value="rent">For Rent</option>
                        <option value="sale">For Sale</option>
                    </select>
                </div>
            </div>
            <div class="inside-form-section">
                <h1 class="form-section-title">Location</h1>
                <div class="form-input">
                    <label for="country">Country</label>
                    <select name="country" id="country" placeholder="Country" required></select>
                </div>
                <div class="form-input">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" placeholder="City" required>
                </div>
                <div class="form-input">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" placeholder="ex: Street, Neighbourhood, Apartment Name" required>
                </div>
            </div>   
            <div class="inside-form-section">
                <h1 class="form-section-title">Details</h1>
                <div class="form-input">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" placeholder="Price" min="0" step="0.01" required>
                </div>
                <div class="form-input">
                    <label for="area_m3">Area (m²)</label>
                    <input type="number" name="area_m3" id="area_m3" placeholder="Area in m²" min="0" step="0.1" required>
                </div>
                <div class="form-input">
                    <label for="room_nb">Number of Rooms</label>
                    <input type="number" name="room_nb" id="room_nb" placeholder="Number of Rooms" min="0" required>
                </div>
                <div class="form-input">
                    <label for="bathroom_nb">Number of Bathrooms</label>
                    <input type="number" name="bathroom_nb" id="bathroom_nb" placeholder="Number of Bathrooms" min="0" required>
                </div>
                <div class="form-input">
                    <label for="bedroom_nb">Number of Bedrooms</label>
                    <input type="number" name="bedroom_nb" id="bedroom_nb" placeholder="Number of Bedrooms" min="0" required>
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
                <button type="submit">Submit Listing</button>
            </div>
        </div>
    </form>
</section>
<script src="{{asset('js/list-property.js')}}"></script>
@endsection

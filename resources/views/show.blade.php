@extends('layouts.app')
@section('title', $property->title)
@section('content')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="{{ asset('js/show.js') }}" defer></script>

<main class="property-show-page">
    <!-- Hero Section with Main Image -->
    <section class="property-hero">
        <div class="hero-image-container">
            @if($property->images->count() > 0)
                <img src="{{ Storage::url($property->images->first()->path) }}" alt="{{ $property->title }}" class="hero-image">
                <div class="image-overlay">
                    <div class="property-badge">
                        <span class="badge-text">{{ $property->listing_type }}</span>
                    </div>
                    <div class="image-counter">
                        <i class="fas fa-images"></i>
                        <span>{{ $property->images->count() }} photos</span>
                    </div>
                </div>
            @else
                <div class="no-image-placeholder">
                    <i class="fas fa-home"></i>
                    <p>No images available</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Property Details Section -->
    <section class="property-details">
        <div class="container">
            <div class="property-layout">
                <!-- Main Content -->
                <div class="property-main">
                    <!-- Header -->
                    <div class="property-header">
                        <div class="property-title-section">
                            <h1 class="property-title">{{ $property->title }}</h1>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $property->address }}, {{ $property->city }}, {{ $property->country }}</span>
                            </div>
                        </div>
                        <div class="property-price-section">
                            <div class="price-main">${{ number_format($property->price) }}</div>
                            <div class="price-period">per month</div>
                        </div>
                    </div>

                    <!-- Property Features -->
                    <div class="property-features">
                        <div class="feature-item">
                            <i class="fas fa-home"></i>
                            <span>{{ $property->property_type }}</span>
                        </div>
                        @if($property->area_m3)
                        <div class="feature-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>{{ $property->area_m3 }} m²</span>
                        </div>
                        @endif
                        <div class="feature-item">
                            <i class="fas fa-door-open"></i>
                            <span>{{ $property->room_nb }} rooms</span>
                        </div>
                        @if($property->bedroom_nb)
                        <div class="feature-item">
                            <i class="fas fa-bed"></i>
                            <span>{{ $property->bedroom_nb }} bedrooms</span>
                        </div>
                        @endif
                        @if($property->bathroom_nb)
                        <div class="feature-item">
                            <i class="fas fa-bath"></i>
                            <span>{{ $property->bathroom_nb }} bathrooms</span>
                        </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="property-description">
                        <h3>Description</h3>
                        <p>{{ $property->description }}</p>
                    </div>

                    <!-- Image Gallery -->
                    @if($property->images->count() > 1)
                    <div class="property-gallery">
                        <h3>Photos</h3>
                        <div class="gallery-grid">
                            @foreach($property->images as $image)
                                <div class="gallery-item" onclick="openImageModal('{{ Storage::url($image->path) }}')">
                                    <img src="{{ Storage::url($image->path) }}" alt="Property Image" class="gallery-image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="property-sidebar">
                    <!-- Contact Card -->
                    <div class="contact-card">
                        <div class="contact-header">
                            <h4>Contact Information</h4>
                        </div>
                        <div class="contact-actions">
                            <button class="contact-btn primary">
                                <i class="fas fa-phone"></i>
                                Call Now
                            </button>
                            <button class="contact-btn secondary">
                                <i class="fas fa-envelope"></i>
                                Send Message
                            </button>
                        </div>
                        <div class="property-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Listed {{ $property->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span>Property ID: #{{ $property->id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Like Button -->
                    @auth
                    <div class="like-section">
                        <button class="like-btn" data-property-id="{{ $property->id }}" data-liked="{{ $property->isLikedBy(auth()->id()) ? 'true' : 'false' }}">
                            <i class="fa-{{ $property->isLikedBy(auth()->id()) ? 'solid' : 'regular' }} fa-heart"></i>
                            <span>{{ $property->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}</span>
                        </button>
                        <div class="like-count">{{ $property->likedBy()->count() }} likes</div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="property-actions">
        <div class="container">
            <div class="actions-content">
                <button onclick="history.back()" class="action-btn back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Search
                </button>
                @auth
                    @if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id)
                        <a href="{{ route('properties.edit', $property) }}" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i>
                            Edit Property
                        </a>
                        <form class="delete-form" action="{{ route('properties.destroy', $property->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">
                                <i class="fas fa-trash"></i>
                                Delete Property
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</main>

<!-- Image Modal -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="close-btn" onclick="closeImageModal()">&times;</span>
        
        <!-- Navigation Arrows -->
        <button class="nav-arrow nav-arrow-left" onclick="previousImage()" id="prevBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="nav-arrow nav-arrow-right" onclick="nextImage()" id="nextBtn">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <!-- Image Container -->
        <div class="image-container">
            <img id="modalImage" src="" alt="Property Image">
        </div>
        
        <!-- Image Counter -->
        <div class="image-counter">
            <span id="currentImageIndex">1</span> / <span id="totalImages">1</span>
        </div>
        
        <!-- Thumbnail Strip -->
        <div class="thumbnail-strip" id="thumbnailStrip">
            <!-- Thumbnails will be populated by JavaScript -->
        </div>
    </div>
</div>


@endsection 
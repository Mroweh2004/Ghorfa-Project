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

                    <!-- Reviews Section -->
                    <div class="reviews-section">
                        <div class="reviews-header">
                            <h3>Reviews & Ratings</h3>
                            @if($reviewsCount > 0)
                                <div class="reviews-summary">
                                    <div class="overall-rating">
                                        <div class="rating-number">{{ $avgRating }}</div>
                                        <div class="rating-stars-large">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-star {{ $i <= floor($avgRating) ? 'fas' : ($i <= $avgRating ? 'fas half-star' : 'far') }}"></i>
                                            @endfor
                                        </div>
                                        <div class="rating-count">{{ $reviewsCount }} {{ \Illuminate\Support\Str::plural('review', $reviewsCount) }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($reviewsCount > 0)
                            <div class="reviews-list">
                                @foreach($property->reviews()->with('user')->latest()->take(5)->get() as $review)
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <div class="reviewer-avatar">
                                                    @if($review->user->profile_image)
                                                        <img src="{{ Storage::url($review->user->profile_image) }}" alt="{{ $review->user->name }}" class="avatar-image">
                                                    @else
                                                        <i class="fas fa-user"></i>
                                                    @endif
                                                </div>
                                                <div class="reviewer-details">
                                                    <div class="reviewer-name">{{ $review->user->name }}</div>
                                                    <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa-star {{ $i <= $review->rating ? 'fas' : 'far' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="review-content">
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($reviewsCount > 5)
                                    <div class="view-all-reviews">
                                        <button class="view-all-btn" onclick="showAllReviews()">
                                            View all {{ $reviewsCount }} reviews
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="no-reviews">
                                <div class="no-reviews-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4>No reviews yet</h4>
                                <p>Be the first to share your experience with this property!</p>
                            </div>
                        @endif
                    </div>
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
                        <button
                            class="like-btn"
                            data-property-id="{{ $property->id }}"
                            data-liked="{{ $property->isLikedBy(auth()->id()) ? 'true' : 'false' }}"
                        >
                            <i class="fa-{{ $property->isLikedBy(auth()->id()) ? 'solid' : 'regular' }} fa-heart"></i>
                            <span>{{ $property->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}</span>
                        </button>
                        <div class="like-count">{{ $property->likedBy()->count() }} likes</div>

                        <hr class="like-divider">

                        @php
                            $userHasReviewed = auth()->check() ? $property->hasUserReviewed(auth()->id()) : false;
                            $userReview = auth()->check() ? $property->getUserReview(auth()->id()) : null;
                        @endphp

                        <!-- Rating summary -->
                        <div class="rating-summary">
                            <div class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-star {{ $i <= floor($avgRating) ? 'fas' : 'far' }}"></i>
                                @endfor
                            </div>
                            @if($reviewsCount > 0)
                                <span class="rating-text">{{ $avgRating }} / 5 · {{ $reviewsCount }} {{ \Illuminate\Support\Str::plural('review', $reviewsCount) }}</span>
                            @else
                                <span class="rating-text">No ratings yet</span>
                            @endif
                        </div>

                        <!-- Review button -->
                        @if($userHasReviewed && $userReview)
                            <div class="user-review-status">
                                <div class="user-review-info">
                                    <i class="fas fa-check-circle"></i>
                                    <span>You reviewed this property</span>
                                </div>
                                <button class="review-btn edit-review-btn" data-review-id="{{ $userReview->id }}" onclick="editUserReview(this.dataset.reviewId)">
                                    <i class="fas fa-edit"></i>
                                    Edit Review
                                </button>
                            </div>
                        @else
                            <button class="review-btn" onclick="openReviewModal()">
                                <i class="fas fa-star-half-alt"></i>
                                Write a Review
                            </button>
                        @endif
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
<!-- Review Modal -->
<div id="reviewModal" class="image-modal review-modal" onclick="closeReviewModal()">
    <div class="modal-content modal-card" onclick="event.stopPropagation()">
        <span class="close-btn" onclick="closeReviewModal()">&times;</span>

        <div class="review-modal-header">
            <h3>Rate this property</h3>
            <p class="review-subtitle">Share your experience to help others</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('reviews.store', $property) }}" class="review-form">
            @csrf

            <div class="form-row">
                <label for="rating">Your Rating</label>
                <div class="star-rating-container">
                    <div class="star-rating" id="starRating">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                            <label for="star{{ $i }}" class="star-label">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                    <div class="rating-text-display" id="ratingText">Select a rating</div>
                </div>
                @error('rating') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <label for="comment">Your Review</label>
                <textarea id="comment" name="comment" rows="6" placeholder="Tell others about your experience with this property..." required>{{ old('comment') }}</textarea>
                <div class="char-counter">
                    <span id="charCount">0</span>/1000 characters
                </div>
                @error('comment') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="review-actions">
                <button type="submit" class="submit-btn" id="submitReviewBtn">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
                <button type="button" class="cancel-btn" onclick="closeReviewModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReviewModal(){ document.getElementById('reviewModal').style.display = 'flex'; }
    function closeReviewModal(){ document.getElementById('reviewModal').style.display = 'none'; }
    window.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeReviewModal(); });
</script>



@endsection 
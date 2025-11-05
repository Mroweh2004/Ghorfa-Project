<?php $__env->startSection('title', $property->title); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/show.css')); ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="<?php echo e(asset('js/show.js')); ?>" defer></script>

<main class="property-show-page">
    <!-- Hero Section with Main Image -->
    <section class="property-hero">
        <div class="hero-image-container">
            <?php
                $heroImage = \App\Services\PropertyImageService::getImageUrl($property);
            ?>
            <img src="<?php echo e($heroImage); ?>" alt="<?php echo e($property->title); ?>" class="hero-image">
            <div class="image-overlay">
                <div class="property-badge">
                    <span class="badge-text"><?php echo e($property->listing_type); ?></span>
                </div>
                <div class="image-counter">
                    <i class="fas fa-images"></i>
                    <span><?php echo e($property->images->count()); ?> photos</span>
                </div>
            </div>
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
                            <h1 class="property-title"><?php echo e($property->title); ?></h1>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo e($property->address); ?>, <?php echo e($property->city); ?>, <?php echo e($property->country); ?></span>
                            </div>
                        </div>
                        <div class="property-price-section">
                            <div class="price-main">$<?php echo e(number_format($property->price)); ?></div>
                            <div class="price-period">per month</div>
                        </div>
                    </div>

                    <!-- Property Location Map -->
                    <?php if($property->latitude && $property->longitude): ?>
                    <div class="property-location-map-container">
                        <div id="property-location-map" style="width: 100%; height: 400px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"></div>
                    </div>
                    <?php endif; ?>

                    <!-- Property Features -->
                    <div class="property-features">
                        <div class="feature-item">
                            <i class="fas fa-home"></i>
                            <span><?php echo e($property->property_type); ?></span>
                        </div>
                        <?php if($property->area_m3): ?>
                        <div class="feature-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span><?php echo e($property->area_m3); ?> m²</span>
                        </div>
                        <?php endif; ?>
                        <div class="feature-item">
                            <i class="fas fa-door-open"></i>
                            <span><?php echo e($property->room_nb); ?> rooms</span>
                        </div>
                        <?php if($property->bedroom_nb): ?>
                        <div class="feature-item">
                            <i class="fas fa-bed"></i>
                            <span><?php echo e($property->bedroom_nb); ?> bedrooms</span>
                        </div>
                        <?php endif; ?>
                        <?php if($property->bathroom_nb): ?>
                        <div class="feature-item">
                            <i class="fas fa-bath"></i>
                            <span><?php echo e($property->bathroom_nb); ?> bathrooms</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="property-description">
                        <h3>Description</h3>
                        <p><?php echo e($property->description); ?></p>
                    </div>

                    <!-- Image Gallery -->
                    <?php if($property->images->count() > 1): ?>
                    <div class="property-gallery">
                        <h3>Photos</h3>
                        <div class="gallery-grid">
                            <?php $__currentLoopData = $property->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="gallery-item" onclick="openImageModal('<?php echo e(Storage::url($image->path)); ?>')">
                                    <img src="<?php echo e(Storage::url($image->path)); ?>" alt="Property Image" class="gallery-image">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Reviews Section -->
                    <div class="reviews-section">
                        <div class="reviews-header">
                            <h3>Reviews & Ratings</h3>
                            <?php if($reviewsCount > 0): ?>
                                <div class="reviews-summary">
                                    <div class="overall-rating">
                                        <div class="rating-number"><?php echo e($avgRating); ?></div>
                                        <div class="rating-stars-large">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa-star <?php echo e($i <= floor($avgRating) ? 'fas' : ($i <= $avgRating ? 'fas half-star' : 'far')); ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="rating-count"><?php echo e($reviewsCount); ?> <?php echo e(\Illuminate\Support\Str::plural('review', $reviewsCount)); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if($reviewsCount > 0): ?>
                            <div class="reviews-list">
                                <?php $__currentLoopData = $property->reviews()->with('user')->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <div class="reviewer-avatar">
                                                    <?php if($review->user->profile_image): ?>
                                                        <img src="<?php echo e(Storage::url($review->user->profile_image)); ?>" alt="<?php echo e($review->user->name); ?>" class="avatar-image">
                                                    <?php else: ?>
                                                        <i class="fas fa-user"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="reviewer-details">
                                                    <div class="reviewer-name"><?php echo e($review->user->name); ?></div>
                                                    <div class="review-date"><?php echo e($review->created_at->format('M d, Y')); ?></div>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa-star <?php echo e($i <= $review->rating ? 'fas' : 'far'); ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="review-content">
                                            <p><?php echo e($review->comment); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php if($reviewsCount > 5): ?>
                                    <div class="view-all-reviews">
                                        <button class="view-all-btn" onclick="showAllReviews()">
                                            View all <?php echo e($reviewsCount); ?> reviews
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-reviews">
                                <div class="no-reviews-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4>No reviews yet</h4>
                                <p>Be the first to share your experience with this property!</p>
                            </div>
                        <?php endif; ?>
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
                                <span>Listed <?php echo e($property->created_at->diffForHumans()); ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span>Property ID: #<?php echo e($property->id); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Like Button -->
                    <?php if(auth()->guard()->check()): ?>
                    <div class="like-section">
                        <button
                            class="like-btn"
                            data-property-id="<?php echo e($property->id); ?>"
                            data-liked="<?php echo e($property->isLikedBy(auth()->id()) ? 'true' : 'false'); ?>"
                        >
                            <i class="fa-<?php echo e($property->isLikedBy(auth()->id()) ? 'solid' : 'regular'); ?> fa-heart"></i>
                            <span><?php echo e($property->isLikedBy(auth()->id()) ? 'Liked' : 'Like'); ?></span>
                        </button>
                        <div class="like-count"><?php echo e($property->likedBy()->count()); ?> likes</div>

                        <hr class="like-divider">

                        <?php
                            $userHasReviewed = auth()->check() ? $property->hasUserReviewed(auth()->id()) : false;
                            $userReview = auth()->check() ? $property->getUserReview(auth()->id()) : null;
                        ?>

                        <!-- Rating summary -->
                        <div class="rating-summary">
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-star <?php echo e($i <= floor($avgRating) ? 'fas' : 'far'); ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if($reviewsCount > 0): ?>
                                <span class="rating-text"><?php echo e($avgRating); ?> / 5 · <?php echo e($reviewsCount); ?> <?php echo e(\Illuminate\Support\Str::plural('review', $reviewsCount)); ?></span>
                            <?php else: ?>
                                <span class="rating-text">No ratings yet</span>
                            <?php endif; ?>
                        </div>

                        <!-- Review button -->
                        <?php if($userHasReviewed && $userReview): ?>
                            <div class="user-review-status">
                                <div class="user-review-info">
                                    <i class="fas fa-check-circle"></i>
                                    <span>You reviewed this property</span>
                                </div>
                                <button class="review-btn edit-review-btn" data-review-id="<?php echo e($userReview->id); ?>" onclick="editUserReview(this.dataset.reviewId)">
                                    <i class="fas fa-edit"></i>
                                    Edit Review
                                </button>
                            </div>
                        <?php else: ?>
                            <button class="review-btn" onclick="openReviewModal()">
                                <i class="fas fa-star-half-alt"></i>
                                Write a Review
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="property-actions">
        <div class="container">
            <div class="actions-content">
                <button class="action-btn back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </button>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id): ?>
                        <a href="<?php echo e(route('properties.edit', $property)); ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i>
                            Edit Property
                        </a>
                        <form class="delete-form" action="<?php echo e(route('properties.destroy', $property->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">
                                <i class="fas fa-trash"></i>
                                Delete Property
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
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

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('reviews.store', $property)); ?>" class="review-form">
            <?php echo csrf_field(); ?>

            <div class="form-row">
                <label for="rating">Your Rating</label>
                <div class="star-rating-container">
                    <div class="star-rating" id="starRating">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo e($i); ?>" name="rating" value="<?php echo e($i); ?>" <?php echo e(old('rating') == $i ? 'checked' : ''); ?>>
                            <label for="star<?php echo e($i); ?>" class="star-label">
                                <i class="fas fa-star"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <div class="rating-text-display" id="ratingText">Select a rating</div>
                </div>
                <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-row">
                <label for="comment">Your Review</label>
                <textarea id="comment" name="comment" rows="6" placeholder="Tell others about your experience with this property..." required><?php echo e(old('comment')); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span>/1000 characters
                </div>
                <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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


<?php if($property->latitude && $property->longitude): ?>
<script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google.maps_browser_key')); ?>&callback=initPropertyShowMap&libraries=places"></script>
<script>
function initPropertyShowMap() {
    const mapElement = document.getElementById('property-location-map');
    
    if (!mapElement) {
        return;
    }

    const propertyLat = parseFloat(<?php echo json_encode($property->latitude, 15, 512) ?>);
    const propertyLng = parseFloat(<?php echo json_encode($property->longitude, 15, 512) ?>);
    const propertyTitle = <?php echo json_encode($property->title, 15, 512) ?>;
    const propertyAddress = <?php echo json_encode($property->address, 15, 512) ?>;
    const propertyCity = <?php echo json_encode($property->city, 15, 512) ?>;
    const propertyCountry = <?php echo json_encode($property->country, 15, 512) ?>;
    const propertyPrice = <?php echo json_encode(number_format($property->price), 15, 512) ?>;
    const unitSymbol = <?php echo json_encode($property->unit ? $property->unit->symbol : '', 15, 512) ?>;

    // Initialize map centered on property location
    const propertyMap = new google.maps.Map(mapElement, {
        center: { lat: propertyLat, lng: propertyLng },
        zoom: 15,
        mapTypeId: 'roadmap',
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ]
    });

    // Add marker for property location
    const propertyMarker = new google.maps.Marker({
        position: { lat: propertyLat, lng: propertyLng },
        map: propertyMap,
        title: propertyTitle,
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(
                '<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">' +
                '<circle cx="20" cy="20" r="18" fill="#ef4444" stroke="white" stroke-width="3"/>' +
                '<text x="20" y="26" text-anchor="middle" fill="white" font-family="Arial" font-size="18" font-weight="bold">📍</text>' +
                '</svg>'
            ),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
        },
        animation: google.maps.Animation.DROP
    });

    // Create info window with property details
    const infoWindow = new google.maps.InfoWindow({
        content: '<div style="padding: 10px; min-width: 200px;">' +
            '<h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: bold; color: #1a1a1a;">' + propertyTitle + '</h3>' +
            '<div style="margin-bottom: 8px; color: #64748b; font-size: 14px;">' +
            '<i class="fas fa-map-marker-alt" style="color: #ef4444;"></i> ' +
            propertyAddress + ', ' + propertyCity + ', ' + propertyCountry +
            '</div>' +
            '<div style="color: #1a1a1a; font-size: 16px; font-weight: 600;">$' + propertyPrice + ' ' + unitSymbol + '</div>' +
            '</div>'
    });

    // Open info window on marker click
    propertyMarker.addListener('click', function() {
        infoWindow.open(propertyMap, propertyMarker);
    });

    // Open info window by default
    infoWindow.open(propertyMap, propertyMarker);
}
</script>
<?php endif; ?>

<script>
    function openReviewModal(){ document.getElementById('reviewModal').style.display = 'flex'; }
    function closeReviewModal(){ document.getElementById('reviewModal').style.display = 'none'; }
    window.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeReviewModal(); });
</script>



<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/show.blade.php ENDPATH**/ ?>
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
            <?php if($property->images->count() > 0): ?>
                <img src="<?php echo e(Storage::url($property->images->first()->path)); ?>" alt="<?php echo e($property->title); ?>" class="hero-image">
                <div class="image-overlay">
                    <div class="property-badge">
                        <span class="badge-text"><?php echo e($property->listing_type); ?></span>
                    </div>
                    <div class="image-counter">
                        <i class="fas fa-images"></i>
                        <span><?php echo e($property->images->count()); ?> photos</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-image-placeholder">
                    <i class="fas fa-home"></i>
                    <p>No images available</p>
                </div>
            <?php endif; ?>
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
                        <button class="like-btn" data-property-id="<?php echo e($property->id); ?>" data-liked="<?php echo e($property->isLikedBy(auth()->id()) ? 'true' : 'false'); ?>">
                            <i class="fa-<?php echo e($property->isLikedBy(auth()->id()) ? 'solid' : 'regular'); ?> fa-heart"></i>
                            <span><?php echo e($property->isLikedBy(auth()->id()) ? 'Liked' : 'Like'); ?></span>
                        </button>
                        <div class="like-count"><?php echo e($property->likedBy()->count()); ?> likes</div>
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
                <button onclick="history.back()" class="action-btn back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Search
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


<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/show.blade.php ENDPATH**/ ?>
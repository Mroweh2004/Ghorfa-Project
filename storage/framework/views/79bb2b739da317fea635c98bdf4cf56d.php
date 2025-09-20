

<?php $__env->startSection('content'); ?>
<script src="<?php echo e(asset('js/search.js')); ?>"></script>
<link rel="stylesheet" href="<?php echo e(asset('css/profile.css')); ?>">
<main>
<?php if(auth()->guard()->check()): ?>
<div class="results-header">
    <div class="results-count">
        <h2><?php echo e($properties->total()); ?> Rooms Found</h2>
    </div>
</div>
<div class="listing">
    <div class="listings-grid">
        <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="listing-card" data-price="<?php echo e($property->price); ?>" data-created="<?php echo e($property->created_at->timestamp); ?>">
                <div class="listing-image">
                    <img src="<?php echo e($property->images->first() ? Storage::url($property->images->first()->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267'); ?>" alt="<?php echo e($property->title); ?>">
                    <span class="listing-tag">For <?php echo e($property->listing_type); ?></span>
                    <button class="setting-btn"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                    <ul class="setting-list">
                        <li><a href="<?php echo e(route('properties.show', $property->id)); ?>">View</a></li>
                        <?php if(auth()->check()): ?>
                            <?php if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id): ?>
                                <li><a href="<?php echo e(route('properties.edit', $property->id)); ?>">Edit</a></li>
                                <li>
                                    <form action="<?php echo e(route('properties.destroy', $property->id)); ?>" method="POST" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">Delete</button>
                                    </form>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if(auth()->guard()->check()): ?>
                        <button 
                            class="favorite-btn like-btn" 
                            data-property-id="<?php echo e($property->id); ?>"
                            data-liked="<?php echo e($property->isLikedBy(auth()->id()) ? 'true' : 'false'); ?>"
                        >
                            <i class="fa-<?php echo e($property->isLikedBy(auth()->id()) ? 'solid' : 'regular'); ?> fa-heart"></i>
                        </button>
                        <span class="like-count" id="like-count-<?php echo e($property->id); ?>" style="display: none;"><?php echo e($property->likedBy()->count()); ?></span>
                    <?php else: ?>
                        <button class="favorite-btn" onclick="window.location.href='<?php echo e(route('login')); ?>'">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="listing-content">
                    <div class="listing-price"><?php echo e($property->price); ?>$/month</div>
                    <h3><?php echo e($property->title); ?></h3>
                    <p class="listing-location">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?php echo e($property->address); ?>, <?php echo e($property->city); ?>, <?php echo e($property->country); ?>

                    </p>
                    <div class="listing-features">
                        <span><i class="fas fa-home"></i> <?php echo e($property->property_type); ?></span>
                        <span><i class="fa-solid fa-person-shelter"></i> <?php echo e($property->room_nb); ?> Room</span>
                        <?php if($property->bedroom_nb): ?>
                            <span><i class="fas fa-bed"></i> <?php echo e($property->bedroom_nb); ?> Bedrooms</span>
                        <?php endif; ?>
                        <?php if($property->bathroom_nb): ?>
                            <span><i class="fas fa-bath"></i> <?php echo e($property->bathroom_nb); ?> Bathrooms</span>
                        <?php endif; ?>
                        <?php if($property->area_m3): ?>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo e($property->area_m3); ?>m²</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="listing-meta">
                        <span class="available-from">Listed <?php echo e($property->created_at->diffForHumans()); ?></span>
                        <a href="<?php echo e(route('properties.show', $property->id)); ?>" class="view-btn">View Details</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
        <div class="pagination">
            <?php if($properties->hasPages()): ?>
                <?php if($properties->onFirstPage()): ?>
                    <button class="pagination-btn" disabled>Previous</button>
                <?php else: ?>
                    <a href="<?php echo e($properties->previousPageUrl()); ?>" class="pagination-btn">Previous</a>
                <?php endif; ?>

                <?php $__currentLoopData = $properties->getUrlRange(1, $properties->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $properties->currentPage()): ?>
                        <button class="pagination-btn active"><?php echo e($page); ?></button>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="pagination-btn"><?php echo e($page); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($properties->hasMorePages()): ?>
                    <a href="<?php echo e($properties->nextPageUrl()); ?>" class="pagination-btn">Next</a>
                <?php else: ?>
                    <button class="pagination-btn" disabled>Next</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    </div>
</main>



<?php endif; ?>
<?php if(auth()->guard()->guest()): ?>
<h1 style="color:red;">Please login first!</h1>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/profile/properties.blade.php ENDPATH**/ ?>
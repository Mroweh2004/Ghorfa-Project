<?php $__env->startSection('title', 'Search'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/search.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/search.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="search-page">
        <!-- Mobile filter overlay -->
        <div class="filter-overlay"></div>
        
        <section class="search-filters">
            <button class="filter-close-btn" aria-label="Close filters">
                <i class="fas fa-times"></i>
            </button>
            <div class="filter-container">
                <form action="<?php echo e(route('filter-search')); ?>" method="GET">
                <div class="filter-group">
                    <h3>Location</h3>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" id="location" name="location" placeholder="Enter city or area..." value="<?php echo e(request('location')); ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Price Range</h3>
                    <div class="price-range">
                        <div class="search-input">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="number" id="min-price" name="min-price" placeholder="Min" min="0" value="<?php echo e(request('min-price')); ?>" oninput="validatePriceRange()">
                        </div>
                        <span>-</span>
                        <div class="search-input">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="number" id="max-price" name="max-price" placeholder="Max" min="0" value="<?php echo e(request('max-price')); ?>" oninput="validatePriceRange()">
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Property Type</h3>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="property_type[]" value="apartment" <?php echo e(is_array(request('property_type')) && in_array('apartment', request('property_type')) ? 'checked' : ''); ?>> Appartment
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="house" <?php echo e(is_array(request('property_type')) && in_array('house', request('property_type')) ? 'checked' : ''); ?>> House
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="dorm" <?php echo e(is_array(request('property_type')) && in_array('dorm', request('property_type')) ? 'checked' : ''); ?>> Dorm
                        </label>
                        <label>
                            <input type="checkbox" name="property_type[]" value="other" <?php echo e(is_array(request('property_type')) && in_array('other', request('property_type')) ? 'checked' : ''); ?>> Other
                        </label>
                    </div>
                </div>
                <div class="filter-group">
                    <h3>Listing Type</h3>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="listing_type[]" value="Rent" <?php echo e(is_array(request('listing_type')) && in_array('Rent', request('listing_type')) ? 'checked' : ''); ?>> For Rent
                        </label>
                        <label>
                            <input type="checkbox" name="listing_type[]" value="Sale" <?php echo e(is_array(request('listing_type')) && in_array('Sale', request('listing_type')) ? 'checked' : ''); ?>> For Sale
                        </label>
                    </div>
                </div>


                <div class="filter-group">
                    <h3>Amenities</h3>
                    <div class="checkbox-group">
                        <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label>
                            <input type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>" <?php echo e(is_array(request('amenities')) && in_array($amenity->id, request('amenities')) ? 'checked' : ''); ?>> <?php echo e($amenity->name); ?>

                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Rules</h3>
                    <div class="checkbox-group">
                        <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label>
                            <input type="checkbox" name="rules[]" value="<?php echo e($rule->name); ?>" <?php echo e(is_array(request('rules')) && in_array($rule->name, request('rules')) ? 'checked' : ''); ?>> <?php echo e($rule->name); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <button class="other-list-btn apply-filters">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </section>
        
        <section class="search-results">
            <button class="search-show-btn"><i class="fas fa-search"></i> Show Filters</button>
            <div class="results-header">
                <div class="results-count">
                    <h2><?php echo e($properties->total()); ?> Rooms Found</h2>
                    <p>in Lebanon</p>
                </div>
                <div class="results-sort">
                    <button class="filter-toggle-btn">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                    <select id="sort-options">
                        <option value="recommended">Recommended</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest</option>
                        <option value="latest">Latest</option>
                    </select>
                </div>
            </div>

            <div class="listings-grid">
                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="listing-card" data-price="<?php echo e($property->price); ?>" data-created="<?php echo e($property->created_at->timestamp); ?>" data-likes="<?php echo e($property->likedBy()->count()); ?>">
                    <div class="listing-image">
                        <img src="<?php echo e(\App\Services\PropertyImageService::getImageUrl($property)); ?>" alt="<?php echo e($property->title); ?>">
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
                            <button class="favorite-btn" data-login-url="<?php echo e(route('login')); ?>">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="listing-content">
                    <span class="available-from">Listed <?php echo e($property->created_at->diffForHumans()); ?></span>
                        <h3><?php echo e($property->title); ?></h3>
                        <p class="listing-location">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?php echo e($property->address); ?>

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
                        <div class="listing-price"><b>$<?php echo e($property->price); ?></b>/month</div>                       
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
        </section>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/search.blade.php ENDPATH**/ ?>
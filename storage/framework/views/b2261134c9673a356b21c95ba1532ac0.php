<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/map.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
<div class="container ">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Property Map</h1>
        <p class="text-gray-600">Explore properties on an interactive map</p>
    </div>

    <!-- Map Statistics -->
    <div class="map-stats">
        <div class="stats-number"><?php echo e($properties->count()); ?></div>
        <div class="stats-label">Properties Found</div>
    </div>

    <!-- Map Layout: Filters on Left, Map on Right -->
    <div class="map-layout">
        <!-- Search Filters - Left Side -->
    <div class="map-filters">
        <form method="GET" action="<?php echo e(route('map')); ?>" id="filterForm">
                <div class="filter-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo e($request->input('location')); ?>" placeholder="City, Country, or Address">
                </div>
                
                <div class="filter-group">
                    <label for="min_price">Min Price</label>
                    <input type="number" id="min_price" name="min_price" value="<?php echo e($request->input('min_price')); ?>" placeholder="Minimum price">
                </div>
                
                <div class="filter-group">
                    <label for="max_price">Max Price</label>
                    <input type="number" id="max_price" name="max_price" value="<?php echo e($request->input('max_price')); ?>" placeholder="Maximum price">
                </div>
                
                <div class="filter-group">
                    <label for="property_type">Property Type</label>
                    <select id="property_type" name="property_type[]" multiple>
                        <option value="apartment" <?php echo e(in_array('apartment', (array)$request->input('property_type', [])) ? 'selected' : ''); ?>>Apartment</option>
                        <option value="house" <?php echo e(in_array('house', (array)$request->input('property_type', [])) ? 'selected' : ''); ?>>House</option>
                        <option value="villa" <?php echo e(in_array('villa', (array)$request->input('property_type', [])) ? 'selected' : ''); ?>>Villa</option>
                        <option value="studio" <?php echo e(in_array('studio', (array)$request->input('property_type', [])) ? 'selected' : ''); ?>>Studio</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="listing_type">Listing Type</label>
                    <select id="listing_type" name="listing_type[]" multiple>
                        <option value="sale" <?php echo e(in_array('sale', (array)$request->input('listing_type', [])) ? 'selected' : ''); ?>>For Sale</option>
                        <option value="rent" <?php echo e(in_array('rent', (array)$request->input('listing_type', [])) ? 'selected' : ''); ?>>For Rent</option>
                    </select>
            </div>
            
                <div class="filter-actions">
                <button type="submit" class="btn-filter">Apply Filters</button>
                <button type="button" class="btn-clear" onclick="clearFilters()">Clear All</button>
            </div>
        </form>
    </div>

        <!-- Map Container - Right Side -->
    <div class="map-container">
        <div id="map"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/MapClickService.js')); ?>"></script>
<script src="<?php echo e(asset('js/map.js')); ?>"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google.maps_browser_key')); ?>&callback=initMap&libraries=places"></script>

<script>
    initializeProperties(<?php echo json_encode($properties, 15, 512) ?>);
    
    window.mapConfig = {
        reverseGeocodeEndpoint: '<?php echo e(route("map.reverse-geocode")); ?>',
        storageUrl: '<?php echo e(asset("storage")); ?>',
        placeholderUrl: '<?php echo e(asset("img/background.jpg")); ?>',
        mapRoute: '<?php echo e(route("map")); ?>'
    };

    setTimeout(function() {
        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API failed to load');
            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Google Maps API failed to load. Please check your API key and internet connection.</div>';
            }
        }
    }, 5000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/map.blade.php ENDPATH**/ ?>
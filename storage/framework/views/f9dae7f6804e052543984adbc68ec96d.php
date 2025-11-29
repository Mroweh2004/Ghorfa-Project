<?php $__env->startSection('title', 'list-space'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/list-property.css')); ?>">
<script src="<?php echo e(asset('js/MapClickService.js')); ?>"></script>
<section class="title-section">
  <div class="content-title">
    <h1>List Your Space</h1>
    <p>Specify your property details properly</p>
  </div>
</section>

<section class="content-section">
  <form class="listing-form" method="POST" action="<?php echo e(route('submit-listing')); ?>" enctype="multipart/form-data" novalidate>
    <?php echo csrf_field(); ?>
    
    
    <?php if($errors->any()): ?>
      <div class="alert alert-danger mb-4">
        <h4>Validation Errors:</h4>
        <ul>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="form-content">
      
      <div class="inside-form-section">
        <h1 class="form-section-title">Basic Info</h1>

        <div class="form-input">
          <label for="title" class="inputs-label">Title</label>
          <input
            type="text"
            id="title"
            name="title"
            value="<?php echo e(old('title')); ?>"
            placeholder="e.g. Sunny 2BR apartment with sea view"
            maxlength="120"
            autocomplete="organization-title"
            required
          >
          <small>Keep it short & descriptive (max 120 characters).</small>
          <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-input">
          <label for="description" class="inputs-label">Description</label>
          <textarea
            id="description"
            name="description"
            placeholder="Tell guests what makes this place special: layout, view, nearby landmarks, and any house highlights…"
            minlength="30"
            maxlength="1200"
            required
          ><?php echo e(old('description')); ?></textarea>
          <small>Be specific: floor, orientation, surroundings, and any rules worth knowing.</small>
          <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="row">
          <div class="form-input">
            <label for="property_type" class="inputs-label">Property Type</label>
            <select id="property_type" name="property_type" required>
              <option value="" disabled <?php echo e(old('property_type') ? '' : 'selected'); ?>>Choose a property type…</option>
              <option value="apartment" <?php echo e(old('property_type') === 'apartment' ? 'selected' : ''); ?>>Apartment</option>
              <option value="house"     <?php echo e(old('property_type') === 'house' ? 'selected' : ''); ?>>House</option>
              <option value="dorm"      <?php echo e(old('property_type') === 'dorm' ? 'selected' : ''); ?>>Dorm</option>
              <option value="other"     <?php echo e(old('property_type') === 'other' ? 'selected' : ''); ?>>Other</option>
            </select>
            <?php $__errorArgs = ['property_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-input">
            <label for="listing_type" class="inputs-label">Listing Type</label>
            <select id="listing_type" name="listing_type" required>
              <option value="" disabled <?php echo e(old('listing_type') ? '' : 'selected'); ?>>Is it for rent or for sale?</option>
              <option value="rent" <?php echo e(old('listing_type') === 'rent' ? 'selected' : ''); ?>>For Rent</option>
              <option value="sale" <?php echo e(old('listing_type') === 'sale' ? 'selected' : ''); ?>>For Sale</option>
            </select>
            <?php $__errorArgs = ['listing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
        </div>
      
      <div class="inside-form-section">
        <h1 class="form-section-title">Location</h1>
        
        <input type="hidden" id="latitude" name="latitude" value="<?php echo e(old('latitude')); ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo e(old('longitude')); ?>">

        
        <div class="form-input">
          <label class="inputs-label">Select Location on Map</label>
          <div style="position: relative;" data-reverse-geocode-endpoint="<?php echo e(route('map.reverse-geocode')); ?>">
            <div id="property-location-map"></div>
            <button type="button" id="enableMapClick" class="map-control-button">
              📍 Enable Map Click
            </button>
            <span id="coordinatesStatus" class="map-status-overlay"></span>
          </div>
          <small>Click on the map to set your property's exact location. This will automatically fill the coordinates.</small>
          <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
     

      
      <div class="details">
        <div class="inside-form-section">
          <h1 class="form-section-title">Details</h1>

          <div class="form-input">
            <label for="price" class="inputs-label">Price</label>
            <label for="unit">Unit</label>
            <div class="row">
              <input
                type="number"
                id="price"
                name="price"
                value="<?php echo e(old('price')); ?>"
                placeholder="e.g. 750 (monthly) or 145000 (sale)"
                inputmode="decimal"
                min="0"
                step="0.01"
                required
              >
              <select name="unit" id="unit">
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($unit->id); ?>" <?php echo e(old('unit') == $unit->id ? 'selected' : ''); ?>><?php echo e($unit->code); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <small>Enter a numeric value only (currency handled elsewhere).</small>
            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="row">
            <div class="form-input">
              <label for="area_m3" class="inputs-label">Area (m²)</label>
              <input
                type="number"
                id="area_m3"
                name="area_m3"
                value="<?php echo e(old('area_m3')); ?>"
                placeholder="e.g. 95"
                inputmode="decimal"
                min="0"
                step="0.1"
                required
              >
              <?php $__errorArgs = ['area_m3'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-input">
              <label for="room_nb" class="inputs-label">Rooms</label>
              <input
                type="number"
                id="room_nb"
                name="room_nb"
                value="<?php echo e(old('room_nb')); ?>"
                placeholder="e.g. 4"
                inputmode="numeric"
                min="0"
                required
              >
              <?php $__errorArgs = ['room_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-input">
              <label for="bathroom_nb" class="inputs-label">Bathrooms</label>
              <input
                type="number"
                id="bathroom_nb"
                name="bathroom_nb"
                value="<?php echo e(old('bathroom_nb')); ?>"
                placeholder="e.g. 2"
                inputmode="numeric"
                min="0"
                required
              >
              <?php $__errorArgs = ['bathroom_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-input">
              <label for="bedroom_nb" class="inputs-label">Bedrooms</label>
              <input
                type="number"
                id="bedroom_nb"
                name="bedroom_nb"
                value="<?php echo e(old('bedroom_nb')); ?>"
                placeholder="e.g. 3"
                inputmode="numeric"
                min="0"
                required
              >
              <?php $__errorArgs = ['bedroom_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        </div>

        
        <div class="form-input amenities-group">
          <h4 class="checkbox-label">Amenities</h4>
          <div class="amenities-grid">
            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="amenity">
                <input
                  type="checkbox"
                  name="amenities[]"
                  value="<?php echo e($amenity->id); ?>"
                  <?php echo e((is_array(old('amenities')) && in_array($amenity->id, old('amenities'))) || (is_array(request('amenities')) && in_array($amenity->id, request('amenities'))) ? 'checked' : ''); ?>

                >
                <span class="amenity-text"><?php echo e($amenity->name); ?></span>
              </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <small>Select all that apply (e.g., Wi-Fi, Parking, Elevator).</small>
        </div>

        
        <div class="form-input rules-group">
          <h4 class="checkbox-label">Rules</h4>
          <div class="rule-grid">
            <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="rule">
                <input
                  type="checkbox"
                  name="rules[]"
                  value="<?php echo e($rule->id); ?>"
                  <?php echo e((is_array(old('rules')) && in_array($rule->id, old('rules'))) || (is_array(request('rules')) && in_array($rule->id, request('rules'))) ? 'checked' : ''); ?>

                >
                <span class="rule-text"><?php echo e($rule->name); ?></span>
              </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <small>Common examples: No smoking, No pets, Quiet hours, ID required on check-in.</small>
        </div>
      </div>

      
      <div class="inside-form-section">
        <h1 class="form-section-title">Images</h1>

        <div class="form-input">
          <label for="images" class="inputs-label">Upload Images</label>
          <div class="file-upload-container">
            <input
              type="file"
              id="images"
              name="images[]"
              accept="image/*"
              multiple
              class="file-input"
              aria-describedby="images_help"
              value="<?php echo e(old('images')); ?>"
            >
            <label for="images" class="file-label">
              <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
              Choose Images
            </label>
            <div id="images_help" class="file-info">
              Add at least 4 clear photos (cover, living room, bedrooms, bathrooms). PNG or JPEG recommended.
            </div>
            <div id="image-previews" class="image-previews" aria-live="polite"></div>
            <?php if($errors->has('images')): ?>
              <div class="alert alert-danger mt-2">
                <small>Please upload at least one image.</small>
              </div>
            <?php endif; ?>
          </div>
          <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      
      <div class="form-control">
        <button type="submit" aria-label="Submit your listing">Submit Listing</button>
      </div>
    </div>
  </form>
</section>


<script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google.maps_browser_key')); ?>&callback=initPropertyLocationMap&libraries=places"></script>
<script src="<?php echo e(asset('js/list-property.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/list-property.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Edit Property'); ?>
<?php $__env->startSection('content'); ?>
<?php
  $backgroundImage = \App\Services\PropertyImageService::getImageAssetUrl($property);
?>
<link rel="stylesheet" href="<?php echo e(asset('css/list-property.css')); ?>">
<script src="<?php echo e(asset('js/list-property.js')); ?>"></script>
<section class="title-section" style="background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url('<?php echo e($backgroundImage); ?>') center/cover;">
  <div class="content-title">
    <h1>Edit Property</h1>
    <p>Update your property details</p>
  </div>
</section>

<section class="content-section">
  
  <?php if(session('error')): ?>
    <div class="alert alert-danger mb-4"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  <form
    class="listing-form"
    method="POST"
    action="<?php echo e(route('properties.update', $property->id)); ?>"
    enctype="multipart/form-data"
    novalidate
  >
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

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
            value="<?php echo e(old('title', $property->title)); ?>"
            placeholder="e.g. Sunny 2BR apartment with sea view"
            maxlength="120"
            autocomplete="organization-title"
            required
          >
          <small>Keep it short and descriptive (max 120 characters).</small>
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
            placeholder="Tell guests what makes this place special: layout, view, nearby landmarks, and any house highlights..."
            minlength="30"
            maxlength="1200"
            required
          ><?php echo e(old('description', $property->description)); ?></textarea>
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

        <div class="form-input">
          <label for="property_type" class="inputs-label">Property Type</label>
          <?php
            $propertyOptions = [
              ['value' => 'apartment', 'label' => 'Apartment'],
              ['value' => 'house', 'label' => 'House'],
              ['value' => 'villa', 'label' => 'Villa'],
              ['value' => 'dorm', 'label' => 'Dorm'],
              ['value' => 'other', 'label' => 'Other'],
            ];
            $currentType = old('property_type', $property->property_type);
          ?>
          <select id="property_type" name="property_type" required>
            <option value="" disabled <?php echo e($currentType ? '' : 'selected'); ?>>Choose a property type...</option>
            <?php $__currentLoopData = $propertyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option
                value="<?php echo e($option['value']); ?>"
                <?php echo e($currentType && strcasecmp($currentType, $option['value']) === 0 ? 'selected' : ''); ?>

              >
                <?php echo e($option['label']); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <small>Select the closest fit.</small>
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
          <?php
            $currentListing = old('listing_type', $property->listing_type);
          ?>
          <select id="listing_type" name="listing_type" required>
            <option value="" disabled <?php echo e($currentListing ? '' : 'selected'); ?>>Is it for rent or for sale?</option>
            <option value="rent" <?php echo e($currentListing && strcasecmp($currentListing, 'rent') === 0 ? 'selected' : ''); ?>>For Rent</option>
            <option value="sale" <?php echo e($currentListing && strcasecmp($currentListing, 'sale') === 0 ? 'selected' : ''); ?>>For Sale</option>
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

      
      <div class="inside-form-section">
        <h1 class="form-section-title">Location</h1>

        <?php
          $countryValue = old('country', $property->country);
        ?>
        <div class="form-input">
          <label for="country" class="inputs-label">Country</label>
          <select
            id="country"
            name="country"
            placeholder="Select country"
            style="width: 100%;"
            data-placeholder="Search or select a country..."
            data-old-value="<?php echo e($countryValue); ?>"
            aria-label="Country"
            required
          >
            <option value="">Select Country</option>
            <?php if($countryValue): ?>
              <option value="<?php echo e($countryValue); ?>" selected><?php echo e($countryValue); ?></option>
            <?php endif; ?>
          </select>
          <small>Start typing to search your country.</small>
          <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-input">
          <label for="city" class="inputs-label">City</label>
          <input
            type="text"
            id="city"
            name="city"
            value="<?php echo e(old('city', $property->city)); ?>"
            placeholder="e.g. Beirut"
            autocomplete="address-level2"
            required
          >
          <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-input">
          <label for="address" class="inputs-label">Address</label>
          <input
            type="text"
            id="address"
            name="address"
            value="<?php echo e(old('address', $property->address)); ?>"
            placeholder="Street, building, floor, apartment number"
            autocomplete="street-address"
            required
          >
          <small>Do not include sensitive info you do not want public.</small>
          <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      
      <div class="inside-form-section">
        <h1 class="form-section-title">Details</h1>

        <div class="form-input">
          <label for="price" class="inputs-label">Price</label>
          <label for="unit">Unit</label>
          <div>
            <input
              type="number"
              id="price"
              name="price"
              value="<?php echo e(old('price', $property->price)); ?>"
              placeholder="e.g. 750 (monthly) or 145000 (sale)"
              inputmode="decimal"
              min="0"
              step="0.01"
              required
            >
            <select name="unit" id="unit">
              <?php
                $selectedUnit = old('unit', $property->unit_id);
              ?>
              <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($unit->id); ?>" <?php echo e((string)$selectedUnit === (string)$unit->id ? 'selected' : ''); ?>>
                  <?php echo e($unit->code); ?>

                </option>
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
          <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-input">
          <label for="area_m3" class="inputs-label">Area (m²)</label>
          <input
            type="number"
            id="area_m3"
            name="area_m3"
            value="<?php echo e(old('area_m3', $property->area_m3)); ?>"
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
          <label for="room_nb" class="inputs-label">Number of Rooms</label>
          <input
            type="number"
            id="room_nb"
            name="room_nb"
            value="<?php echo e(old('room_nb', $property->room_nb)); ?>"
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
          <label for="bathroom_nb" class="inputs-label">Number of Bathrooms</label>
          <input
            type="number"
            id="bathroom_nb"
            name="bathroom_nb"
            value="<?php echo e(old('bathroom_nb', $property->bathroom_nb)); ?>"
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
          <label for="bedroom_nb" class="inputs-label">Number of Bedrooms</label>
          <input
            type="number"
            id="bedroom_nb"
            name="bedroom_nb"
            value="<?php echo e(old('bedroom_nb', $property->bedroom_nb)); ?>"
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

        
        <div class="form-input amenities-group">
          <h4 class="checkbox-label">Amenities</h4>
          <?php
            $selectedAmenities = collect(old('amenities', $property->amenities->pluck('id')->toArray()))
              ->map(fn($v) => (int)$v)
              ->all();
          ?>
          <div class="amenities-grid">
            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="amenity">
                <input
                  type="checkbox"
                  name="amenities[]"
                  value="<?php echo e($amenity->id); ?>"
                  <?php echo e(in_array($amenity->id, $selectedAmenities, true) ? 'checked' : ''); ?>

                >
                <span class="amenity-text"><?php echo e($amenity->name); ?></span>
              </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <small>Select all that apply (e.g., Wi-Fi, Parking, Elevator).</small>
        </div>

        
        <div class="form-input rules-group">
          <h4 class="checkbox-label">Rules</h4>
          <?php
            $selectedRules = collect(old('rules', $property->rules->pluck('id')->toArray()))
              ->map(fn($v) => (int)$v)
              ->all();
          ?>
          <div class="rule-grid">
            <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="rule">
                <input
                  type="checkbox"
                  name="rules[]"
                  value="<?php echo e($rule->id); ?>"
                  <?php echo e(in_array($rule->id, $selectedRules, true) ? 'checked' : ''); ?>

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

        <?php
          $removedImageIds = collect(old('remove_images', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

          $existingImages = $property->images->map(function ($img) use ($removedImageIds) {
              return [
                  'id'         => $img->id,
                  'url'        => Storage::url($img->path),
                  'name'       => basename($img->path),
                  'is_primary' => (bool) $img->is_primary,
                  'removed'    => in_array($img->id, $removedImageIds, true),
              ];
          })->values();
        ?>

        <div class="form-input">
          <label for="images" class="inputs-label">Manage Images</label>
          <div class="file-upload-container">
            <input
              type="file"
              id="images"
              name="images[]"
              accept="image/*"
              multiple
              class="file-input"
              aria-describedby="images_help"
            >
            <label for="images" class="file-label">
              <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
              Choose Images
            </label>
            <div id="images_help" class="file-info">
              Add new photos or remove existing ones. PNG or JPEG recommended.
            </div>
            <div
              id="image-previews"
              class="image-previews"
              aria-live="polite"
              data-existing-images='<?php echo json_encode($existingImages, 15, 512) ?>'
              data-remove-input-name="remove_images[]"
              data-remove-container-id="removed-images-container"
            ></div>
            <div
              id="removed-images-container"
              data-role="removed-images-container"
              style="display:none;"
            >
              <?php $__currentLoopData = $removedImageIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $removedId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="remove_images[]" value="<?php echo e($removedId); ?>">
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
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
        <button type="submit" aria-label="Update your listing">Update Property</button>
      </div>
    </div>
  </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/edit-property.blade.php ENDPATH**/ ?>
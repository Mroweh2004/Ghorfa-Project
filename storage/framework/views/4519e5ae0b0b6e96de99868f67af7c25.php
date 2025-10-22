

<?php $__env->startSection('title', 'Edit Property'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/list-property.css')); ?>">
<script src="<?php echo e(asset('js/list-property.js')); ?>"></script>
<section class="title-section">
    <div class="content-title">
        <h1>Edit Property</h1>
        <p>Update your property details</p>
    </div>
</section>

<section class="content-section">
    
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="listing-form" method="POST" action="<?php echo e(route('properties.update', $property->id)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="form-content">

            
            <div class="inside-form-section">
                <h1 class="form-section-title">Basic Info</h1>

                <div class="form-input">
                    <label for="title" class="inputs-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        placeholder="e.g., Bright 2BR apartment with sea view"
                        value="<?php echo e(old('title', $property->title)); ?>"
                        required
                    >
                </div>

                <div class="form-input">
                    <label for="description" class="inputs-label">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Describe your place, the neighborhood, nearby services, and any special rules…"
                        required
                    ><?php echo e(old('description', $property->description)); ?></textarea>
                </div>

                <div class="form-input">
                    <label for="property_type" class="inputs-label">Property Type</label>
                    <select name="property_type" id="property_type" required>
                        <?php
                            $types = ['Apartment','House','Villa','Dorm','Other'];
                            $currentType = old('property_type', $property->property_type);
                        ?>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type); ?>" <?php echo e($currentType === $type ? 'selected' : ''); ?>>
                                <?php echo e($type); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            
            <div class="inside-form-section">
                <h1 class="form-section-title">Details</h1>

                <div class="form-input">
                    <label for="price" class="inputs-label">Price</label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        placeholder="e.g., 750"
                        value="<?php echo e(old('price', $property->price)); ?>"
                        min="0" step="0.01" required
                    >
                    <small>Enter the total price in your platform’s currency.</small>
                </div>

                <div class="form-input">
                    <label for="area_m3" class="inputs-label">Area (m²)</label>
                    <input
                        type="number"
                        name="area_m3"
                        id="area_m3"
                        placeholder="e.g., 120"
                        value="<?php echo e(old('area_m3', $property->area_m3)); ?>"
                        min="0" step="0.1" required
                    >
                </div>

                <div class="form-input">
                    <label for="room_nb" class="inputs-label">Number of Rooms</label>
                    <input
                        type="number"
                        name="room_nb"
                        id="room_nb"
                        placeholder="e.g., 5"
                        value="<?php echo e(old('room_nb', $property->room_nb)); ?>"
                        min="0" required
                    >
                </div>

                <div class="form-input">
                    <label for="bedroom_nb" class="inputs-label">Number of Bedrooms</label>
                    <input
                        type="number"
                        name="bedroom_nb"
                        id="bedroom_nb"
                        placeholder="e.g., 3"
                        value="<?php echo e(old('bedroom_nb', $property->bedroom_nb)); ?>"
                        min="0" required
                    >
                </div>

                <div class="form-input">
                    <label for="bathroom_nb" class="inputs-label">Number of Bathrooms</label>
                    <input
                        type="number"
                        name="bathroom_nb"
                        id="bathroom_nb"
                        placeholder="e.g., 2"
                        value="<?php echo e(old('bathroom_nb', $property->bathroom_nb)); ?>"
                        min="0" required
                    >
                </div>

                
                <div class="form-input amenities-group">
                    <h4 class="checkbox-label">Amenities</h4>
                    <div class="amenities-grid">
                        <?php
                            $selectedAmenities = collect(old('amenities', $property->amenities->pluck('id')->toArray()))->map(fn($v)=>(int)$v)->all();
                        ?>
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
                </div>

                
                <div class="form-input rules-group">
                    <h4 class="checkbox-label">Rules</h4>
                    <div class="rule-grid">
                        <?php
                            $selectedRules = collect(old('rules', $property->rules->pluck('id')->toArray()))->map(fn($v)=>(int)$v)->all();
                        ?>
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
                </div>
            </div>

            
            <div class="inside-form-section">
                <h1 class="form-section-title">Images</h1>

                
                <?php if($property->images->count()): ?>
                    <div class="form-input" style="margin-bottom:8px;">
                        <label class="inputs-label">Current Images</label>
                        <div class="property-images-grid">
                            <?php $__currentLoopData = $property->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img class="property-image" src="<?php echo e(Storage::disk('public')->url($img->path)); ?>" alt="Property image">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-input">
                    <label for="images" class="inputs-label">Add More Images</label>
                    <div class="file-upload-container">
                        <input
                            type="file"
                            name="images[]"
                            id="images"
                            accept="image/*"
                            multiple
                            class="file-input"
                        >
                        <label for="images" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Choose Images
                        </label>
                        <div class="file-info">You can select multiple images (JPG, PNG, GIF, WebP). Max 2MB each.</div>
                    </div>

                    
                    <div id="imagePreview" class="thumbs-wrap" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(90px,1fr));gap:10px;margin-top:10px;"></div>
                </div>
            </div>

            <div class="form-control">
                <button type="submit">Update Property</button>
            </div>
        </div>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/edit-property.blade.php ENDPATH**/ ?>
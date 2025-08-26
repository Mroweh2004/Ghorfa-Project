
<?php $__env->startSection('title', $property->title); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/show.css')); ?>">

<div class="property-container">
    <h1><?php echo e($property->title); ?></h1>
    <div class="property-information">
        <strong>Type:</strong> <?php echo e($property->property_type); ?><br>
        <strong>Listing:</strong> <?php echo e($property->listing_type); ?><br>
        <strong>Price:</strong> $<?php echo e(number_format($property->price, 2)); ?><br>
        <strong>Address:</strong> <?php echo e($property->address); ?>, <?php echo e($property->city); ?>, <?php echo e($property->country); ?><br>
        <strong>Area:</strong> <?php echo e($property->area_m3); ?> m²<br>
        <strong>Rooms:</strong> <?php echo e($property->room_nb); ?><br>
        <strong>Bedrooms:</strong> <?php echo e($property->bedroom_nb); ?><br>
        <strong>Bathrooms:</strong> <?php echo e($property->bathroom_nb); ?><br>
        <strong>Description:</strong> <?php echo e($property->description); ?>

        <p>Posted <?php echo e($property->created_at->diffForHumans()); ?></p>
    </div>
    <div class="property-images-grid">
        <?php $__currentLoopData = $property->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <img src="<?php echo e(Storage::url($image->path)); ?>" class="property-image" alt="Property Image">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <div class="property-actions">
        <?php if(auth()->guard()->check()): ?>
            <button onclick="history.back()" class="back-button">Back</button>
            <?php if(auth()->user()->role === 'admin' || auth()->id() === $property->user_id): ?>
                <a href="<?php echo e(route('properties.edit', $property)); ?>" class="edit-btn">Edit Property</a>
                <form class="delete-form" action="<?php echo e(route('properties.destroy', $property->id)); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this property?')">Delete Property</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(auth()->guard()->guest()): ?>
            <button onclick="history.back()" class="back-button">Back</button>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/show.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="mobile-nav">
        <ul>
            <li><a href="<?php echo e(route('home')); ?>"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="<?php echo e(route('search')); ?>"><i class="fas fa-search"></i>Search</a></li>
            <li><a href="<?php echo e(route('map')); ?>"><i class="fas fa-map-marked-alt"></i>Map</a></li>
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'admin'): ?>
                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html><?php /**PATH C:\Ghorfa-Project\resources\views/partials/mobile-nav.blade.php ENDPATH**/ ?>
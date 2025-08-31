<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ghorfa</title>
    <script src="<?php echo e(asset('js/profile.js')); ?>"></script>
</head>
<body>

    <?php if(auth()->guard()->check()): ?> 
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Ghorfa Logo" width="50px">
                Ghorfa
            </div>
            <ul class="nav-links">
                <li><a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo e(route('search')); ?>" class="<?php echo e(request()->routeIs('search') ? 'active' : ''); ?>"><i class="fas fa-search"></i> Search</a></li>                
                <?php if(auth()->user()->role === 'admin'): ?>
                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-right">
                <button class="main-list-btn" onclick="location.href='<?php echo e(route('list-property')); ?>'"><i class="fas fa-plus"></i> List Your Space</button>
                <div id="profile-link">
                    <div class="nav-profile-image">
                        <?php if(Auth::user()->profile_image): ?>
                            <img src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" alt="Profile Image">       
                        <?php else: ?>
                            <img src="<?php echo e(asset('images/default-profile.png')); ?>" alt="Default Profile Image">
                        <?php endif; ?>
                    </div>
                    <img src="<?php echo e(asset( 'img/up.png')); ?>" alt="Up Arrow" class="up">
                    <img src="<?php echo e(asset('img/down.png')); ?>" alt="Down Arrow" class="down">
                </div>
            </div>
        </nav>
            <form method="post" class="profile-dropdown" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
                <a href="<?php echo e(route('profileInfo')); ?>" class="profile-dropdown-option">Profile</a>
                <a href="<?php echo e(route( 'profileProperties')); ?>" class="profile-dropdown-option">My Properties</a>
                <a href="<?php echo e(route(name: 'profileProperties')); ?>" class="profile-dropdown-option">My Favorites</a>
                <button type="submit" class="profile-dropdown-option"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
    </header>
    <?php endif; ?>

    <?php if(auth()->guard()->guest()): ?>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Ghorfa Logo" width="50px">
                Ghorfa
            </div>
            <ul class="nav-links">
                <li><a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo e(route('search')); ?>" class="<?php echo e(request()->routeIs('search') ? 'active' : ''); ?>"><i class="fas fa-search"></i> Search</a></li>
            </ul>
            <ul class="nav-links">
                <li><a href="<?php echo e(route('login')); ?>" class="<?php echo e(request()->routeIs('login') ? 'active' : ''); ?>">Login</a></li>
                <li><a href="<?php echo e(route('register')); ?>" class="<?php echo e(request()->routeIs('register') ? 'active' : ''); ?>">SignUp</a></li>
            </ul>
        </nav>
    </header>
    <?php endif; ?>

</body>
</html>
<?php /**PATH C:\Ghorfa-Project\resources\views/partials/nav.blade.php ENDPATH**/ ?>
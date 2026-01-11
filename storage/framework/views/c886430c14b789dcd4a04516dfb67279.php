<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ghorfa</title>
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
                <li><a href="<?php echo e(route('map')); ?>" class="<?php echo e(request()->routeIs('map') ? 'active' : ''); ?>"><i class="fas fa-map-marked-alt"></i> Map</a></li>                
                <?php if(auth()->user()->role === 'admin'): ?>
                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-right">
                <div class="notification-bell-container" id="notificationContainer">
                    <button class="notification-bell" id="notificationBell" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button class="mark-all-read-btn" id="markAllReadBtn">Mark all as read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-loading">Loading notifications...</div>
                        </div>
                        <div class="notification-footer">
                            <a href="#" id="viewAllNotifications">View all notifications</a>
                        </div>
                    </div>
                </div>
                <?php if(auth()->user()->isLandlord() || auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('list-property')); ?>" class="main-list-btn"><i class="fas fa-plus"></i> List Your Space</a>
                <?php endif; ?>
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
                <a href="<?php echo e(route('profileInfo')); ?>" class="profile-dropdown-option"><i class="fas fa-user"></i> Profile</a>
                <a href="<?php echo e(route( 'profileFavorites')); ?>" class="profile-dropdown-option"><i class="fas fa-heart"></i> My Favorites</a>
                <?php if(auth()->user()->canBecomeLandlord()): ?>
                <a href="<?php echo e(route('landlord.apply')); ?>" class="profile-dropdown-option"><i class="fas fa-building"></i> Become a Landlord</a>
                <?php endif; ?>
                <?php if(auth()->user()->isLandlord()): ?>
                <a href="<?php echo e(route('landlord.dashboard')); ?>" class="profile-dropdown-option"><i class="fas fa-building"></i> Landlord Dashboard</a>
                <?php endif; ?>
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
                <li><a href="<?php echo e(route('map')); ?>" class="<?php echo e(request()->routeIs('map') ? 'active' : ''); ?>"><i class="fas fa-map-marked-alt"></i> Map</a></li>
            </ul>
            <ul class="nav-links">
                <li><a href="<?php echo e(route('login')); ?>" class="<?php echo e(request()->routeIs('login') ? 'active' : ''); ?>">Login</a></li>
                <li><a href="<?php echo e(route('register')); ?>" class="<?php echo e(request()->routeIs('register') ? 'active' : ''); ?>">SignUp</a></li>
            </ul>
        </nav>
    </header>
    <?php endif; ?>

<script src="<?php echo e(asset('js/profile.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Ghorfa-Project\resources\views/partials/nav.blade.php ENDPATH**/ ?>
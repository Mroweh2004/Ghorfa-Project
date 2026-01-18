<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ghorfa</title>
</head>
<body>

    @auth 
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="{{ asset('img/logo.png') }}" alt="Ghorfa Logo" width="50px">
                Ghorfa
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'active' : '' }}"><i class="fas fa-search"></i> Search</a></li>
                <li><a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}"><i class="fas fa-map-marked-alt"></i> Map</a></li>                
                @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                @endif
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
                @if(auth()->user()->isLandlord() || auth()->user()->isAdmin())
                <a href="{{ route('list-property') }}" class="main-list-btn"><i class="fas fa-plus"></i> List Your Space</a>
                @endif
                <div id="profile-link">
                    <div class="nav-profile-image">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile Image">       
                        @else
                            <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Image">
                        @endif
                    </div>
                    <img src="{{ asset( 'img/up.png') }}" alt="Up Arrow" class="up">
                    <img src="{{ asset('img/down.png') }}" alt="Down Arrow" class="down">
                </div>
            </div>
        </nav>
            <form method="post" class="profile-dropdown" action="{{ route('logout') }}">
            @csrf
                <a href="{{ route('profileInfo') }}" class="profile-dropdown-option"><i class="fas fa-user"></i> Profile</a>
                <a href="{{ route( 'profileFavorites') }}" class="profile-dropdown-option"><i class="fas fa-heart"></i> My Favorites</a>
                @if(auth()->user()->canBecomeLandlord())
                <a href="{{ route('landlord.apply') }}" class="profile-dropdown-option"><i class="fas fa-building"></i> Become a Landlord</a>
                @endif
                @if(auth()->user()->isLandlord())
                <a href="{{ route('landlord.dashboard') }}" class="profile-dropdown-option"><i class="fas fa-building"></i> Landlord Dashboard</a>
                @endif
                <button type="submit" class="profile-dropdown-option"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
    </header>
    @endauth

    @guest
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="{{ asset('img/logo.png') }}" alt="Ghorfa Logo" width="50px">
                Ghorfa
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'active' : '' }}"><i class="fas fa-search"></i> Search</a></li>
                <li><a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}"><i class="fas fa-map-marked-alt"></i> Map</a></li>
            </ul>
            <ul class="nav-links">
                <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
                <li><a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">SignUp</a></li>
            </ul>
        </nav>
    </header>
    @endguest

<script src="{{ asset('js/profile/profile.js') }}"></script>
</body>
</html>

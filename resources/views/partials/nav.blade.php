<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ghorfa</title>
    <script src="{{ asset('js/profile.js') }}"></script>
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
                <a href="{{ route('list-property') }}" class="main-list-btn"><i class="fas fa-plus"></i> List Your Space</a>
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
                <a href="{{ route('profileInfo') }}" class="profile-dropdown-option">Profile</a>
                <a href="{{ route( 'profileProperties') }}" class="profile-dropdown-option">My Properties</a>
                <a href="{{ route( 'profileFavorites') }}" class="profile-dropdown-option">My Favorites</a>
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

</body>
</html>

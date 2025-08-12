<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ghorfa</title>
    <!-- You can include your CSS here -->
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
                <li><a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
                @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                @endif
            </ul>
            <button class="main-list-btn" onclick="location.href='{{ route('list-property') }}'"><i class="fas fa-plus"></i> List Your Space</button>
        </nav>
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

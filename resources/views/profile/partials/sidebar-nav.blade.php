<aside class="profile-sidebar" aria-label="Profile navigation">
    <nav class="sidebar-nav">
        <a href="{{ route('profileInfo') }}" class="nav-item {{ request()->routeIs('profileInfo') ? 'active' : '' }}" title="Profile Info">
            <i class="fas fa-user"></i>
        </a>
        <a href="{{ route('profileFavorites') }}" class="nav-item {{ request()->routeIs('profileFavorites') ? 'active' : '' }}" title="Favorites">
            <i class="far fa-heart"></i>
        </a>
        <a href="{{ route('profileProperties') }}" class="nav-item {{ request()->routeIs('profileProperties') ? 'active' : '' }}" title="My listings">
            <i class="fas fa-map-marker-alt"></i>
        </a>
        <a href="#" class="nav-item" title="Settings">
            <i class="fas fa-cog"></i>
        </a>
    </nav>
</aside>

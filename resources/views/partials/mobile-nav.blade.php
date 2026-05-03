<div class="mobile-nav">
    <ul>
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home" aria-hidden="true"></i> Home</a></li>
        <li><a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'active' : '' }}"><i class="fas fa-search" aria-hidden="true"></i> Search</a></li>
        <li><a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}"><i class="fas fa-map-marked-alt" aria-hidden="true"></i> Map</a></li>
        @auth
            @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-cog" aria-hidden="true"></i> Admin</a></li>
            @endif
        @endauth
    </ul>
</div>

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
            <li><a href="{{route('home')}}"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="{{route('search')}}"><i class="fas fa-search"></i>Search</a></li>
            @auth
                @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                @endif
            @endauth
        </ul>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/admin.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('img/logo.png') }}">   
    <title>@yield('title', 'Admin Dashboard')</title>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" width="32px">
                    <span>Ghorfa Admin</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">MENU</div>
                    <ul class="nav-menu">
                        <li class="nav-item active">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                                <i class="fas fa-th-large"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#applications-section" class="nav-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Applications</span>
                                @if(isset($stats) && $stats['pending_applications'] > 0)
                                    <span class="nav-badge">{{ $stats['pending_applications'] }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#users-section" class="nav-link">
                                <i class="fas fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#landlords-section" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <span>Landlords</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#properties-section" class="nav-link">
                            <i class="fas fa-building"></i>
                            <span>Pending</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#recent-activity" class="nav-link">
                                <i class="fas fa-history"></i>
                                <span>Activity</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">QUICK LINKS</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                                <i class="fas fa-home"></i>
                                <span>View Site</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('search') }}" class="nav-link" target="_blank">
                                <i class="fas fa-search"></i>
                                <span>Search Properties</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search dashboard..." id="adminSearch">
                        <span class="search-shortcut">⌘K</span>
                    </div>
                </div>
                <div class="header-right">
                    <button class="header-icon-btn" id="themeToggle" title="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="notification-dropdown">
                        <button class="header-icon-btn" id="notificationBtn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-dot"></span>
                        </button>
                    </div>
                    <div class="user-dropdown">
                        <button class="user-profile-btn" id="userProfileBtn">
                            <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}" alt="{{ auth()->user()->name }}">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script src="{{ asset('js/admin/admin.js') }}"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('collapsed');
        });

        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('mobile-open');
        });

        // User dropdown
        document.getElementById('userProfileBtn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('userDropdownMenu').classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                document.getElementById('userDropdownMenu')?.classList.remove('show');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>

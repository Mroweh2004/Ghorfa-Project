@extends('layouts/app')
@section('title', 'Login Page')

@section('content')
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

@auth
<div class="auth-wrapper">
    <div class="auth-shell single-panel">
        <div class="auth-main">
            <div class="auth-card login-card">
                <h1 class="card-title">You are already logged in.</h1>
                <p class="card-sub">Head back to the homepage to continue exploring.</p>
                <div class="form-actions">
                    <a href="{{ route('home') }}" class="login-btn btn-link">Go to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

@guest
<div class="auth-wrapper">
    <div class="auth-shell">
        <aside class="auth-aside login-aside">
            <div class="brand">
                <div class="brand-logo">
                    <img src="{{ asset('img/logo.png') }}" alt="Ghorfa logo" width="32" height="32" loading="lazy">
                    <span class="logo-dot"></span>
                </div>
                <h1>Ghorfa</h1>
            </div>
            <h2>Welcome back</h2>
            <p class="aside-sub">Sign in to manage your listings, track favourites, and pick up where you left off.</p>
            <ul class="bullets">
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Keep your bookings in sync
                </li>
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Chat with landlords instantly
                </li>
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Save and compare properties
                </li>
            </ul>
            <div class="aside-footer">
                New to Ghorfa?
                <a href="{{ route('register') }}">Create an account</a>
            </div>
        </aside>

        <div class="auth-main">
            <div class="auth-card login-card">
                <h1 class="card-title">Sign in</h1>
                <p class="card-sub">Enter your details to access your dashboard.</p>

                @if ($errors->any())
                    <div class="alert">
                        <strong>We found {{ $errors->count() }} {{ Str::plural('issue', $errors->count()) }}:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('submit.login') }}" method="POST" class="login-form" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8l8 5 8-5"/><rect x="4" y="4" width="16" height="16" fill="none"/></svg>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email" autofocus>
                        </div>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="login-btn">Login</button>
                    </div>

                    <p class="auth-switch">
                        Don't have an account?
                        <a href="{{ route('register') }}">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endguest

@endsection

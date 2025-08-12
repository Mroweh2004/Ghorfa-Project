@extends('layouts/app')
@section('title', 'Login Page')
@section('content')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

@auth
    <div class="login-container">
        <h2>You are already logged in.</h2>
        <a href="{{ route('home') }}" class="btn btn-primary">Go to Home</a>
    </div>
@endauth

@guest
<div class="login-container">
    <h1 class="login-title">Login</h1>

   

    <form action="{{ route('submit.login') }}" method="POST" class="login-form">
        @csrf

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="login-button">Login</button>
        @if ($errors->any())
        <div class="error-messages">
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color: red">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    </form>
</div>
@endguest

@endsection

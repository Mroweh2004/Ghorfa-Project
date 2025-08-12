@extends('layouts/app')

@section('content')

    <link rel="stylesheet" href="{{asset('css/register.css')}}">
   
    <div class="container">
        <div class="register-card">
            <h2>Create Your Account</h2>
            <form action="{{ route('submit.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="profile_image" class="profile-image-label">
                        <div class="profile-image-preview">
                            <i class="fas fa-user-circle"></i>
                            <span>Upload Profile Image</span>
                        </div>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-image-input">
                    </label>
                </div>
                <div class="form-group">
                    <label for="name">First Name</label>
                    <input type="text" id="name" name="name"   required>
                    <span class="text.danger">@error('name') {{$message}} @enderror</span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"  required>
                    <span class="text.danger">@error('name') {{$message}} @enderror</span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"  required>
                    <span class="text.danger">@error('name') {{$message}} @enderror</span>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"  required>
                    <span class="text.danger">@error('name') {{$message}} @enderror</span>
                </div>
                <div class="form-group">
                    <label for="phone-nb">Phone Number</label>
                    <input type="text" id="phone-nb" name="phone-nb" required>
                    <span class="text.danger">@error('phone-nb') {{$message}} @enderror</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="register-btn">Create Account</button>
                </div>
            </form>
    </div>
</div>

<script src="{{ asset('js/register.js') }}"></script>

@endsection


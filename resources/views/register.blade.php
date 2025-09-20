@extends('layouts/app')

@section('content')
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

<div class="auth-wrapper">
  <div class="auth-shell">
    {{-- Left panel --}}
    <aside class="auth-aside">
      <div class="brand">
        <div class="brand-logo">
          <img src="{{ asset('img/logo.png') }}" alt="Ghorfa logo" width="32" height="32" loading="lazy">
        </div>
        <h1>Ghorfa</h1>
      </div>

      <h2>Create your account</h2>
      <p class="aside-sub">Join us in less than a minute. Manage your profile, bookings and more.</p>

      <ul class="bullets">
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Secure & private</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Fast onboarding</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Landlord tools</li>
      </ul>

      <div class="aside-footer">Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
      </div>
    </aside>

    <div class="auth-main">
      <div class="register-card">

        {{-- Top-level error summary --}}
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

        <form action="{{ route('submit.register') }}" method="POST" enctype="multipart/form-data" novalidate>
          @csrf

          {{-- Profile image --}}
          <div class="form-row">
            <label for="profile_image" class="profile-image-label">
              <div class="profile-image-preview" id="imagePreview">
                <i class="fas fa-user-circle" aria-hidden="true"></i>
                <span>Upload Profile Image</span>
                <img id="profileImageTag" alt="" />
              </div>
              <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-image-input">
            </label>
            @error('profile_image') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="grid two">
            <div class="form-group">
              <label for="first_name">First Name</label>
              <div class="input-wrap">
                <span class="input-icon">@</span>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Ali" required>
              </div>
              @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
              <label for="last_name">Last Name</label>
              <div class="input-wrap">
                <span class="input-icon">@</span>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Ahmad" required>
              </div>
              @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" fill="none"/><path d="M4 8l8 5 8-5"/></svg>
              </span>
              <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
            </div>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="grid two">
            <div class="form-group">
              <label for="password">Password</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                </span>
                <input type="password" id="password" name="password" placeholder="Min 8 characters" required>
                <button type="button" class="toggle-eye" data-target="password" aria-label="Show or hide password">👁</button>
              </div>
              <small id="pwHint" class="hint">Use 8+ chars with letters & numbers</small>
              @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirm Password</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                <button type="button" class="toggle-eye" data-target="password_confirmation" aria-label="Show or hide password">👁</button>
              </div>
              @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="form-group">
            <label for="phone_nb">Phone Number</label>
            <div class="input-inline">
              <span class="dial">+961</span>
              <input type="tel" id="phone_nb" name="phone_nb" inputmode="numeric" pattern="[0-9 ]*" value="{{ old('phone_nb') }}" placeholder="70 123 456" required>
            </div>
            @error('phone_nb') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label>Date of Birth</label>
            <div class="dob-grid">
              <select name="dob_day" id="dob_day" aria-label="Day">
                <option value="">DD</option>
                @for($d=1;$d<=31;$d++)
                  <option value="{{ $d }}" @selected(old('dob_day') == $d)>{{ sprintf('%02d',$d) }}</option>
                @endfor
              </select>
              <select name="dob_month" id="dob_month" aria-label="Month">
                <option value="">MM</option>
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}" @selected(old('dob_month') == $m)>{{ sprintf('%02d',$m) }}</option>
                @endfor
              </select>
              <select name="dob_year" id="dob_year" aria-label="Year">
                <option value="">YYYY</option>
                @for($y=date('Y');$y>=1900;$y--)
                  <option value="{{ $y }}" @selected(old('dob_year') == $y)>{{ $y }}</option>
                @endfor
              </select>
            </div>
            {{-- If your validator maps to date_of_birth, keep this: --}}
            @error('date_of_birth') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" placeholder="Street, City, Country">{{ old('address') }}</textarea>
            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="form-group landlord">
            <label class="switch">
              <input type="checkbox" id="is_landlord" name="is_landlord" value="1" @checked(old('is_landlord'))>
              <span class="slider"></span>
            </label>
            <label for="is_landlord" class="switch-label">I am a landlord</label>
            @error('is_landlord') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <button type="submit" class="register-btn">Create Account</button>

          <p class="terms">By creating an account, you agree to our <a href="{{ url('/terms') }}">Terms</a> & <a href="{{ url('/privacy') }}">Privacy Policy</a>.</p>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://kit.fontawesome.com/a2c0d5f0d1.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/register.js') }}"></script>
@endsection

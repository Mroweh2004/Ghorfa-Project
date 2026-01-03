{{-- resources/views/profile.blade.php --}}
@extends('layouts/app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/profile.js') }}" defer></script>
@endpush

@section('content')
@auth
    <main>
        <div class="profile-container">
            <div class="profile-card profile-card-shadow" role="region" aria-label="User profile">

                {{-- Header / Avatar / Basic Info --}}
                <div class="profile-header">
                    <div class="avatar-wrapper">
                        <div
                            class="profile-image profile-image-margin profile-image-wrapper"
                            id="avatarClickTarget"
                            role="button"
                            tabindex="0"
                            aria-label="View profile photo"
                        >
                            @if (Auth::user()->profile_image)
                                <img
                                    src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                                    alt="{{ Auth::user()->name }} profile image"
                                    onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff';"
                                >
                            @else
                                <img
                                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff"
                                    alt="Default profile avatar for {{ Auth::user()->name }}"
                                >
                            @endif
                        </div>

                        {{-- Inline avatar change (auto-submit on choose) --}}
                        <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data" class="avatar-inline-form" title="Change photo">
                            @csrf
                            @method('PUT')
                            <input type="file" name="profile_image" id="avatarFile" accept="image/*" class="avatar-file-input" hidden>
                            <label for="avatarFile" class="avatar-edit-btn" aria-label="Change profile photo">
                                <i class="fa-duotone fa-regular fa-pen-to-square"></i>
                            </label>
                        </form>
                    </div>

                    <h2 class="profile-name" aria-live="polite">{{ Auth::user()->name }}</h2>

                    <div class="profile-info profile-info-margin">
                        <p>
                            <span class="profile-icon-email">📧</span>
                            <a href="mailto:{{ Auth::user()->email }}">{{ Auth::user()->email }}</a>
                        </p>
                        <p>
                            <span class="profile-icon-phone">📞</span>
                            @if(!empty(Auth::user()->phone_nb))
                                +961 {{ Auth::user()->phone_nb }}
                            @else
                                N/A
                            @endif
                        </p>
                        @if(!empty(Auth::user()->date_of_birth))
                            <p>
                                <span class="profile-icon-dob">🎂</span>
                                {{ \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('F j, Y') }}
                            </p>
                        @endif
                        @if(!empty(Auth::user()->address))
                            <p>
                                <span class="profile-icon-address">📍</span>
                                {{ Auth::user()->address }}
                            </p>
                        @endif
                        @if(Auth::user()->isLandlord())
                            <p><span class="profile-icon-landlord">🏠</span>Landlord</p>
                        @endif
                        <p>
                            <span class="profile-icon-joined">📅</span>
                            Joined {{ Auth::user()->created_at->format('F Y') }}
                        </p>
                        @if(Auth::user()->last_login_at)
                            <p>
                                <span class="profile-icon-last-login">🕒</span>
                                Last login: {{ \Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans() }}
                            </p>
                        @endif
                    </div>

                    <div class="profile-actions-inline">
                        <button type="button" class="edit-profile-btn" id="toggleEditBtn">Edit profile</button>
                    </div>
                </div>

                {{-- Avatar Modal --}}
                <div id="avatarModal" class="avatar-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Profile photo">
                    <div class="avatar-modal-backdrop" data-close="avatarModal"></div>
                    <div class="avatar-modal-dialog" role="document">
                        <button type="button" class="avatar-modal-close" data-close="avatarModal" aria-label="Close">×</button>
                        <div class="avatar-modal-body">
                            @if (Auth::user()->profile_image)
                                <img id="avatarModalImg" src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }} profile image large">
                            @else
                                <img id="avatarModalImg" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff" alt="Default profile avatar for {{ Auth::user()->name }}">
                            @endif
                        </div>
                        <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data" class="avatar-modal-actions">
                            @csrf
                            @method('PUT')
                            <input type="file" name="profile_image" id="avatarFileModal" accept="image/*" class="avatar-file-input" hidden>
                            <label for="avatarFileModal" class="btn-secondary avatar-change-btn">Edit photo</label>
                        </form>
                    </div>
                </div>

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="alert-success" role="status">{{ session('success') }}</div>
                @endif

                {{-- Prepare values --}}
                @php
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    $dob = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null;
                    $showEdit = $errors->any();
                @endphp

                {{-- Edit Form --}}
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="editProfileSection"
                    @if(!$showEdit) 
                    style="display:none" 
                    @endif
                    >
                    @csrf
                    @method('PUT')

                    {{-- Exit Edit --}}
                    <button type="button" class="exit-edit-btn" id="exitEditBtn" style="display:none;">
                        <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20">
                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <span>Exit</span>
                    </button>

                        <div class="profile-form">

                        <div class="grid two">
                            {{-- First Name --}}
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <div class="input-wrap">
                                    <input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        value="{{ old('first_name', $user->first_name) }}"
                                        placeholder="e.g. Ali"
                                        required
                                        spellcheck="false"
                                        autocomplete="given-name">
                                </div>
                                @error('first_name') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <div class="input-wrap">                    
                                    <input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        value="{{ old('last_name', $user->last_name) }}"
                                        placeholder="e.g. Ahmad"
                                        required
                                        spellcheck="false"
                                        autocomplete="family-name"
                                    >
                                </div>
                                @error('last_name') <div class="error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrap">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="you@example.com"
                                    required
                                    inputmode="email"
                                    autocomplete="email"
                                >
                            </div>
                            @error('email') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-group">
                            <label for="phone_nb">Phone Number</label>
                            <div class="input-inline">
                                <span class="dial">+961</span>
                                <input
                                    id="phone_nb"
                                    name="phone_nb"
                                    type="tel"
                                    inputmode="numeric"
                                    pattern="[0-9 ]*"
                                    value="{{ old('phone_nb', $user->phone_nb) }}"
                                    placeholder="70 123 456"
                                    required
                                    autocomplete="tel-national"
                                >
                            </div>
                            @error('phone_nb') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        {{-- DOB (Day / Month / Year) --}}
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <div class="dob-grid">
                                <select name="dob_day" id="dob_day" aria-label="Day">
                                    <option value="">DD</option>
                                    @for ($d = 1; $d <= 31; $d++)
                                        <option value="{{ $d }}" @selected($dob && $dob->day == $d)>{{ sprintf('%02d',$d) }}</option>
                                    @endfor
                                </select>

                                <select name="dob_month" id="dob_month" aria-label="Month">
                                    <option value="">MM</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected($dob && $dob->month == $m)>{{ sprintf('%02d',$m) }}</option>
                                    @endfor
                                </select>

                                <select name="dob_year" id="dob_year" aria-label="Year">
                                    <option value="">YYYY</option>
                                    @for ($y = date('Y'); $y >= 1900; $y--)
                                        <option value="{{ $y }}" @selected($dob && $dob->year == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            @error('date_of_birth') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        {{-- Address --}}
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                placeholder="Street, City, Country"
                                autocomplete="street-address"
                            >{{ old('address', $user->address) }}</textarea>
                            @error('address') <div class="error">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="form-actions profile-actions">
                        <button type="submit" class="btn-secondary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endauth
@endsection

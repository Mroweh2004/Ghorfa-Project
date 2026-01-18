@extends('layouts.app')
@section('title', 'Become a Landlord')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
@endpush

@section('content')
<main>
    <div class="profile-container">
        <div class="profile-card profile-card-shadow">
            <div class="profile-header">
                <h1>Become a Landlord</h1>
                <p>Fill out the form below to apply for a landlord account. Once approved, you'll be able to list and manage your properties.</p>
            </div>

            <form action="{{ route('landlord.submit-application') }}" method="POST" class="profile-form">
                @csrf

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone_nb) }}" placeholder="Enter your phone number">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" placeholder="Enter your full address">{{ old('address', auth()->user()->address) }}</textarea>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_number">ID Number (Optional)</label>
                    <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" placeholder="Enter your ID number">
                    @error('id_number')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="trade_license">Trade License (Optional)</label>
                    <input type="text" id="trade_license" name="trade_license" value="{{ old('trade_license') }}" placeholder="Enter your trade license number">
                    @error('trade_license')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Tell us about yourself and why you want to become a landlord">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection


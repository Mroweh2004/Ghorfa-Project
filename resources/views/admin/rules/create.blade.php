@extends('admin.layout-subpage')

@section('title', 'New rule')

@section('content')
    <nav class="admin-user-show__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="admin-user-show__back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Admin dashboard
        </a>
        <span class="admin-user-show__crumb-sep" aria-hidden="true">/</span>
        <a href="{{ route('admin.rules.index') }}" class="admin-user-show__back">House rules</a>
        <span class="admin-user-show__crumb-sep" aria-hidden="true">/</span>
        <span class="admin-user-show__crumb-current">New</span>
    </nav>

    <header class="admin-crud-header admin-crud-header--single">
        <h1 class="admin-user-show__title">New house rule</h1>
        <p class="admin-user-show__subtitle">Clear, short rule text (e.g. No smoking, Quiet hours after 10pm).</p>
    </header>

    <section class="admin-user-card">
        <form action="{{ route('admin.rules.store') }}" method="POST" class="admin-crud-form">
            @csrf
            <div class="admin-crud-field">
                <label for="rule-name">Name</label>
                <input type="text" id="rule-name" name="name" value="{{ old('name') }}" required maxlength="255" class="admin-crud-input @error('name') admin-crud-input--error @enderror" autocomplete="off">
                @error('name')
                    <p class="admin-crud-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="admin-crud-form-actions">
                <a href="{{ route('admin.rules.index') }}" class="admin-crud-btn admin-crud-btn--ghost">Cancel</a>
                <button type="submit" class="admin-crud-btn admin-crud-btn--primary">Create rule</button>
            </div>
        </form>
    </section>
@endsection

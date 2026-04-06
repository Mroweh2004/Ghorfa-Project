<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <title>{{ $user->name }} — User profile (Admin)</title>
</head>
<body class="admin-body">
    @include('partials.nav')

    <main class="admin-user-show">
        <div class="admin-user-show__inner">
            <nav class="admin-user-show__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="admin-user-show__back">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i> Admin dashboard
                </a>
                <span class="admin-user-show__crumb-sep" aria-hidden="true">/</span>
                <span class="admin-user-show__crumb-current">User #{{ $user->id }}</span>
            </nav>

            @if(session('success'))
                <div class="admin-user-show__flash admin-user-show__flash--success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-user-show__flash admin-user-show__flash--error">{{ session('error') }}</div>
            @endif

            <header class="admin-user-show__header">
                <div>
                    <h1 class="admin-user-show__title">{{ $user->name }}</h1>
                    <p class="admin-user-show__subtitle">{{ $user->email }}</p>
                </div>
                <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
            </header>

            <section class="admin-user-card" aria-labelledby="account-heading">
                <h2 class="admin-user-card__title" id="account-heading"><i class="fas fa-id-badge"></i> Account</h2>
                <dl class="admin-user-dl">
                    <div><dt>User ID</dt><dd>{{ $user->id }}</dd></div>
                    <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
                    <div><dt>Phone</dt><dd>{{ $user->phone_nb ?: '—' }}</dd></div>
                    <div><dt>Date of birth</dt><dd>{{ $user->date_of_birth ? $user->date_of_birth->format('M j, Y') : '—' }}</dd></div>
                    <div><dt>Address</dt><dd>{{ $user->address ? \Illuminate\Support\Str::limit($user->address, 120) : '—' }}</dd></div>
                    <div><dt>Registered</dt><dd>{{ $user->created_at->format('M j, Y g:i A') }}</dd></div>
                    <div><dt>Last login</dt><dd>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '—' }}</dd></div>
                    <div><dt>Properties listed</dt><dd>{{ number_format($propertyCount) }}</dd></div>
                    <div><dt>Reviews written</dt><dd>{{ number_format($reviewCount) }}</dd></div>
                </dl>
            </section>

            @if($user->profile_image)
                <section class="admin-user-card" aria-labelledby="profile-photo-heading">
                    <h2 class="admin-user-card__title" id="profile-photo-heading"><i class="fas fa-user-circle"></i> Profile picture</h2>
                    <figure class="admin-user-show__profile-figure">
                        <a href="{{ asset('storage/'.$user->profile_image) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('storage/'.$user->profile_image) }}" alt="Profile photo" class="admin-user-show__profile-img">
                        </a>
                        <figcaption class="admin-user-show__caption">Opens full size in a new tab</figcaption>
                    </figure>
                </section>
            @endif

            @php $app = $user->landlordApplication; @endphp
            <section class="admin-user-card" aria-labelledby="verification-heading">
                <h2 class="admin-user-card__title" id="verification-heading"><i class="fas fa-shield-halved"></i> Landlord verification</h2>
                @if($app)
                    <dl class="admin-user-dl admin-user-dl--compact">
                        <div><dt>Application status</dt><dd><span class="admin-pill admin-pill--{{ $app->status }}">{{ ucfirst($app->status) }}</span></dd></div>
                        <div><dt>Document type</dt><dd>{{ $app->verificationLabel() }}</dd></div>
                        <div><dt>Document number</dt><dd>{{ $app->verificationNumber() ?: '—' }}</dd></div>
                        <div><dt>Applied</dt><dd>{{ $app->created_at->format('M j, Y g:i A') }}</dd></div>
                        @if($app->reviewed_at)
                            <div><dt>Reviewed</dt><dd>{{ $app->reviewed_at->format('M j, Y g:i A') }}{{ $app->reviewer ? ' — '.$app->reviewer->name : '' }}</dd></div>
                        @endif
                    </dl>

                    <div class="admin-user-gallery">
                        @if($app->document_front_path)
                            <figure class="admin-user-gallery__item">
                                <a href="{{ asset('storage/'.$app->document_front_path) }}" target="_blank" rel="noopener noreferrer" class="admin-user-gallery__link">
                                    <img src="{{ asset('storage/'.$app->document_front_path) }}" alt="ID or license — front" loading="lazy">
                                </a>
                                <figcaption>Document — front <span class="admin-user-gallery__hint">(click to enlarge)</span></figcaption>
                            </figure>
                        @endif
                        @if($app->document_back_path)
                            <figure class="admin-user-gallery__item">
                                <a href="{{ asset('storage/'.$app->document_back_path) }}" target="_blank" rel="noopener noreferrer" class="admin-user-gallery__link">
                                    <img src="{{ asset('storage/'.$app->document_back_path) }}" alt="ID or license — back" loading="lazy">
                                </a>
                                <figcaption>Document — back</figcaption>
                            </figure>
                        @endif
                        @if($app->face_photo_path)
                            <figure class="admin-user-gallery__item">
                                <a href="{{ asset('storage/'.$app->face_photo_path) }}" target="_blank" rel="noopener noreferrer" class="admin-user-gallery__link">
                                    <img src="{{ asset('storage/'.$app->face_photo_path) }}" alt="Face verification photo" loading="lazy">
                                </a>
                                <figcaption>Face photo (capture)</figcaption>
                            </figure>
                        @endif
                    </div>
                    @if(!$app->document_front_path && !$app->document_back_path && !$app->face_photo_path)
                        <p class="admin-user-show__empty">No document images stored for this application.</p>
                    @endif
                @else
                    <p class="admin-user-show__empty">No landlord application on file for this user.</p>
                @endif
            </section>

            @if($user->properties->isNotEmpty())
                <section class="admin-user-card" aria-labelledby="props-heading">
                    <h2 class="admin-user-card__title" id="props-heading"><i class="fas fa-building"></i> Recent properties</h2>
                    <ul class="admin-user-props">
                        @foreach($user->properties as $prop)
                            <li>
                                <span class="admin-user-props__title">{{ $prop->title }}</span>
                                <span class="admin-user-props__meta">{{ ucfirst($prop->status) }} · {{ $prop->created_at->format('M j, Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <footer class="admin-user-show__footer">
                @if($user->role !== 'admin')
                    <div class="admin-user-show__footer-actions">
                        @if($app && $app->isPending() && $user->role === 'client')
                            <form action="{{ route('admin.landlord.approve', $app) }}" method="POST" onsubmit="return confirm('Approve this user as a landlord? They will be able to list properties.');">
                                @csrf
                                <button type="submit" class="btn btn-success approve-btn">
                                    <i class="fas fa-user-check" aria-hidden="true"></i> Approve as landlord
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Delete this user permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn"><i class="fas fa-trash"></i> Delete user</button>
                        </form>
                    </div>
                @else
                    <p class="admin-user-show__note">Admin accounts cannot be deleted from this screen.</p>
                @endif
            </footer>
        </div>
    </main>
</body>
</html>

@extends('layouts.app')
@section('title', 'Transaction #' . $transaction->id)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transaction-request.css') }}">
<link rel="stylesheet" href="{{ asset('css/landlord/dashboard.css') }}">
@endpush

@section('content')
@php
    $user = $transaction->user;
    $property = $transaction->property;
    $userPhone = $user ? ($user->phone_nb ?? $user->phone ?? '') : '';
    $statusLabel = $transaction->status ? str_replace('_', ' ', ucwords($transaction->status, '_')) : 'Pending';
    $statusClass = 'request-detail-status--' . ($transaction->status ?? 'pending');
    $propertyRules = $property ? ($property->rules ?? collect()) : collect();
    $propertyAmenities = $property ? ($property->amenities ?? collect()) : collect();
@endphp

<div class="container py-4">
    <div class="transaction-report-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-file-contract"></i> Transaction Report #{{ $transaction->id }}</h1>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($isBuyer && $transaction->hasContractGenerated() && !$transaction->isBuyerApproved())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>The landlord has generated a contract.</strong> Please review the full report below and approve or reject the contract.
            </div>
        @endif

        @if($isBuyer && $transaction->isBuyerApproved() && $transaction->isPending())
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                You have approved this contract. Waiting for the landlord to confirm.
            </div>
        @endif

        @if($isBuyer && !$transaction->hasContractGenerated())
            <div class="alert alert-secondary">
                <i class="fas fa-clock"></i>
                Waiting for the landlord to generate the contract. You will be able to review and approve it here once it is ready.
            </div>
        @endif

        <div class="request-details-content request-details-content--standalone">
            <h3 class="request-details-title"><i class="fas fa-clipboard-list"></i> Full report</h3>

            {{-- Your information (buyer) --}}
            <section class="request-detail-section">
                <h4 class="request-detail-section-title"><i class="fas fa-user"></i> Your information</h4>
                <div class="request-details-grid">
                    <div class="request-detail-item">
                        <label><i class="fas fa-user"></i> Name</label>
                        <span>{{ $user ? $user->name : '—' }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <span>{{ $user ? $user->email : '—' }}</span>
                    </div>
                    @if($userPhone)
                    <div class="request-detail-item">
                        <label><i class="fas fa-phone"></i> Phone</label>
                        <span>{{ $userPhone }}</span>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Transaction & price --}}
            <section class="request-detail-section">
                <h4 class="request-detail-section-title"><i class="fas fa-file-invoice"></i> Transaction & price</h4>
                <div class="request-details-grid">
                    <div class="request-detail-item">
                        <label><i class="fas fa-tag"></i> Request type</label>
                        <span>{{ $transaction->type === 'rent' ? 'Rental' : 'Purchase' }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-info-circle"></i> Status</label>
                        <span class="request-detail-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-dollar-sign"></i> Price</label>
                        <span><strong>${{ number_format($transaction->price, 2) }}</strong> {{ $transaction->currency ?? 'USD' }}{{ $transaction->type === 'rent' ? ' / total for stay' : '' }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-calendar-alt"></i> Request date</label>
                        <span>{{ $transaction->created_at ? $transaction->created_at->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    @if($transaction->type === 'rent')
                    <div class="request-detail-item">
                        <label><i class="fas fa-calendar-check"></i> Check-in</label>
                        <span>{{ $transaction->start_date ? $transaction->start_date->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-calendar-times"></i> Check-out</label>
                        <span>{{ $transaction->end_date ? $transaction->end_date->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-check-double"></i> Rules accepted</label>
                        <span class="request-detail-rules-accepted {{ $transaction->rules_accepted ? 'request-detail-rules-accepted--yes' : 'request-detail-rules-accepted--no' }}">
                            {{ $transaction->rules_accepted ? 'Yes ✓' : 'No ✗' }}
                        </span>
                    </div>
                    @endif
                </div>
                @if($transaction->rules_exceptions && trim($transaction->rules_exceptions))
                <div class="request-detail-item request-detail-item--full">
                    <label class="request-detail-label--warning"><i class="fas fa-exclamation-circle"></i> Your rules exceptions / concerns</label>
                    <div class="request-detail-box request-detail-box--warning">{{ $transaction->rules_exceptions }}</div>
                </div>
                @endif
                @if($transaction->notes && trim($transaction->notes))
                <div class="request-detail-item request-detail-item--full">
                    <label class="request-detail-label--notes"><i class="fas fa-comment-dots"></i> Your notes</label>
                    <div class="request-detail-box request-detail-box--notes">{{ $transaction->notes }}</div>
                </div>
                @endif
            </section>

            {{-- Property information --}}
            @if($property)
            <section class="request-detail-section">
                <h4 class="request-detail-section-title"><i class="fas fa-home"></i> Property information</h4>
                <div class="request-details-grid">
                    <div class="request-detail-item">
                        <label><i class="fas fa-building"></i> Property</label>
                        <span>{{ $property->title }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-map-marker-alt"></i> Location</label>
                        <span>{{ $property->city }}, {{ $property->country }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-layer-group"></i> Type</label>
                        <span>{{ $property->property_type }}</span>
                    </div>
                    <div class="request-detail-item">
                        <label><i class="fas fa-dollar-sign"></i> Price</label>
                        <span>${{ number_format($property->price) }}/month</span>
                    </div>
                    @if($property->bedroom_nb)
                    <div class="request-detail-item">
                        <label><i class="fas fa-bed"></i> Bedrooms</label>
                        <span>{{ $property->bedroom_nb }}</span>
                    </div>
                    @endif
                    @if($property->bathroom_nb)
                    <div class="request-detail-item">
                        <label><i class="fas fa-bath"></i> Bathrooms</label>
                        <span>{{ $property->bathroom_nb }}</span>
                    </div>
                    @endif
                    @if($property->area_m3)
                    <div class="request-detail-item">
                        <label><i class="fas fa-ruler-combined"></i> Area</label>
                        <span>{{ $property->area_m3 }} m²</span>
                    </div>
                    @endif
                    @if($property->description && trim($property->description))
                    <div class="request-detail-item request-detail-item--full">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <div class="request-detail-box">{{ $property->description }}</div>
                    </div>
                    @endif
                    <div class="request-detail-item request-detail-item--full request-detail-rules-amenities-row">
                        <div class="request-detail-rules-amenities-grid">
                            <div class="request-detail-rules-amenities-col">
                                <label><i class="fas fa-list-alt"></i> House rules</label>
                                @if($propertyRules->isNotEmpty())
                                <ul class="request-detail-list">
                                    @foreach($propertyRules as $rule)
                                    <li>{{ $rule->name }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="request-detail-empty">None listed</span>
                                @endif
                            </div>
                            <div class="request-detail-rules-amenities-col">
                                <label><i class="fas fa-concierge-bell"></i> Amenities</label>
                                @if($propertyAmenities->isNotEmpty())
                                <ul class="request-detail-list request-detail-list--amenities">
                                    @foreach($propertyAmenities as $amenity)
                                    <li>{{ $amenity->name }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="request-detail-empty">None listed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- Contract actions (buyer only, when contract is generated and not yet approved) --}}
            @if($canApprove)
            <div class="request-detail-section request-detail-section--actions">
                <h4 class="request-detail-section-title"><i class="fas fa-file-signature"></i> Approve or reject contract</h4>
                <p class="text-muted mb-3">After reviewing the information above, confirm that you agree to the terms or reject the contract.</p>
                <div class="request-details-actions transaction-report-actions">
                    <form method="POST" action="{{ route('transactions.approve-contract', $transaction) }}" class="transaction-report-action-form">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Approve contract
                        </button>
                    </form>
                    <form method="POST" action="{{ route('transactions.reject-contract', $transaction) }}" class="transaction-report-action-form transaction-report-reject-form" onsubmit="return confirm('Rejecting the contract will cancel this transaction. Continue?');">
                        @csrf
                        <input type="text" name="reason" class="form-control" placeholder="Reason (optional)" maxlength="500">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Reject contract
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

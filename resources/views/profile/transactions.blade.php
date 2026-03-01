@extends('layouts.app')
@section('title', 'My requests')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile/transactions.css') }}">
@endpush

@section('content')
<main>
    <div class="my-requests-page">
        <div class="requests-header">
            <div class="requests-header-content">
                <h2><i class="fas fa-file-contract"></i> My requests</h2>
                <p class="text-muted mb-3">Your rental and purchase requests. Open a request to view the full report (price, property, rules, amenities) and approve or reject the contract once the landlord has generated it.</p>
            </div>
            <div class="requests-character-container">
                <img src="{{ asset('images/character/tie.png') }}" alt="Your Requests" class="requests-character">
            </div>
        </div>

        @if($transactions->isEmpty())
            <div class="my-requests-empty">
                <div class="empty-transactions-character">
                    <img src="{{ asset('images/character/dashboard-empty.png') }}" alt="No requests yet" class="empty-state-character">
                </div>
                <p>You have no requests yet.</p>
                <a href="{{ route('search') }}" class="view-report-btn">Search properties</a>
            </div>
        @else
            <table class="my-requests-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td>
                            <strong>{{ $tx->property ? $tx->property->title : '—' }}</strong>
                        </td>
                        <td>{{ $tx->type === 'rent' ? 'Rental' : 'Purchase' }}</td>
                        <td>
                            @php
                                $status = $tx->status ?? 'pending';
                                $statusLabel = str_replace('_', ' ', ucwords($status, '_'));
                            @endphp
                            <span class="status-badge status-{{ $status }}">{{ $statusLabel }}</span>
                        </td>
                        <td>{{ $tx->created_at->format('M j, Y') }}</td>
                        <td>
                            <a href="{{ route('transactions.show', $tx) }}" class="view-report-btn">
                                <i class="fas fa-external-link-alt"></i> View report
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($transactions->hasPages())
                <div class="pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>
</main>
@endsection

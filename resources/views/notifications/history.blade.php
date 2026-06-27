@extends('layouts.app')
@section('title', 'Notifications')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('content')
<main class="notifications-history-page">
    <div class="notifications-history">
        <header class="notifications-history__header">
            <div class="notifications-history__header-text">
                <h1><i class="fas fa-bell" aria-hidden="true"></i> Notifications</h1>
                <p class="notifications-history__subtitle">
                    @if($unreadCount > 0)
                        You have {{ $unreadCount }} unread {{ \Illuminate\Support\Str::plural('notification', $unreadCount) }}.
                    @else
                        Your full notification history.
                    @endif
                </p>
            </div>
            @if($unreadCount > 0)
            <button type="button" class="notifications-history__mark-all" id="markAllReadHistoryBtn">
                Mark all as read
            </button>
            @endif
        </header>

        @if($totalCount > 0)
        <form class="notifications-history__search" method="GET" action="{{ route('notifications.history') }}" role="search">
            <label class="notifications-history__search-label" for="notificationsSearchInput">Search notifications</label>
            <div class="notifications-history__search-row">
                <span class="notifications-history__search-icon" aria-hidden="true">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="search"
                    id="notificationsSearchInput"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search by title, message, or type..."
                    class="notifications-history__search-input"
                    autocomplete="off"
                >
                <button type="submit" class="notifications-history__search-submit">Search</button>
                @if($search !== '')
                <a href="{{ route('notifications.history') }}" class="notifications-history__search-clear">Clear</a>
                @endif
            </div>
        </form>

        <p class="notifications-history__meta">
            Sorted newest first
            @if($search !== '')
                · {{ $notifications->total() }} {{ \Illuminate\Support\Str::plural('match', $notifications->total()) }} for “{{ $search }}”
            @else
                · {{ $notifications->total() }} {{ \Illuminate\Support\Str::plural('notification', $notifications->total()) }}
            @endif
            @if($notifications->total() > 0)
                · Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }}
            @endif
        </p>
        @endif

        @if($totalCount === 0)
            <div class="notifications-history__empty">
                <i class="fas fa-bell-slash" aria-hidden="true"></i>
                <p>No notifications yet.</p>
                <a href="{{ route('home') }}" class="notifications-history__empty-link">Back to home</a>
            </div>
        @elseif($notifications->isEmpty())
            <div class="notifications-history__empty">
                <i class="fas fa-search" aria-hidden="true"></i>
                <p>No notifications match “{{ $search }}”.</p>
                <a href="{{ route('notifications.history') }}" class="notifications-history__empty-link">Clear search</a>
            </div>
        @else
            <ul class="notifications-history__list" id="notificationsHistoryList">
                @foreach($notifications as $notification)
                    @php
                        $iconMap = [
                            'like' => 'fa-heart',
                            'approve' => 'fa-check-circle',
                            'reject' => 'fa-times-circle',
                            'pending' => 'fa-clock',
                            'review' => 'fa-star',
                            'transaction' => 'fa-file-contract',
                        ];
                        $colorMap = [
                            'like' => '#ef4444',
                            'approve' => '#10b981',
                            'reject' => '#ef4444',
                            'pending' => '#f59e0b',
                            'review' => '#f59e0b',
                            'transaction' => '#3b82f6',
                        ];
                        $icon = $iconMap[$notification->type] ?? 'fa-bell';
                        $color = $colorMap[$notification->type] ?? '#6b7280';
                    @endphp
                    <li>
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}"
                               class="notification-history-item {{ $notification->read ? '' : 'unread' }}"
                               data-notification-id="{{ $notification->id }}">
                        @else
                            <div class="notification-history-item {{ $notification->read ? '' : 'unread' }}"
                                 data-notification-id="{{ $notification->id }}">
                        @endif
                            <span class="notification-icon" style="background: {{ $color }}20; color: {{ $color }};">
                                <i class="fas {{ $icon }}" aria-hidden="true"></i>
                            </span>
                            <span class="notification-history-item__body">
                                <span class="notification-title">{{ $notification->title }}</span>
                                <span class="notification-message">{{ $notification->message }}</span>
                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }} · {{ $notification->created_at->format('M j, Y g:i A') }}</span>
                            </span>
                            @if(!$notification->read)
                                <span class="notification-unread-dot" aria-hidden="true"></span>
                            @endif
                        @if($notification->action_url)
                            </a>
                        @else
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            @if($notifications->hasPages())
                <div class="notifications-history__pagination">
                    <nav class="pagination notifications-pagination" aria-label="Notifications pagination">
                        @if($notifications->onFirstPage())
                            <button type="button" class="pagination-btn" disabled>Previous</button>
                        @else
                            <a href="{{ $notifications->previousPageUrl() }}" class="pagination-btn">Previous</a>
                        @endif

                        @foreach($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                            @if($page == $notifications->currentPage())
                                <button type="button" class="pagination-btn active" aria-current="page">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($notifications->hasMorePages())
                            <a href="{{ $notifications->nextPageUrl() }}" class="pagination-btn">Next</a>
                        @else
                            <button type="button" class="pagination-btn" disabled>Next</button>
                        @endif
                    </nav>
                </div>
            @endif
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/notifications-page.js') }}" defer></script>
@endpush

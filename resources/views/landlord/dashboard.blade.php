@extends('layouts.app')
@section('title', 'Landlord Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/landlord/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/landlord-tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/transaction-request.css') }}">
@endpush

@section('content')
<div class="main-container">
<!-- Left Sidebar Navigation (fixed, collapsible) -->
<aside id="landlordSidebar" class="landlord-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" width="32px">
            <span>Ghorfa Landlord</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" title="Collapse / Expand">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">MENU</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#requests-section" class="nav-link">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Requests</span>
                        @if($stats['pending_requests'] > 0)
                            <span class="nav-badge">{{ $stats['pending_requests'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#active-section" class="nav-link">
                        <i class="fas fa-handshake"></i>
                        <span>Active Transactions</span>
                        @if($stats['active_transactions'] > 0)
                            <span class="nav-badge">{{ $stats['active_transactions'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#published-section" class="nav-link">
                        <i class="fas fa-check-circle"></i>
                        <span>Published</span>
                        @if($approvedProperties->count() > 0)
                            <span class="nav-badge">{{ $approvedProperties->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pending-section" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <span>Pending</span>
                        @if($pendingProperties->count() > 0)
                            <span class="nav-badge">{{ $pendingProperties->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#rejected-section" class="nav-link">
                        <i class="fas fa-times-circle"></i>
                        <span>Rejected</span>
                        @if($rejectedProperties->count() > 0)
                            <span class="nav-badge nav-badge-danger">{{ $rejectedProperties->count() }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">QUICK LINKS</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link" target="_blank">
                        <i class="fas fa-home"></i>
                        <span>View Site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('list-property') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i>
                        <span>List Property</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>

<!-- Main Content Area -->
<main class="dashboard-main">
    <!-- Header -->
    <header class="landlord-header">
        <div class="header-title">
            <h1>Landlord Dashboard</h1>
            <p>Manage your properties and track your listings</p>
        </div>
    </header>

    <!-- Content Area -->
    <div class="landlord-content">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_properties'] }}</h3>
                    <p>Total Properties</p>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['active_listings'] }}</h3>
                    <p>Published Properties</p>
                </div>
            </div>

            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['pending_properties'] }}</h3>
                    <p>Pending Approval</p>
                </div>
            </div>

            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_likes'] }}</h3>
                    <p>Total Likes</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Sections -->

            <!-- Rental/Purchase Requests Section -->
            <div id="requests-section" class="content-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-envelope-open-text"></i>
                        Rental/Purchase Requests
                        <span class="badge badge-info">{{ $transactionRequests->count() }}</span>
                    </h2>
                </div>
                <div class="requests-table-wrapper">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Property</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactionRequests as $transaction)
                            <tr>
                                <td>
                                    <div class="request-buyer-cell">
                                        @if($transaction->user->profile_image)
                                            <img src="{{ Storage::url($transaction->user->profile_image) }}" alt="{{ $transaction->user->name }}" class="buyer-avatar">
                                        @else
                                            <div class="buyer-avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $transaction->user->name }}</strong>
                                            <small>{{ $transaction->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $transaction->property->title }}</strong>
                                </td>
                                <td>
                                    @if($transaction->type === 'rent')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-calendar-check"></i> Rental
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-handshake"></i> Purchase
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-pending">Pending Review</span>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="request-actions">
                                        <button class="btn btn-sm btn-primary" onclick="openRequestDetails({{ $transaction->id }})">
                                            <i class="fas fa-eye"></i> Review
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No rental or purchase requests at the moment.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            

            <!-- Active Transactions Section -->
            <div id="active-section" class="content-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-handshake"></i>
                        Active Transactions
                        <span class="badge badge-success">{{ $activeTransactions->count() }}</span>
                    </h2>
                </div>
                <div class="transactions-table-wrapper">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Property</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeTransactions as $transaction)
                            <tr>
                                <td>
                                    <div class="transaction-buyer-cell">
                                        @if($transaction->user->profile_image)
                                            <img src="{{ Storage::url($transaction->user->profile_image) }}" alt="{{ $transaction->user->name }}" class="buyer-avatar">
                                        @else
                                            <div class="buyer-avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <strong>{{ $transaction->user->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $transaction->property->title }}</td>
                                <td>
                                    @if($transaction->type === 'rent')
                                        <span class="badge badge-primary">Rental</span>
                                    @else
                                        <span class="badge badge-success">Purchase</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status === 'confirmed')
                                        <span class="status-badge status-confirmed">Awaiting Payment</span>
                                    @elseif($transaction->status === 'paid')
                                        <span class="status-badge status-paid">Payment Received</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($transaction->price, 2) }}</strong>
                                    <small class="text-muted">{{ $transaction->currency }}</small>
                                </td>
                                <td>
                                    <div class="transaction-actions">
                                        @if($transaction->status === 'confirmed')
                                            <button class="btn btn-sm btn-success" onclick="confirmPaymentModal({{ $transaction->id }})">
                                                <i class="fas fa-check"></i> Confirm Payment
                                            </button>
                                        @elseif($transaction->status === 'paid')
                                            <button class="btn btn-sm btn-primary" onclick="completeTransactionModal({{ $transaction->id }})">
                                                <i class="fas fa-flag-checkered"></i> Complete
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="requestRefundModal({{ $transaction->id }})">
                                                <i class="fas fa-undo"></i> Refund
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No active transactions at the moment.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            

        <!-- Published Properties Section -->
        @if($approvedProperties->count() > 0)
        <div id="published-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Published Properties
                    <span class="badge badge-success">{{ $approvedProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr>
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
                                <td>
                                    <span class="status-badge status-approved">Published</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Pending Properties Section -->
        @if($pendingProperties->count() > 0)
        <div id="pending-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Approval
                    <span class="badge badge-warning">{{ $pendingProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr class="table-row-pending">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
                                <td>
                                    <span class="status-badge status-pending">Pending</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Rejected Properties Section -->
        @if($rejectedProperties->count() > 0)
        <div id="rejected-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Rejected Properties
                    <span class="badge badge-danger">{{ $rejectedProperties->count() }}</span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedProperties as $property)
                            @php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            @endphp
                            <tr class="table-row-rejected">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong>{{ $property->title }}</strong>
                                            @if($property->bedroom_nb || $property->bathroom_nb)
                                                <span class="property-table-details">
                                                    @if($property->bedroom_nb)
                                                        <i class="fas fa-bed"></i> {{ $property->bedroom_nb }}
                                                    @endif
                                                    @if($property->bathroom_nb)
                                                        <i class="fas fa-bath"></i> {{ $property->bathroom_nb }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city }}, {{ $property->country }}
                                </td>
                                <td>{{ $property->property_type }}</td>
                                <td class="property-price-cell">${{ number_format($property->price) }}/month</td>
                                <td>
                                    <span class="status-badge status-rejected">Rejected</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn-icon" title="Edit & Resubmit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Empty State -->
        @if($approvedProperties->count() == 0 && $pendingProperties->count() == 0 && $rejectedProperties->count() == 0)
        <div class="content-section">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>No Properties Yet</h3>
                <p>You haven't listed any properties yet. Start by adding your first property!</p>
                <a href="{{ route('list-property') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> List Your First Property
                </a>
            </div>
        </div>
        @endif
            </div>
        </div>
    </div>
</main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    const sections = document.querySelectorAll('.content-section');

    // Show only the selected section and hide others
    function showSection(sectionId) {
        sections.forEach(s => {
            if (s.id === sectionId) {
                s.style.display = '';
                s.classList.add('active');
            } else {
                s.style.display = 'none';
                s.classList.remove('active');
            }
        });

        // Update active nav link
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href') || '';
            if (href === '#' + sectionId) {
                link.classList.add('active');
            }
        });

        // Update URL hash without jumping
        if (history.replaceState) {
            history.replaceState(null, null, '#' + sectionId);
        } else {
            window.location.hash = sectionId;
        }
    }

    // Handle click on nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    showSection(targetId);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });
    });

    // Initialize from URL hash or first section
    (function initVisibleSection() {
        let initial = window.location.hash ? window.location.hash.substring(1) : null;
        if (!initial) {
            for (const link of navLinks) {
                const href = link.getAttribute('href') || '';
                if (href.startsWith('#')) {
                    initial = href.substring(1);
                    break;
                }
            }
        }

        const sectionExists = initial && document.getElementById(initial);
        if (sectionExists) {
            showSection(initial);
        } else if (sections.length > 0) {
            showSection(sections[0].id);
        }
    })();

    // Transaction action modals
    function openRequestDetails(transactionId) {
        var block = document.getElementById('request-details-block-' + transactionId);
        if (block) {
            document.getElementById('requestDetailsContent').innerHTML = block.innerHTML;
        }
        document.getElementById('requestDetailsModal').style.display = 'flex';
    }

    function closeRequestDetailsModal() {
        document.getElementById('requestDetailsModal').style.display = 'none';
    }

    function confirmPaymentModal(transactionId) {
        if (confirm('Are you sure you have received the payment from the buyer?')) {
            confirmPayment(transactionId);
        }
    }

    function completeTransactionModal(transactionId) {
        if (confirm('Mark this transaction as completed?')) {
            completeTransaction(transactionId);
        }
    }

    function requestRefundModal(transactionId) {
        const reason = prompt('Enter reason for refund:');
        if (reason !== null) {
            requestRefund(transactionId, reason);
        }
    }

    function confirmPayment(transactionId) {
        fetch(`/transactions/${transactionId}/confirm-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Payment confirmed successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function completeTransaction(transactionId) {
        fetch(`/transactions/${transactionId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Transaction completed successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function requestRefund(transactionId, reason) {
        fetch(`/transactions/${transactionId}/request-refund`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            alert('Refund requested successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    function generateAndSendContract(transactionId) {
        // This would typically call an endpoint to generate a PDF contract
        // and send it to the buyer
        const contractPath = prompt('Generate contract. Enter file path or leave empty for default:');
        if (contractPath !== null) {
            fetch(`/transactions/${transactionId}/generate-contract`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ contract_path: contractPath || `contracts/transaction_${transactionId}.pdf` })
            })
            .then(response => response.json())
            .then(data => {
                alert('Contract generated and sent to buyer!');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating the contract.');
            });
        }
    }

    function downloadTransactionReport(transactionId) {
        window.open(`/transactions/${transactionId}/download-report`, '_blank');
    }
    // expose functions to global scope so inline `onclick` handlers work
    window.openRequestDetails = openRequestDetails;
    window.closeRequestDetailsModal = closeRequestDetailsModal;
    window.confirmPaymentModal = confirmPaymentModal;
    window.completeTransactionModal = completeTransactionModal;
    window.requestRefundModal = requestRefundModal;
    window.confirmPayment = confirmPayment;
    window.completeTransaction = completeTransaction;
    window.requestRefund = requestRefund;
    window.generateAndSendContract = generateAndSendContract;
    window.downloadTransactionReport = downloadTransactionReport;

    window.addEventListener('keydown', function(e){
        if(e.key === 'Escape') {
            closeRequestDetailsModal();
        }
    });
});
</script>

<script>
// Sidebar collapse behavior
document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('landlordSidebar');
  const toggle = document.getElementById('sidebarToggle');
  const container = document.querySelector('.main-container');
  if (!sidebar || !toggle || !container) return;

  const collapsed = localStorage.getItem('landlordSidebarCollapsed') === '1';
  if (collapsed) {
    sidebar.classList.add('collapsed');
    container.classList.add('sidebar-collapsed');
  }

  toggle.addEventListener('click', function () {
    sidebar.classList.toggle('collapsed');
    container.classList.toggle('sidebar-collapsed');

    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('landlordSidebarCollapsed', isCollapsed ? '1' : '0');
  });
});
</script>

{{-- Pre-rendered request details (one per transaction), hidden; JS copies into modal on open --}}
@foreach($transactionRequests as $transaction)
<div id="request-details-block-{{ $transaction->id }}" class="request-details-block" aria-hidden="true">
    @include('landlord.partials.request-details-content', ['transaction' => $transaction])
</div>
@endforeach
@foreach($activeTransactions as $transaction)
@if(!$transactionRequests->contains('id', $transaction->id))
<div id="request-details-block-{{ $transaction->id }}" class="request-details-block" aria-hidden="true">
    @include('landlord.partials.request-details-content', ['transaction' => $transaction])
</div>
@endif
@endforeach

<!-- Request Details Modal -->
<div id="requestDetailsModal" class="modal-overlay" onclick="if(event.target === this) closeRequestDetailsModal()">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Request Details</h2>
            <button class="modal-close" onclick="closeRequestDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="requestDetailsContent">
        </div>
    </div>
</div>

@endpush


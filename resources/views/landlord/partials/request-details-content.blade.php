@php
    $user = $transaction->user;
    $property = $transaction->property;
    $userPhone = $user ? ($user->phone_nb ?? $user->phone ?? '') : '';
    $statusLabel = $transaction->status ? str_replace('_', ' ', ucwords($transaction->status, '_')) : 'Pending';
    $statusClass = 'request-detail-status--' . ($transaction->status ?? 'pending');
@endphp

<div class="request-details-content">
    <h3 class="request-details-title"><i class="fas fa-clipboard-list"></i> Request Details</h3>

    <section class="request-detail-section">
        <h4 class="request-detail-section-title">
            <i class="fas fa-user"></i> Buyer information
        </h4>
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

    <section class="request-detail-section">
        <h4 class="request-detail-section-title">
            <i class="fas fa-file-invoice"></i> Transaction information
        </h4>
        <div class="request-details-grid">
            <div class="request-detail-item">
                <label><i class="fas fa-tag"></i> Request type</label>
                <span>{{ $transaction->type === 'rent' ? 'Rental Request' : 'Purchase Request' }}</span>
            </div>
            <div class="request-detail-item">
                <label><i class="fas fa-info-circle"></i> Status</label>
                <span class="request-detail-status {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div class="request-detail-item">
                <label><i class="fas fa-calendar-alt"></i> Request date</label>
                <span>{{ $transaction->created_at ? $transaction->created_at->format('M j, Y') : 'N/A' }}</span>
            </div>
            <div class="request-detail-item">
                <label><i class="fas fa-check-double"></i> Rules accepted</label>
                <span class="request-detail-rules-accepted {{ $transaction->rules_accepted ? 'request-detail-rules-accepted--yes' : 'request-detail-rules-accepted--no' }}">
                    {{ $transaction->rules_accepted ? 'Yes ✓' : 'No ✗' }}
                </span>
            </div>
            @if($transaction->type === 'rent')
            <div class="request-detail-item">
                <label><i class="fas fa-calendar-check"></i> Check-in</label>
                <span>{{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('M j, Y') : 'N/A' }}</span>
            </div>
            <div class="request-detail-item">
                <label><i class="fas fa-calendar-times"></i> Check-out</label>
                <span>{{ $transaction->end_date ? \Carbon\Carbon::parse($transaction->end_date)->format('M j, Y') : 'N/A' }}</span>
            </div>
            
            @endif
            @if($transaction->rules_exceptions && trim($transaction->rules_exceptions))
            <div class="request-detail-item request-detail-item--full">
                <label class="request-detail-label--warning"><i class="fas fa-exclamation-circle"></i> Rules exceptions / concerns</label>
                <div class="request-detail-box request-detail-box--warning">{{ $transaction->rules_exceptions }}</div>
            </div>
            @endif
            @if($transaction->notes && trim($transaction->notes))
            <div class="request-detail-item request-detail-item--full">
                <label class="request-detail-label--notes"><i class="fas fa-comment-dots"></i> Buyer notes</label>
                <div class="request-detail-box request-detail-box--notes">{{ $transaction->notes }}</div>
            </div>
            @endif
        </div>
    </section>

    @if($property)
    <section class="request-detail-section">
        <h4 class="request-detail-section-title">
            <i class="fas fa-home"></i> Property information
        </h4>
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
                <span>
                    ${{ number_format($property->price) }}
                    @if(($property->listing_type ?? null) === 'rent')
                        /{{ $property->price_duration ?? 'month' }}
                    @endif
                </span>
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
        @php
            $propertyRules = $property->rules ?? collect();
            $propertyAmenities = $property->amenities ?? collect();
        @endphp
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

    <div class="request-details-actions">
        <form method="POST" action="{{ route('transactions.generate-contract', $transaction) }}" class="request-details-action-form">
            @csrf
            <input type="hidden" name="contract_path" value="contracts/transaction_{{ $transaction->id }}.pdf">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Generate Contract & Send
            </button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="downloadTransactionReport({{ $transaction->id }})">
            <i class="fas fa-download"></i> Download Report
        </button>
        <a href="{{ url('/transactions/' . $transaction->id . '/edit') }}" class="btn btn-secondary" target="_blank">
            <i class="fas fa-edit"></i> Edit Request
        </a>
        <button type="button" class="btn btn-secondary" onclick="closeRequestDetailsModal()">Close</button>
    </div>
</div>

@php
    $primaryImage = $property->images->first();
    $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
    $failedResubmitId = session('resubmit_failed_property_id');
    $resubmitNotesValue = ($failedResubmitId && (int) $failedResubmitId === (int) $property->id) ? old('resubmit_notes') : '';
@endphp
<div class="rejected-property-modal-body">
    <div class="rejected-property-modal-hero">
        <img src="{{ $imageUrl }}" alt="" class="rejected-property-modal-image">
        <div class="rejected-property-modal-title-block">
            <h3 class="rejected-property-modal-title">{{ $property->title }}</h3>
            <p class="rejected-property-modal-meta">
                <i class="fas fa-map-marker-alt"></i>
                {{ $property->city }}, {{ $property->country }}
            </p>
            <p class="rejected-property-modal-meta">
                <span>{{ $property->property_type }}</span>
                <span class="rejected-property-modal-sep">·</span>
                <span>${{ number_format($property->price) }}
                    @if(($property->listing_type ?? null) === 'rent')
                        /{{ $property->price_duration ?? 'month' }}
                    @endif
                </span>
            </p>
        </div>
    </div>
    <div class="rejection-feedback-box">
        <h4 class="rejection-feedback-heading"><i class="fas fa-exclamation-circle"></i> Admin feedback</h4>
        <p class="rejection-feedback-text">{{ $property->rejection_reason ? $property->rejection_reason : 'No rejection details were stored for this listing.' }}</p>
    </div>
    <p class="rejected-property-modal-hint">Update your listing to address the feedback, then submit it again for review.</p>
    <div class="rejected-property-modal-actions">
        <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i> Edit listing
        </a>
        <a href="{{ route('properties.show', $property) }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt"></i> Full preview
        </a>
        <form method="POST" action="{{ route('landlord.properties.resubmit', $property) }}" class="rejected-resubmit-form" onsubmit="return confirm('Send this listing to the admin for review with your notes?');">
            @csrf
            <label class="resubmit-notes-label" for="resubmit-notes-{{ $property->id }}">What did you change? <span class="resubmit-notes-required">(required)</span></label>
            <p class="resubmit-notes-hint">Describe the updates you made so the admin can verify them before approving.</p>
            <textarea
                id="resubmit-notes-{{ $property->id }}"
                name="resubmit_notes"
                class="resubmit-notes-textarea"
                rows="4"
                required
                minlength="15"
                maxlength="5000"
                placeholder="Example: Updated photos of the kitchen, corrected the monthly rent, and expanded the description as requested."
            >{{ $resubmitNotesValue }}</textarea>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane"></i> Resubmit for approval
            </button>
        </form>
    </div>
</div>

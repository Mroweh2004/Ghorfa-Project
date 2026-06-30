@extends('layouts.app')
@section('title', 'Become a Landlord')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
<style>
    .verify-doc {
        margin: 0 0 1.75rem;
        padding: 1.35rem 1.35rem 1.5rem;
        background: linear-gradient(165deg, #fafbff 0%, #f4f6fb 100%);
        border: 1px solid rgba(108, 99, 255, 0.12);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(45, 52, 54, 0.06);
    }
    .verify-doc__title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.05rem;
        font-weight: 700;
        color: #2d3436;
        margin: 0 0 0.5rem;
        letter-spacing: -0.02em;
    }
    .verify-doc__title i {
        color: var(--primary-color, #6C63FF);
        font-size: 1.1rem;
    }
    .verify-doc__hint {
        font-size: 0.875rem;
        color: var(--text-light, #636E72);
        line-height: 1.55;
        margin: 0 0 1.25rem;
        max-width: 42rem;
    }
    .verify-doc__hint strong { color: #2d3436; font-weight: 600; }

    .doc-type-requirements {
        margin: 0 0 1.25rem;
        padding: 0.9rem 1rem;
        border-radius: 12px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        font-size: 0.8125rem;
        color: #1e40af;
        line-height: 1.55;
    }

    .doc-type-requirements.is-trade {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .doc-type-requirements h3 {
        margin: 0 0 0.4rem;
        font-size: 0.88rem;
        font-weight: 700;
        color: inherit;
    }

    .doc-type-requirements ul {
        margin: 0;
        padding-left: 1.15rem;
    }

    .doc-type-requirements li + li {
        margin-top: 0.25rem;
    }

    .doc-fields-panel {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .doc-upload-card--optional-back {
        border-style: dashed;
        background: #fafafa;
    }

    .doc-upload-card__status.is-optional {
        color: #b45309;
        background: #fffbeb;
    }

    .doc-license-mock {
        width: min(100%, 190px);
        aspect-ratio: 1 / 1.35;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
    }

    .doc-license-mock::before {
        content: '';
        position: absolute;
        left: 10%;
        right: 10%;
        top: 12%;
        height: 12%;
        border-radius: 4px;
        background: #cbd5e1;
    }

    .doc-license-mock::after {
        content: '';
        position: absolute;
        left: 10%;
        right: 10%;
        top: 30%;
        bottom: 14%;
        border-radius: 6px;
        border: 1px dashed #cbd5e1;
        background: repeating-linear-gradient(
            180deg,
            #f8fafc 0,
            #f8fafc 10px,
            #eef2f7 10px,
            #eef2f7 12px
        );
    }

    .doc-mock--trade { display: none; }
    .doc-type-is-trade .doc-mock--national { display: none; }
    .doc-type-is-trade .doc-mock--trade { display: block; }

    .doc-upload-tips__trade-only { display: none; }
    .doc-type-is-trade .doc-upload-tips__trade-only { display: list-item; }

    .document-type-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.85rem;
    }
    .doc-type-card {
        position: relative;
        cursor: pointer;
        border-radius: 14px;
        margin: 0;
    }
    .doc-type-card__input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
    }
    .doc-type-card__face {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.35rem;
        padding: 1.1rem 1.15rem;
        min-height: 5.5rem;
        background: #fff;
        border: 2px solid #e8eaef;
        border-radius: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
    }
    .doc-type-card:hover .doc-type-card__face {
        border-color: rgba(108, 99, 255, 0.35);
        box-shadow: 0 6px 20px rgba(108, 99, 255, 0.08);
    }
    .doc-type-card__input:focus-visible + .doc-type-card__face {
        outline: 2px solid var(--primary-color, #6C63FF);
        outline-offset: 2px;
    }
    .doc-type-card__input:checked + .doc-type-card__face {
        border-color: var(--primary-color, #6C63FF);
        background: linear-gradient(145deg, rgba(108, 99, 255, 0.06) 0%, #fff 55%);
        box-shadow: 0 4px 16px rgba(108, 99, 255, 0.12);
    }
    .doc-type-card__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        background: rgba(108, 99, 255, 0.1);
        color: var(--primary-color, #6C63FF);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .doc-type-card__input:checked + .doc-type-card__face .doc-type-card__icon {
        background: var(--primary-color, #6C63FF);
        color: #fff;
    }
    .doc-type-card__label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #2d3436;
    }
    .doc-type-card__sub {
        font-size: 0.78rem;
        color: var(--text-light, #636E72);
        line-height: 1.35;
    }

    .doc-uploads-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.35rem;
        margin-top: 0.5rem;
    }
    @media (min-width: 640px) {
        .doc-uploads-grid { grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    }

    .doc-upload-card {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1.1rem 1.1rem 1.15rem;
        background: #fff;
        border: 1px solid #e8eaef;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
    }

    .doc-upload-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .doc-upload-card__step {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary-color, #6C63FF);
        background: rgba(108, 99, 255, 0.1);
        padding: 0.28rem 0.55rem;
        border-radius: 999px;
    }

    .doc-upload-card__status {
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: #f1f5f9;
    }

    .doc-upload-card__status.is-done {
        color: #047857;
        background: #ecfdf5;
    }

    .doc-upload-card__title {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.02em;
    }

    .doc-upload-card__desc {
        margin: -0.35rem 0 0;
        font-size: 0.8125rem;
        color: #64748b;
        line-height: 1.45;
    }

    .doc-dropzone {
        position: relative;
        display: block;
        margin: 0;
        cursor: pointer;
        border-radius: 14px;
        border: 2px dashed #cbd5e1;
        background: linear-gradient(165deg, #fafbff 0%, #f8fafc 55%, #f1f5f9 100%);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, transform 0.15s ease;
        overflow: hidden;
        min-height: 220px;
    }

    .doc-dropzone:hover,
    .doc-dropzone:focus-within {
        border-color: rgba(108, 99, 255, 0.55);
        box-shadow: 0 8px 28px rgba(108, 99, 255, 0.1);
        background: linear-gradient(165deg, #f5f3ff 0%, #fafbff 50%, #f8fafc 100%);
    }

    .doc-dropzone.is-dragover {
        border-color: var(--primary-color, #6C63FF);
        border-style: solid;
        transform: scale(1.01);
    }

    .doc-dropzone--filled {
        border-style: solid;
        border-color: #86efac;
        background: #f0fdf4;
        min-height: auto;
    }

    .doc-dropzone--filled .doc-dropzone__empty {
        display: none;
    }

    .doc-dropzone__input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .doc-dropzone__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.85rem;
        padding: 1.35rem 1rem 1.5rem;
        text-align: center;
        min-height: 220px;
    }

    .doc-id-mock {
        width: min(100%, 210px);
        aspect-ratio: 1.586 / 1;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08), inset 0 0 0 1px rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
    }

    .doc-id-mock--front::before {
        content: '';
        position: absolute;
        left: 12%;
        top: 22%;
        width: 28%;
        aspect-ratio: 1;
        border-radius: 8px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .doc-id-mock--front::after {
        content: '';
        position: absolute;
        right: 10%;
        top: 20%;
        width: 48%;
        height: 10%;
        border-radius: 4px;
        background: #e2e8f0;
        box-shadow: 0 14px 0 #eef2f7, 0 28px 0 #e2e8f0, 0 42px 0 #eef2f7;
    }

    .doc-id-mock--back::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 14%;
        height: 22%;
        background: linear-gradient(180deg, #334155 0%, #1e293b 100%);
    }

    .doc-id-mock--back::after {
        content: '';
        position: absolute;
        left: 10%;
        right: 10%;
        bottom: 18%;
        height: 28%;
        border-radius: 6px;
        border: 2px dashed #cbd5e1;
        background: repeating-linear-gradient(
            90deg,
            #f1f5f9 0,
            #f1f5f9 8px,
            #e2e8f0 8px,
            #e2e8f0 10px
        );
    }

    .doc-id-mock__badge {
        position: absolute;
        top: 8px;
        left: 8px;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #fff;
        background: var(--primary-color, #6C63FF);
        padding: 0.2rem 0.45rem;
        border-radius: 6px;
        z-index: 1;
    }

    .doc-dropzone__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    .doc-dropzone__cta i {
        color: var(--primary-color, #6C63FF);
        font-size: 1.1rem;
    }

    .doc-dropzone__formats {
        font-size: 0.78rem;
        color: #94a3b8;
    }

    .doc-upload-tips {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .doc-upload-tips li {
        display: flex;
        align-items: flex-start;
        gap: 0.45rem;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.4;
    }

    .doc-upload-tips li i {
        color: #22c55e;
        margin-top: 0.1rem;
        font-size: 0.7rem;
    }

    .doc-preview {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #0f172a;
        border: none;
        min-height: 180px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .doc-dropzone--filled .doc-preview.is-visible {
        display: flex;
    }

    .doc-preview.is-visible { display: flex; }
    .doc-preview__img {
        display: block;
        width: 100%;
        height: auto;
        max-height: min(52vh, 480px);
        object-fit: contain;
        object-position: center;
        image-rendering: auto;
        -ms-interpolation-mode: bicubic;
    }
    .doc-preview__meta {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 0.45rem 0.65rem;
        font-size: 0.72rem;
        color: #f8fafc;
        background: linear-gradient(transparent, rgba(15, 23, 42, 0.82));
        line-height: 1.35;
        pointer-events: none;
    }
    .doc-preview__clear {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 2;
        width: 2rem;
        height: 2rem;
        border: none;
        border-radius: 8px;
        background: rgba(15, 23, 42, 0.75);
        color: #fff;
        font-size: 1.1rem;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s ease;
    }
    .doc-preview__clear:hover { background: #0f172a; }

    .doc-dropzone__replace {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        display: none;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.95);
        color: #1e293b;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
        pointer-events: none;
    }

    .doc-dropzone--filled .doc-dropzone__replace {
        display: inline-flex;
    }

    .form-hint { font-size: 0.8125rem; color: #64748b; margin: 0; }

    .face-verify-block {
        margin-top: 0.5rem;
        padding: 1.25rem 1.35rem;
        background: linear-gradient(165deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .face-verify-block__title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        color: #2d3436;
        margin: 0 0 0.4rem;
    }
    .face-verify-block__title i {
        color: var(--primary-color, #6C63FF);
    }
    .face-verify-block__hint {
        font-size: 0.875rem;
        color: var(--text-light, #636E72);
        line-height: 1.5;
        margin: 0 0 1rem;
        max-width: 40rem;
    }
    .face-camera-ui { margin-top: 0.25rem; }
    .face-camera-video-wrap {
        display: none;
        margin-top: 0.75rem;
        border-radius: 14px;
        overflow: hidden;
        background: #0f172a;
        border: 2px solid #e2e8f0;
        max-width: 100%;
    }
    .face-camera-video-wrap.is-active { display: block; }
    .face-camera-video-wrap video {
        display: block;
        width: 100%;
        max-height: min(50vh, 420px);
        object-fit: cover;
        vertical-align: middle;
    }
    .face-camera-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
        margin-top: 0.85rem;
    }
    .face-camera-actions .btn-camera {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: opacity 0.15s ease, transform 0.1s ease;
    }
    .face-camera-actions .btn-camera:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .btn-camera-primary {
        background: var(--primary-color, #6C63FF);
        color: #fff;
    }
    .btn-camera-primary:hover:not(:disabled) {
        filter: brightness(1.05);
    }
    .btn-camera-secondary {
        background: #e2e8f0;
        color: #334155;
    }
    .btn-camera-secondary:hover:not(:disabled) {
        background: #cbd5e1;
    }
    .face-camera-error {
        display: none;
        margin-top: 0.65rem;
        padding: 0.65rem 0.85rem;
        font-size: 0.8125rem;
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
    }
    .face-camera-error.is-visible { display: block; }
    .face-photo-input-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
@endpush

@section('content')
<main>
    <div class="profile-container">
        <div class="profile-card profile-card-shadow">
            <div class="profile-header">
                <h1>Become a Landlord</h1>
                <p>Fill out the form below to apply for a landlord account. Once approved, you'll be able to list and manage your properties.</p>
            </div>

            <form action="{{ route('landlord.submit-application') }}" method="POST" class="profile-form" enctype="multipart/form-data">
                @csrf
                @if(!auth()->user()->phone_nb)
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone_nb) }}" placeholder="Enter your phone number">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                @endif
                <div class="verify-doc" role="group" aria-labelledby="verify-doc-title">
                    <h2 class="verify-doc__title" id="verify-doc-title">
                        <i class="fas fa-shield-halved" aria-hidden="true"></i>
                        Verification document
                    </h2>
                    <p class="verify-doc__hint" id="verify_doc_hint">Choose <strong>one</strong> document type below. The fields underneath will update to match what we need to verify you.</p>

                    <div class="doc-type-requirements" id="doc_type_requirements" aria-live="polite">
                        <h3 class="doc-type-requirements__title" id="doc_type_requirements_title">National ID — what we need</h3>
                        <ul class="doc-type-requirements__list" id="doc_type_requirements_list">
                            <li><strong>ID number</strong> — as printed on your card</li>
                            <li><strong>Front photo</strong> — face and number visible</li>
                            <li><strong>Back photo</strong> — barcode / security side</li>
                        </ul>
                    </div>
                    <div class="document-type-cards">
                        <label class="doc-type-card">
                            <input class="doc-type-card__input" type="radio" name="document_type" value="national_id" {{ old('document_type', 'national_id') === 'national_id' ? 'checked' : '' }}>
                            <span class="doc-type-card__face">
                                <span class="doc-type-card__icon" aria-hidden="true"><i class="fas fa-id-card"></i></span>
                                <span class="doc-type-card__label">National ID</span>
                                <span class="doc-type-card__sub">Government-issued photo ID</span>
                            </span>
                        </label>
                        <label class="doc-type-card">
                            <input class="doc-type-card__input" type="radio" name="document_type" value="trade_license" {{ old('document_type') === 'trade_license' ? 'checked' : '' }}>
                            <span class="doc-type-card__face">
                                <span class="doc-type-card__icon" aria-hidden="true"><i class="fas fa-file-contract"></i></span>
                                <span class="doc-type-card__label">Trade license</span>
                                <span class="doc-type-card__sub">Business / commercial registration</span>
                            </span>
                        </label>
                    </div>
                    @error('document_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="document_number_group">
                    <label for="document_number" id="document_number_label">National ID number</label>
                    <input type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" placeholder="Enter the number exactly as on the document" required autocomplete="off">
                    <p class="form-hint" id="document_number_hint">Enter the number exactly as it appears on your document.</p>
                    @error('document_number')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="doc-uploads-grid" id="doc_uploads_grid">
                    <div class="doc-upload-card">
                        <div class="doc-upload-card__header">
                            <span class="doc-upload-card__step" id="front_step_label">Step 1 · Front</span>
                            <span class="doc-upload-card__status" id="status_document_front" data-default="Required">Required</span>
                        </div>
                        <h3 class="doc-upload-card__title" id="document_front_label">Front of national ID</h3>
                        <p class="doc-upload-card__desc" id="document_front_desc">Upload a clear photo of the front — your face and ID number must be readable.</p>

                        <label class="doc-dropzone" for="document_front" id="dropzone_document_front">
                            <input type="file" class="doc-dropzone__input" id="document_front" name="document_front" accept="image/jpeg,image/png,image/jpg,image/webp" required data-doc-preview="preview_front" data-status-target="status_document_front">
                            <div class="doc-dropzone__empty">
                                <div class="doc-id-mock doc-id-mock--front doc-mock--national" aria-hidden="true">
                                    <span class="doc-id-mock__badge">Front</span>
                                </div>
                                <div class="doc-license-mock doc-mock--trade" aria-hidden="true">
                                    <span class="doc-id-mock__badge">Main page</span>
                                </div>
                                <span class="doc-dropzone__cta" id="document_front_cta"><i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload front photo</span>
                                <span class="doc-dropzone__formats">JPEG, PNG, or WebP · max 5 MB</span>
                            </div>
                            <div class="doc-preview" id="preview_front" hidden>
                                <button type="button" class="doc-preview__clear" data-clear-input="document_front" aria-label="Remove front image">&times;</button>
                                <img class="doc-preview__img" alt="Front document preview" decoding="async">
                                <span class="doc-preview__meta" aria-hidden="true"></span>
                                <span class="doc-dropzone__replace"><i class="fas fa-rotate" aria-hidden="true"></i> Tap to replace</span>
                            </div>
                        </label>

                        @error('document_front')
                            <span class="error-message">{{ $message }}</span>
                        @enderror

                        <ul class="doc-upload-tips" id="document_front_tips" aria-label="Photo tips for document front">
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Use good lighting — avoid shadows on the card</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Keep the full card inside the frame, no cut-off edges</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Hold the phone steady so text stays sharp</li>
                        </ul>
                    </div>

                    <div class="doc-upload-card" id="back_upload_card">
                        <div class="doc-upload-card__header">
                            <span class="doc-upload-card__step" id="back_step_label">Step 2 · Back</span>
                            <span class="doc-upload-card__status" id="status_document_back" data-default="Required" data-optional-label="Optional">Required</span>
                        </div>
                        <h3 class="doc-upload-card__title" id="document_back_label">Back of national ID</h3>
                        <p class="doc-upload-card__desc" id="document_back_desc">Upload a clear photo of the back — barcode and security details visible.</p>

                        <label class="doc-dropzone" for="document_back" id="dropzone_document_back">
                            <input type="file" class="doc-dropzone__input" id="document_back" name="document_back" accept="image/jpeg,image/png,image/jpg,image/webp" required data-doc-preview="preview_back" data-status-target="status_document_back">
                            <div class="doc-dropzone__empty">
                                <div class="doc-id-mock doc-id-mock--back doc-mock--national" aria-hidden="true">
                                    <span class="doc-id-mock__badge">Back</span>
                                </div>
                                <div class="doc-license-mock doc-mock--trade" aria-hidden="true">
                                    <span class="doc-id-mock__badge">Extra page</span>
                                </div>
                                <span class="doc-dropzone__cta" id="document_back_cta"><i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload back photo</span>
                                <span class="doc-dropzone__formats">JPEG, PNG, or WebP · max 5 MB</span>
                            </div>
                            <div class="doc-preview" id="preview_back" hidden>
                                <button type="button" class="doc-preview__clear" data-clear-input="document_back" aria-label="Remove back image">&times;</button>
                                <img class="doc-preview__img" alt="Back document preview" decoding="async">
                                <span class="doc-preview__meta" aria-hidden="true"></span>
                                <span class="doc-dropzone__replace"><i class="fas fa-rotate" aria-hidden="true"></i> Tap to replace</span>
                            </div>
                        </label>

                        @error('document_back')
                            <span class="error-message">{{ $message }}</span>
                        @enderror

                        <ul class="doc-upload-tips" id="document_back_tips" aria-label="Photo tips for document back or extra page">
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Flat surface — reduce glare and reflections</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> All corners visible; don’t cover details with fingers</li>
                            <li class="doc-upload-tips__trade-only" id="back_tip_trade"><i class="fas fa-check-circle" aria-hidden="true"></i> Skip this if your license is a single-page document</li>
                        </ul>
                    </div>
                </div>

                <div class="face-verify-block" role="group" aria-labelledby="face-verify-title">
                    <h2 class="face-verify-block__title" id="face-verify-title">
                        <i class="fas fa-camera" aria-hidden="true"></i>
                        Face photo (camera only)
                    </h2>
                    <p class="face-verify-block__hint">Your device camera opens here — you cannot pick an old photo from the gallery. Center your face, use good light, then tap <strong>Capture photo</strong>. Allow camera access when the browser asks.</p>
                    <div class="form-group doc-upload-block" style="margin-bottom: 0; position: relative;">
                        <span id="face_photo_label" class="face-verify-block__title" style="font-size: 0.95rem; margin-bottom: 0.35rem;">Live camera</span>
                        <input type="file" id="face_photo" name="face_photo" class="face-photo-input-hidden" accept="image/jpeg,image/png,image/jpg,image/webp" required data-doc-preview="preview_face" tabindex="-1" aria-hidden="true">
                        <canvas id="faceCaptureCanvas" hidden aria-hidden="true"></canvas>
                        <div class="face-camera-ui">
                            <div id="faceCameraError" class="face-camera-error" role="alert"></div>
                            <div id="faceVideoWrap" class="face-camera-video-wrap">
                                <video id="faceVideo" playsinline muted autoplay></video>
                            </div>
                            <div class="face-camera-actions">
                                <button type="button" class="btn-camera btn-camera-primary" id="faceBtnStart">
                                    <i class="fas fa-video" aria-hidden="true"></i> Open camera
                                </button>
                                <button type="button" class="btn-camera btn-camera-primary" id="faceBtnCapture" disabled>
                                    <i class="fas fa-camera" aria-hidden="true"></i> Capture photo
                                </button>
                                <button type="button" class="btn-camera btn-camera-secondary" id="faceBtnStop" disabled>
                                    Stop camera
                                </button>
                            </div>
                        </div>
                        <p class="form-hint">Photo is saved as JPEG from the camera. Max 5&nbsp;MB after capture (we shrink quality automatically if needed).</p>
                        @error('face_photo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="doc-preview" id="preview_face" hidden>
                            <button type="button" class="doc-preview__clear" data-clear-input="face_photo" data-face-camera-clear="1" aria-label="Remove face photo and retake">&times;</button>
                            <img class="doc-preview__img" alt="Captured face preview" decoding="async">
                            <span class="doc-preview__meta" aria-hidden="true"></span>
                        </div>
                    </div>
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

@push('scripts')
<script>
(function () {
    var radios = document.querySelectorAll('input[name="document_type"]');
    var label = document.getElementById('document_number_label');
    var input = document.getElementById('document_number');
    var frontLbl = document.getElementById('document_front_label');
    var backLbl = document.getElementById('document_back_label');
    var frontDesc = document.getElementById('document_front_desc');
    var backDesc = document.getElementById('document_back_desc');
    var previewUrls = { document_front: null, document_back: null, face_photo: null };
    var faceStream = null;
    var faceVideo = document.getElementById('faceVideo');
    var faceVideoWrap = document.getElementById('faceVideoWrap');
    var faceCanvas = document.getElementById('faceCaptureCanvas');
    var faceInput = document.getElementById('face_photo');
    var faceErr = document.getElementById('faceCameraError');
    var faceBtnStart = document.getElementById('faceBtnStart');
    var faceBtnCapture = document.getElementById('faceBtnCapture');
    var faceBtnStop = document.getElementById('faceBtnStop');
    var MAX_FACE_BYTES = 5 * 1024 * 1024;

    function showFaceError(msg) {
        if (!faceErr) return;
        faceErr.textContent = msg;
        faceErr.classList.add('is-visible');
    }
    function hideFaceError() {
        if (!faceErr) return;
        faceErr.textContent = '';
        faceErr.classList.remove('is-visible');
    }
    function stopFaceStream() {
        if (faceStream) {
            faceStream.getTracks().forEach(function (t) { t.stop(); });
            faceStream = null;
        }
        if (faceVideo) faceVideo.srcObject = null;
        if (faceVideoWrap) faceVideoWrap.classList.remove('is-active');
        if (faceBtnCapture) faceBtnCapture.disabled = true;
        if (faceBtnStop) faceBtnStop.disabled = true;
        if (faceBtnStart) faceBtnStart.disabled = false;
    }
    function setFaceFileFromBlob(blob) {
        if (!faceInput || !blob) return;
        var file = new File([blob], 'face-capture.jpg', { type: 'image/jpeg' });
        var dt = new DataTransfer();
        dt.items.add(file);
        faceInput.files = dt.files;
        faceInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    function buildFaceBlob(video, canvas, maxBytes, cb) {
        if (!video || !canvas || !video.videoWidth || !video.videoHeight) {
            cb(null);
            return;
        }
        var ctx = canvas.getContext('2d');
        var scale = 1;
        function drawAndBlob() {
            var w = Math.round(video.videoWidth * scale);
            var h = Math.round(video.videoHeight * scale);
            if (w < 80 || h < 80) {
                cb(null);
                return;
            }
            canvas.width = w;
            canvas.height = h;
            ctx.drawImage(video, 0, 0, w, h);
            function compress(q) {
                canvas.toBlob(function (blob) {
                    if (!blob) {
                        cb(null);
                        return;
                    }
                    if (blob.size <= maxBytes) {
                        cb(blob);
                        return;
                    }
                    if (q >= 0.52) {
                        compress(q - 0.07);
                        return;
                    }
                    if (scale > 0.32) {
                        scale *= 0.78;
                        drawAndBlob();
                        return;
                    }
                    cb(blob);
                }, 'image/jpeg', q);
            }
            compress(0.92);
        }
        drawAndBlob();
    }

    function revokePreview(inputId) {
        if (previewUrls[inputId]) {
            URL.revokeObjectURL(previewUrls[inputId]);
            previewUrls[inputId] = null;
        }
    }

    function setUploadStatus(fileInput, uploaded) {
        var statusId = fileInput.getAttribute('data-status-target');
        var statusEl = statusId ? document.getElementById(statusId) : null;
        var dropzone = fileInput.closest('.doc-dropzone');
        if (dropzone) {
            dropzone.classList.toggle('doc-dropzone--filled', !!uploaded);
        }
        if (statusEl) {
            if (uploaded) {
                statusEl.textContent = 'Uploaded';
                statusEl.classList.add('is-done');
                statusEl.classList.remove('is-optional');
            } else {
                var defaultLabel = statusEl.getAttribute('data-default') || 'Required';
                statusEl.textContent = defaultLabel;
                statusEl.classList.remove('is-done');
                statusEl.classList.toggle('is-optional', defaultLabel === 'Optional');
            }
        }
    }

    function bindDropzone(fileInput) {
        var dropzone = fileInput.closest('.doc-dropzone');
        if (!dropzone) return;

        ['dragenter', 'dragover'].forEach(function (evt) {
            dropzone.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (evt) {
            dropzone.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });
        dropzone.addEventListener('drop', function (e) {
            var files = e.dataTransfer && e.dataTransfer.files;
            if (!files || !files.length) return;
            var dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    function bindImagePreview(fileInput) {
        bindDropzone(fileInput);
        var previewId = fileInput.getAttribute('data-doc-preview');
        if (!previewId) return;
        var box = document.getElementById(previewId);
        if (!box) return;
        var img = box.querySelector('.doc-preview__img');
        var meta = box.querySelector('.doc-preview__meta');

        fileInput.addEventListener('change', function () {
            revokePreview(fileInput.id);
            var file = fileInput.files && fileInput.files[0];
            if (!file || !file.type.match(/^image\//)) {
                box.classList.remove('is-visible');
                box.hidden = true;
                if (img) { img.removeAttribute('src'); }
                if (meta) meta.textContent = '';
                setUploadStatus(fileInput, false);
                return;
            }
            var url = URL.createObjectURL(file);
            previewUrls[fileInput.id] = url;
            img.src = url;
            img.onload = function () {
                var w = img.naturalWidth;
                var h = img.naturalHeight;
                var mb = (file.size / (1024 * 1024)).toFixed(2);
                meta.textContent = file.name + ' · ' + w + '×' + h + ' px · ' + mb + ' MB';
            };
            box.classList.add('is-visible');
            box.hidden = false;
            setUploadStatus(fileInput, true);
        });
    }

    document.querySelectorAll('input[data-doc-preview]').forEach(bindImagePreview);

    document.querySelectorAll('.doc-preview__clear').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.getAttribute('data-clear-input');
            var fi = id ? document.getElementById(id) : null;
            if (!fi) return;
            if (id === 'face_photo') stopFaceStream();
            revokePreview(fi.id);
            fi.value = '';
            fi.dispatchEvent(new Event('change', { bubbles: true }));
            var previewId = fi.getAttribute('data-doc-preview');
            var box = previewId ? document.getElementById(previewId) : null;
            if (box) {
                box.classList.remove('is-visible');
                box.hidden = true;
                var im = box.querySelector('.doc-preview__img');
                var me = box.querySelector('.doc-preview__meta');
                if (im) im.removeAttribute('src');
                if (me) me.textContent = '';
            }
            setUploadStatus(fi, false);
        });
    });

    if (faceBtnStart && faceBtnCapture && faceBtnStop && faceVideo && faceCanvas && faceInput) {
        faceBtnStart.addEventListener('click', function () {
            hideFaceError();
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                showFaceError('Camera API is not available. Use HTTPS, a recent browser, and a device with a camera.');
                return;
            }
            faceBtnStart.disabled = true;
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'user' }, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false,
            }).then(function (stream) {
                faceStream = stream;
                faceVideo.srcObject = stream;
                if (faceVideoWrap) faceVideoWrap.classList.add('is-active');
                return faceVideo.play();
            }).then(function () {
                faceBtnCapture.disabled = false;
                faceBtnStop.disabled = false;
            }).catch(function () {
                showFaceError('Could not open the camera. Allow camera access in your browser settings, or close other apps using the camera.');
                faceBtnStart.disabled = false;
            });
        });
        faceBtnStop.addEventListener('click', function () {
            stopFaceStream();
            hideFaceError();
        });
        faceBtnCapture.addEventListener('click', function () {
            if (!faceStream || !faceVideo || faceVideo.readyState < 2) {
                showFaceError('Wait for the video to start, then capture again.');
                return;
            }
            hideFaceError();
            buildFaceBlob(faceVideo, faceCanvas, MAX_FACE_BYTES, function (blob) {
                if (!blob) {
                    showFaceError('Capture failed. Try “Stop camera”, then “Open camera” again.');
                    return;
                }
                setFaceFileFromBlob(blob);
                stopFaceStream();
            });
        });
    }

    window.addEventListener('beforeunload', function () {
        stopFaceStream();
    });

    if (!radios.length || !label || !input) return;

    var numberHint = document.getElementById('document_number_hint');
    var reqPanel = document.getElementById('doc_type_requirements');
    var reqTitle = document.getElementById('doc_type_requirements_title');
    var reqList = document.getElementById('doc_type_requirements_list');
    var uploadsGrid = document.getElementById('doc_uploads_grid');
    var backCard = document.getElementById('back_upload_card');
    var backInput = document.getElementById('document_back');
    var backStatus = document.getElementById('status_document_back');
    var frontStep = document.getElementById('front_step_label');
    var backStep = document.getElementById('back_step_label');
    var frontCta = document.getElementById('document_front_cta');
    var backCta = document.getElementById('document_back_cta');
    var frontTips = document.getElementById('document_front_tips');

    function sync() {
        var v = document.querySelector('input[name="document_type"]:checked');
        if (!v) return;
        var isTrade = v.value === 'trade_license';

        if (reqPanel) reqPanel.classList.toggle('is-trade', isTrade);
        if (uploadsGrid) uploadsGrid.classList.toggle('doc-type-is-trade', isTrade);
        if (backCard) backCard.classList.toggle('doc-upload-card--optional-back', isTrade);

        if (isTrade) {
            label.textContent = 'Trade license number';
            input.placeholder = 'Enter your trade license / registration number';
            if (numberHint) numberHint.textContent = 'The license or commercial registration number printed on your document.';
            if (reqTitle) reqTitle.textContent = 'Trade license — what we need';
            if (reqList) {
                reqList.innerHTML =
                    '<li><strong>License number</strong> — registration number on the certificate</li>' +
                    '<li><strong>Main page photo</strong> — business name and number clearly visible</li>' +
                    '<li><strong>Second page</strong> — optional (stamp, terms, or extra sheet)</li>';
            }
            if (frontStep) frontStep.textContent = 'Step 1 · Main page';
            if (backStep) backStep.textContent = 'Step 2 · Extra page';
            if (frontLbl) frontLbl.textContent = 'Trade license — main page';
            if (backLbl) backLbl.textContent = 'Extra page (optional)';
            if (frontDesc) frontDesc.textContent = 'Upload a clear photo of the main page showing your business name and license number.';
            if (backDesc) backDesc.textContent = 'If your license has another page (stamp, conditions, or reverse side), upload it here. Single-page licenses can skip this.';
            if (frontCta) frontCta.innerHTML = '<i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload license photo';
            if (backCta) backCta.innerHTML = '<i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload extra page (optional)';
            if (backInput) backInput.removeAttribute('required');
            if (backStatus) {
                backStatus.setAttribute('data-default', 'Optional');
                if (!backInput || !backInput.files || !backInput.files.length) {
                    backStatus.textContent = 'Optional';
                    backStatus.classList.remove('is-done');
                    backStatus.classList.add('is-optional');
                }
            }
            if (frontTips) {
                frontTips.innerHTML =
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Lay the document flat — avoid curled edges</li>' +
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Include the full page with license number readable</li>' +
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Good lighting; no heavy shadows on text</li>';
            }
        } else {
            label.textContent = 'National ID number';
            input.placeholder = 'Enter the number exactly as on the ID';
            if (numberHint) numberHint.textContent = 'Enter the number exactly as it appears on your ID card.';
            if (reqTitle) reqTitle.textContent = 'National ID — what we need';
            if (reqList) {
                reqList.innerHTML =
                    '<li><strong>ID number</strong> — as printed on your card</li>' +
                    '<li><strong>Front photo</strong> — face and number visible</li>' +
                    '<li><strong>Back photo</strong> — barcode / security side</li>';
            }
            if (frontStep) frontStep.textContent = 'Step 1 · Front';
            if (backStep) backStep.textContent = 'Step 2 · Back';
            if (frontLbl) frontLbl.textContent = 'Front of national ID';
            if (backLbl) backLbl.textContent = 'Back of national ID';
            if (frontDesc) frontDesc.textContent = 'Upload a clear photo of the front — your face and ID number must be readable.';
            if (backDesc) backDesc.textContent = 'Upload a clear photo of the back — barcode and security details visible.';
            if (frontCta) frontCta.innerHTML = '<i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload front photo';
            if (backCta) backCta.innerHTML = '<i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Tap to upload back photo';
            if (backInput) backInput.setAttribute('required', 'required');
            if (backStatus) {
                backStatus.setAttribute('data-default', 'Required');
                if (!backInput || !backInput.files || !backInput.files.length) {
                    backStatus.textContent = 'Required';
                    backStatus.classList.remove('is-done', 'is-optional');
                }
            }
            if (frontTips) {
                frontTips.innerHTML =
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Use good lighting — avoid shadows on the card</li>' +
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Keep the full card inside the frame, no cut-off edges</li>' +
                    '<li><i class="fas fa-check-circle" aria-hidden="true"></i> Hold the phone steady so text stays sharp</li>';
            }
        }
    }
    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
})();
</script>
@endpush

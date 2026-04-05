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
        margin-top: 0.25rem;
    }
    @media (min-width: 640px) {
        .doc-uploads-grid { grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    }

    .doc-upload-block {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .doc-upload-block > label:first-of-type {
        font-weight: 600;
        color: #2d3436;
    }
    .doc-file-wrap {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
    }
    .doc-file-wrap input[type="file"] {
        font-size: 0.8125rem;
        max-width: 100%;
        padding: 0.4rem 0;
    }
    .doc-preview {
        position: relative;
        margin-top: 0.35rem;
        border-radius: 12px;
        overflow: hidden;
        background: #eef1f6;
        border: 1px solid #e2e6ed;
        min-height: 160px;
        display: none;
        align-items: center;
        justify-content: center;
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

    .form-hint { font-size: 0.8125rem; color: #64748b; margin: 0; }
    .profile-form .doc-upload-block .form-hint { margin-top: -0.2rem; }

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
                    <p class="verify-doc__hint">Choose <strong>one</strong> type. Enter the number as printed on the document, then add two photos — <strong>front</strong> and <strong>back</strong>. Use a well-lit, in-focus picture; the preview shows your file at full quality (no compression). For a trade license, the second photo can be the reverse or another page.</p>
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

                <div class="form-group">
                    <label for="document_number" id="document_number_label">National ID number</label>
                    <input type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" placeholder="Enter the number exactly as on the document" required autocomplete="off">
                    @error('document_number')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="doc-uploads-grid">
                    <div class="form-group doc-upload-block">
                        <label for="document_front" id="document_front_label">Front of national ID (photo)</label>
                        <div class="doc-file-wrap">
                            <input type="file" id="document_front" name="document_front" accept="image/jpeg,image/png,image/jpg,image/webp" required data-doc-preview="preview_front">
                        </div>
                        <p class="form-hint">JPEG, PNG, or WebP, max 5&nbsp;MB. Preview uses your original pixels.</p>
                        @error('document_front')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="doc-preview" id="preview_front" hidden>
                            <button type="button" class="doc-preview__clear" data-clear-input="document_front" aria-label="Remove front image">&times;</button>
                            <img class="doc-preview__img" alt="Front document preview" decoding="async">
                            <span class="doc-preview__meta" aria-hidden="true"></span>
                        </div>
                    </div>

                    <div class="form-group doc-upload-block">
                        <label for="document_back" id="document_back_label">Back of national ID (photo)</label>
                        <div class="doc-file-wrap">
                            <input type="file" id="document_back" name="document_back" accept="image/jpeg,image/png,image/jpg,image/webp" required data-doc-preview="preview_back">
                        </div>
                        <p class="form-hint">JPEG, PNG, or WebP, max 5&nbsp;MB. Preview uses your original pixels.</p>
                        @error('document_back')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="doc-preview" id="preview_back" hidden>
                            <button type="button" class="doc-preview__clear" data-clear-input="document_back" aria-label="Remove back image">&times;</button>
                            <img class="doc-preview__img" alt="Back document preview" decoding="async">
                            <span class="doc-preview__meta" aria-hidden="true"></span>
                        </div>
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

    function bindImagePreview(fileInput) {
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
        });
    }

    document.querySelectorAll('input[data-doc-preview]').forEach(bindImagePreview);

    document.querySelectorAll('.doc-preview__clear').forEach(function (btn) {
        btn.addEventListener('click', function () {
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
    function sync() {
        var v = document.querySelector('input[name="document_type"]:checked');
        if (!v) return;
        if (v.value === 'trade_license') {
            label.textContent = 'Trade license number';
            input.placeholder = 'Enter your trade license number';
            if (frontLbl) frontLbl.textContent = 'Front of trade license (photo)';
            if (backLbl) backLbl.textContent = 'Back or second page of trade license (photo)';
        } else {
            label.textContent = 'National ID number';
            input.placeholder = 'Enter the number exactly as on the ID';
            if (frontLbl) frontLbl.textContent = 'Front of national ID (photo)';
            if (backLbl) backLbl.textContent = 'Back of national ID (photo)';
        }
    }
    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
})();
</script>
@endpush

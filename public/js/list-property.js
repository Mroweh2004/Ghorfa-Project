  /* =================== Image previews with remove (X) =================== */
  
function setupImagePreview(inputId = 'images', containerId = 'image-previews') {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);
    
    if (!input) {
        console.error('Image input not found:', inputId);
        return;
    }
    
    if (!container) {
        console.error('Image preview container not found:', containerId);
        return;
    }
    
    console.log('Setting up image preview:', { inputId, containerId });

    const form = input.form || container.closest('form');

    const existingRaw = container.dataset.existingImages;
    let existingImages = [];
    if (existingRaw) {
      try {
        const parsed = JSON.parse(existingRaw);
        if (Array.isArray(parsed)) {
          existingImages = parsed;
        }
      } catch (err) {
        console.error('Failed to parse existing images payload:', err);
      }
    }

    const removeInputName = container.dataset.removeInputName || 'remove_images[]';
    const removeContainerId = container.dataset.removeContainerId;
    let removeContainer = null;
    if (removeContainerId) {
      removeContainer = document.getElementById(removeContainerId);
    }
    if (!removeContainer && form) {
      removeContainer = form.querySelector('[data-role="removed-images-container"]');
    }

    const removedExisting = new Set();
    const hiddenInputs = new Map();
    const compressStatus = document.getElementById('image-compress-status');

    if (removeContainer) {
      const presetInputs = Array.from(
        removeContainer.querySelectorAll(`input[type="hidden"][name="${removeInputName}"]`)
      );
      presetInputs.forEach((inputEl) => {
        const id = parseInt(inputEl.value, 10);
        if (!Number.isNaN(id)) {
          hiddenInputs.set(id, inputEl);
          removedExisting.add(id);
        }
      });
    }

    let currentFiles = [];
    const objectUrlMap = new Map();

    input.style.position = 'absolute';
    input.style.width = '1px';
    input.style.height = '1px';
    input.style.overflow = 'hidden';
    input.style.clip = 'rect(0 0 0 0)';
    input.style.whiteSpace = 'nowrap';
    input.style.clipPath = 'inset(50%)';
    input.style.border = '0';
    input.style.padding = '0';
    input.style.margin = '-1px';

    input.addEventListener('change', async () => {
      const picked = Array.from(input.files || []);
      if (!picked.length) {
        return;
      }

      container.classList.add('is-compressing');
      if (compressStatus) {
        compressStatus.hidden = false;
      }

      try {
        const processed = typeof window.compressImageFiles === 'function'
          ? await window.compressImageFiles(picked)
          : picked;

        const all = [...currentFiles];
        processed.forEach((file) => {
          const duplicate = all.some((existing) =>
            existing.name === file.name &&
            existing.size === file.size &&
            existing.lastModified === file.lastModified
          );
          if (!duplicate) {
            all.push(file);
          }
        });

      currentFiles = all;
      renderPreviews();
      syncInputFiles();
      container.dispatchEvent(new CustomEvent('wizard-field-changed', { bubbles: true }));
      } catch (error) {
        console.error('Failed to process images:', error);
        alert('Could not process one or more images. Please try different files.');
      } finally {
        container.classList.remove('is-compressing');
        if (compressStatus) {
          compressStatus.hidden = true;
        }
        syncInputFiles();
      }
    });

    function ensureRemovalInput(id) {
      if (!removeContainer || hiddenInputs.has(id)) return;
      const inputHidden = document.createElement('input');
      inputHidden.type = 'hidden';
      inputHidden.name = removeInputName;
      inputHidden.value = id;
      removeContainer.appendChild(inputHidden);
      hiddenInputs.set(id, inputHidden);
    }

    function removeRemovalInput(id) {
      const node = hiddenInputs.get(id);
      if (node && node.parentNode) {
        node.parentNode.removeChild(node);
      }
      hiddenInputs.delete(id);
    }

    function getObjectUrl(file) {
      if (!objectUrlMap.has(file)) {
        objectUrlMap.set(file, URL.createObjectURL(file));
      }
      return objectUrlMap.get(file);
    }

    function revokeObjectUrl(file) {
      const url = objectUrlMap.get(file);
      if (url) {
        URL.revokeObjectURL(url);
        objectUrlMap.delete(file);
      }
    }

    function renderPreviews() {
      container.innerHTML = '';

      const activeExisting = existingImages.filter((img) => !removedExisting.has(img.id));

      if (!activeExisting.length && !currentFiles.length) {
        return;
      }

      activeExisting.forEach((img) => {
        const wrap = document.createElement('div');
        wrap.className = 'thumb existing-image';
        wrap.style.position = 'relative';

        const imageEl = document.createElement('img');
        imageEl.src = img.url;
        imageEl.alt = img.name || 'Property image';
        imageEl.style.width = '100%';
        imageEl.style.height = '100%';
        imageEl.style.objectFit = 'cover';
        imageEl.decoding = 'async';

        if (img.is_primary) {
          const badge = document.createElement('span');
          badge.className = 'thumb-badge';
          badge.textContent = 'Primary';
          wrap.appendChild(badge);
        }

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-btn';
        btn.setAttribute('aria-label', `Remove existing image ${img.name || ''}`.trim());
        btn.innerHTML = '&times;';

        btn.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();

          removedExisting.add(img.id);
          ensureRemovalInput(img.id);
          renderPreviews();
          container.dispatchEvent(new CustomEvent('wizard-field-changed', { bubbles: true }));
        });

        wrap.appendChild(imageEl);
        wrap.appendChild(btn);
        container.appendChild(wrap);
      });

      currentFiles.forEach((file, idx) => {
        if (!file.type || !file.type.startsWith('image/')) return;

        const url = getObjectUrl(file);

        const wrap = document.createElement('div');
        wrap.className = 'thumb';
        wrap.style.position = 'relative';

        const img = document.createElement('img');
        img.src = url;
        img.alt = file.name;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.decoding = 'async';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-btn';
        btn.setAttribute('aria-label', `Remove ${file.name}`);
        btn.innerHTML = '&times;';

        const sizeHint = document.createElement('span');
        sizeHint.className = 'thumb-size-hint';
        sizeHint.textContent = formatFileSize(file.size);

        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();

          const [removed] = currentFiles.splice(idx, 1);
          if (removed) {
            revokeObjectUrl(removed);
          }
          renderPreviews();
          syncInputFiles();
          container.dispatchEvent(new CustomEvent('wizard-field-changed', { bubbles: true }));
        });

        wrap.appendChild(img);
        wrap.appendChild(sizeHint);
        wrap.appendChild(btn);
        container.appendChild(wrap);
      });
    }

    function formatFileSize(bytes) {
      if (!Number.isFinite(bytes) || bytes <= 0) {
        return '';
      }

      if (bytes < 1024 * 1024) {
        return `${Math.max(1, Math.round(bytes / 1024))} KB`;
      }

      return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    }

    function syncInputFiles() {
      if (!currentFiles.length) {
        return false;
      }

      try {
        const dt = new DataTransfer();  
        currentFiles.forEach((file) => {
          if (file instanceof File) {
            dt.items.add(file);
          }
        });
        input.files = dt.files;
        return input.files.length > 0;
      } catch (error) {
        console.warn('Could not sync image files to the file input:', error);
        return false;
      }
    }

    function appendListingFormFields(targetForm, sourceForm, { skipFileInputs = true } = {}) {
      Array.from(sourceForm.elements).forEach((element) => {
        if (!element.name || element.disabled) {
          return;
        }

        if (skipFileInputs && element.type === 'file') {
          return;
        }

        if (element.type === 'checkbox' || element.type === 'radio') {
          if (!element.checked) {
            return;
          }

          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = element.name;
          hidden.value = element.value;
          targetForm.appendChild(hidden);
          return;
        }

        if (element.tagName === 'SELECT' && element.multiple) {
          Array.from(element.selectedOptions).forEach((option) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = element.name;
            hidden.value = option.value;
            targetForm.appendChild(hidden);
          });
          return;
        }

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = element.name;
        hidden.value = element.value;
        targetForm.appendChild(hidden);
      });
    }

    function submitFormWithImageFiles() {
      const fieldName = input.name || 'images[]';
      const submitBtn = form.querySelector('[type="submit"]');
      const statusEl = document.getElementById('listing-submit-status');

      const showStatus = (message) => {
        if (!statusEl) {
          return;
        }

        statusEl.textContent = message;
        statusEl.hidden = !message;
      };

      if (submitBtn) {
        submitBtn.disabled = true;
      }

      showStatus('Submitting your listing…');
      syncInputFiles();

      if (input.files && input.files.length > 0) {
        form.__submittingWithImages = true;
        form.submit();
        return;
      }

      const tempForm = document.createElement('form');
      tempForm.method = 'POST';
      tempForm.action = form.action;
      tempForm.enctype = 'multipart/form-data';
      tempForm.style.display = 'none';

      appendListingFormFields(tempForm, form);

      const fileInput = document.createElement('input');
      fileInput.type = 'file';
      fileInput.name = fieldName;
      fileInput.multiple = true;

      const transfer = new DataTransfer();
      currentFiles.forEach((file) => {
        if (file instanceof File) {
          transfer.items.add(file);
        }
      });
      fileInput.files = transfer.files;
      tempForm.appendChild(fileInput);

      document.body.appendChild(tempForm);
      tempForm.submit();
    }

    if (form) {
      form.__syncPropertyImages = syncInputFiles;
      form.__getPropertyImageFileCount = () => currentFiles.length;
      form.__submitPropertyImagesWithForm = submitFormWithImageFiles;
    }

    // Initial render to show any existing images
    existingImages.forEach((img) => {
      if (img.removed) {
        removedExisting.add(img.id);
        ensureRemovalInput(img.id);
      }
    });
    renderPreviews();
  }
  
  /* =================== Property Location Map =================== */
let propertyLocationMap = null;
let propertyMapClickService = null;
let mapsApiLoaded = false;

function isLocationStepVisible() {
  const step2 = document.querySelector('.wizard-content[data-step="2"]');
  if (!step2) {
    return !!document.getElementById('property-location-map');
  }

  return window.getComputedStyle(step2).display !== 'none';
}

function refreshPropertyLocationMap() {
  if (!propertyLocationMap || typeof google === 'undefined' || !google.maps) {
    return;
  }

  const center = propertyLocationMap.getCenter();
  google.maps.event.trigger(propertyLocationMap, 'resize');
  if (center) {
    propertyLocationMap.setCenter(center);
  }
}

function buildPropertyLocationMap() {
  if (propertyLocationMap) {
    refreshPropertyLocationMap();
    return;
  }

  const mapElement = document.getElementById('property-location-map');
  if (!mapElement) {
    return;
  }

  const mapContainer = mapElement.closest('[data-reverse-geocode-endpoint]');
  const reverseGeocodeEndpoint = mapContainer?.dataset.reverseGeocodeEndpoint || '/map/reverse-geocode';

  const oldLat = parseFloat(document.getElementById('latitude')?.value) || 33.894917;
  const oldLng = parseFloat(document.getElementById('longitude')?.value) || 35.503083;

  propertyLocationMap = new google.maps.Map(mapElement, {
    center: { lat: oldLat, lng: oldLng },
    zoom: 13,
    mapTypeId: 'roadmap',
  });

  if (document.getElementById('latitude')?.value && document.getElementById('longitude')?.value) {
    new google.maps.Marker({
      position: { lat: oldLat, lng: oldLng },
      map: propertyLocationMap,
      title: 'Selected Location',
    });
    updateCoordinatesStatus(oldLat, oldLng, true);
  }

  propertyMapClickService = new MapClickService(propertyLocationMap, {
    showMarker: true,
    showInfoWindow: true,
    enableReverseGeocoding: true,
    reverseGeocodeEndpoint: reverseGeocodeEndpoint,
  });

  propertyMapClickService.onClick((coordinates) => {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (latInput) {
      latInput.value = coordinates.latitude;
      latInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (lngInput) {
      lngInput.value = coordinates.longitude;
      lngInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
    updateCoordinatesStatus(coordinates.latitude, coordinates.longitude, true);
  });

  const enableButton = document.getElementById('enableMapClick');
  const statusSpan = document.getElementById('coordinatesStatus');

  if (enableButton && enableButton.dataset.mapBound !== '1') {
    enableButton.dataset.mapBound = '1';

    enableButton.addEventListener('click', (e) => {
      e.stopPropagation();
      if (propertyMapClickService.isEnabled) {
        propertyMapClickService.disable();
        enableButton.textContent = '📍 Enable Map Click';
        enableButton.style.background = '#3b82f6';
        enableButton.classList.remove('active');
        if (statusSpan) statusSpan.textContent = '';
      } else {
        propertyMapClickService.enable();
        enableButton.textContent = '✓ Click Mode Active';
        enableButton.style.background = '#10b981';
        enableButton.classList.add('active');
        if (statusSpan) statusSpan.textContent = 'Click anywhere on the map to set location';
      }
    });

    if (!document.getElementById('latitude')?.value || !document.getElementById('longitude')?.value) {
      propertyMapClickService.enable();
      enableButton.textContent = '✓ Click Mode Active';
      enableButton.style.background = '#10b981';
      enableButton.classList.add('active');
      if (statusSpan) statusSpan.textContent = 'Click anywhere on the map to set location';
    }
  }

  addAddressSearchBox();
}

function ensurePropertyLocationMap() {
  if (!mapsApiLoaded || typeof google === 'undefined' || !google.maps) {
    return false;
  }

  if (!isLocationStepVisible()) {
    return false;
  }

  buildPropertyLocationMap();
  refreshPropertyLocationMap();
  return !!propertyLocationMap;
}

function schedulePropertyLocationMapRefresh() {
  requestAnimationFrame(() => {
    ensurePropertyLocationMap();
    setTimeout(refreshPropertyLocationMap, 120);
    setTimeout(refreshPropertyLocationMap, 400);
  });
}

window.initPropertyLocationMap = function initPropertyLocationMapCallback() {
  mapsApiLoaded = true;
  ensurePropertyLocationMap();
};

window.initEditPropertyLocationMap = window.initPropertyLocationMap;

function updateCoordinatesStatus(lat, lng, isSet) {
    const statusSpan = document.getElementById('coordinatesStatus');
    if (!statusSpan) return;
    
    if (isSet) {
        statusSpan.textContent = `✓ Location set: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        statusSpan.style.color = '#10b981';
    } else {
        statusSpan.textContent = 'Location not set';
        statusSpan.style.color = '#6b7280';
    }
}

function addAddressSearchBox() {
    if (!propertyLocationMap) return;

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Search for address...';
    input.style.cssText = `
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 15px;
        padding: 10px;
        text-overflow: ellipsis;
        width: 300px;
        margin: 10px;
    `;

    const searchBox = new google.maps.places.SearchBox(input);
    propertyLocationMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', () => {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const place = places[0];
        if (!place.geometry || !place.geometry.location) return;

        propertyLocationMap.setCenter(place.geometry.location);
        propertyLocationMap.setZoom(16);

        if (propertyMapClickService) {
            propertyMapClickService.handleMapClick(place.geometry.location);
        }

        const addressField = document.getElementById('address');
        if (addressField && !addressField.value) {
            addressField.value = place.formatted_address || place.name;
        }
    });
}

function validatePropertyLocation() {
    const form = document.querySelector('.listing-form');
    if (!form) return;

    const requireCoordinates = form.dataset.requireCoordinates === 'true';

    form.addEventListener('submit', function(e) {
        const lat = document.getElementById('latitude')?.value;
        const lng = document.getElementById('longitude')?.value;

        if (lat && lng) {
            return;
        }

        if (!requireCoordinates) {
            const confirmed = confirm(
                'No location coordinates set. The system will try to geocode from address. Continue anyway?'
            );
            if (!confirmed) {
                e.preventDefault();
            }
            return;
        }

        e.preventDefault();
        alert('Please set the property location by clicking on the map.');
        const enableButton = document.getElementById('enableMapClick');
        if (enableButton) {
            enableButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (!propertyMapClickService || !propertyMapClickService.isEnabled) {
                enableButton.click();
            }
        }
    });
}

  /* ============================ Wizard Step Validation ============================ */
  function wizardValidationEnabled() {
    const form = document.querySelector('.listing-form');
    return form?.dataset.wizardValidateSteps === 'true';
  }

  function markWizardFieldInvalid(field, message) {
    if (!field) {
      return;
    }

    field.classList.add('wizard-field-error');
    field.setAttribute('aria-invalid', 'true');

    const wrapper = field.closest('.form-input') || field.parentElement;
    if (!wrapper) {
      return;
    }

    let hint = wrapper.querySelector('.wizard-inline-error');
    if (!hint) {
      hint = document.createElement('small');
      hint.className = 'wizard-inline-error text-danger';
      wrapper.appendChild(hint);
    }

    hint.textContent = message;
  }

  function clearWizardStepErrors(stepEl) {
    if (!stepEl) {
      return;
    }

    stepEl.querySelectorAll('.wizard-field-error').forEach((field) => {
      field.classList.remove('wizard-field-error');
      field.removeAttribute('aria-invalid');
    });

    stepEl.querySelectorAll('.wizard-inline-error').forEach((hint) => hint.remove());

    const summary = stepEl.querySelector('.wizard-step-errors');
    if (summary) {
      summary.hidden = true;
      summary.innerHTML = '';
    }
  }

  function showWizardStepErrors(stepEl, messages) {
    if (!stepEl || !messages.length) {
      return;
    }

    let summary = stepEl.querySelector('.wizard-step-errors');
    if (!summary) {
      summary = document.createElement('div');
      summary.className = 'wizard-step-errors alert alert-danger';
      summary.setAttribute('role', 'alert');
      stepEl.insertBefore(summary, stepEl.firstChild);
    }

    summary.innerHTML = `<ul>${messages.map((message) => `<li>${message}</li>`).join('')}</ul>`;
    summary.hidden = false;
  }

  function isValidNumericField(field, { min = 0 } = {}) {
    if (!field) {
      return false;
    }

    const raw = String(field.value ?? '').trim();
    if (raw === '') {
      return false;
    }

    const value = Number(raw);
    return !Number.isNaN(value) && value >= min;
  }

  function isRentListing() {
    const listingType = document.getElementById('listing_type');
    return listingType?.value === 'rent';
  }

  function hasSelectValue(select) {
    if (!select) {
      return false;
    }

    const value = String(select.value ?? '').trim();
    return value !== '';
  }

  function validateStepNativeFields(stepEl, addError) {
    const fields = stepEl.querySelectorAll('input:not([type="hidden"]):not([type="file"]), select, textarea');

    fields.forEach((field) => {
      if (!field.checkValidity()) {
        const message = field.validationMessage || 'Please complete this field.';
        addError(field, message);
      }
    });
  }

  function getWizardImageCount() {
    return document.querySelectorAll('#image-previews .thumb').length;
  }

  function validateWizardStep(step, { silent = false } = {}) {
    const stepEl = document.querySelector(`.wizard-content[data-step="${step}"]`);
    if (!stepEl) {
      return { valid: true, firstInvalid: null, messages: [] };
    }

    if (!silent) {
      clearWizardStepErrors(stepEl);
    }

    const fieldErrors = [];
    const messages = [];

    const addError = (field, message) => {
      messages.push(message);
      fieldErrors.push({ field, message });
      if (!silent) {
        markWizardFieldInvalid(field, message);
      }
    };

    if (step === 1) {
      validateStepNativeFields(stepEl, addError);
    }

    if (step === 2) {
      const latitude = document.getElementById('latitude');
      const longitude = document.getElementById('longitude');
      const latValue = String(latitude?.value ?? '').trim();
      const lngValue = String(longitude?.value ?? '').trim();

      if (!latValue || !lngValue) {
        addError(latitude, 'Please set the property location on the map.');
      }
    }

    if (step === 3) {
      const price = stepEl.querySelector('#price');
      const unit = stepEl.querySelector('#unit');
      const priceDuration = stepEl.querySelector('#price_duration');
      const area = stepEl.querySelector('#area_m3');
      const rooms = stepEl.querySelector('#room_nb');
      const bathrooms = stepEl.querySelector('#bathroom_nb');
      const bedrooms = stepEl.querySelector('#bedroom_nb');

      if (!isValidNumericField(price)) {
        addError(price, 'Enter a valid price.');
      }

      if (!unit?.value) {
        addError(unit, 'Please choose a currency unit.');
      }

      if (isRentListing()) {
        if (!priceDuration?.value) {
          addError(priceDuration, 'Choose a price duration.');
        }

        const rentUnits = stepEl.querySelectorAll('input[name="rent_duration_units[]"]:checked');
        if (!rentUnits.length) {
          const rentGrid = stepEl.querySelector('#rent_duration_units');
          addError(rentGrid, 'Select at least one accepted rent duration.');
        }
      }

      if (!isValidNumericField(area)) {
        addError(area, 'Enter the property area.');
      }

      if (!isValidNumericField(rooms)) {
        addError(rooms, 'Enter the number of rooms.');
      }

      if (!isValidNumericField(bathrooms)) {
        addError(bathrooms, 'Enter the number of bathrooms.');
      }

      if (!isValidNumericField(bedrooms)) {
        addError(bedrooms, 'Enter the number of bedrooms.');
      }
    }

    if (step === 5) {
      const minImages = Number(document.querySelector('.listing-form')?.dataset.minImages || 1);
      if (getWizardImageCount() < minImages) {
        const imagesInput = document.getElementById('images');
        addError(imagesInput, `Please keep or upload at least ${minImages} image${minImages === 1 ? '' : 's'}.`);
      }
    }

    if (!silent) {
      showWizardStepErrors(stepEl, [...new Set(messages)]);
    }

    return {
      valid: fieldErrors.length === 0,
      firstInvalid: fieldErrors[0]?.field || null,
      messages,
    };
  }

  function ensurePropertyImagesOnSubmit(event) {
    const form = event.currentTarget || event.target;

    if (form.__submittingWithImages) {
      return;
    }

    const fileCount = form.__getPropertyImageFileCount?.() || 0;

    if (!fileCount) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();
    form.__submitPropertyImagesWithForm?.();
  }

  /* ============================ Simple Wizard ============================ */
  function initWizard() {
    let currentStep = 1;
    const totalSteps = 5;
    
    const prevBtn = document.getElementById('wizardPrev');
    const nextBtn = document.getElementById('wizardNext');
    const submitBtn = document.getElementById('wizardSubmit');
    const speechBubble = document.querySelector('.helper-speech-bubble');
    const helperAvatar = document.querySelector('.helper-avatar');
    
    const stepMessages = {
      1: { text: "Let's get started!", image: "/images/character/thinking.png" },
      2: { text: "Pin your location!", image: "/images/character/phone.png" },
      3: { text: "Set your pricing!", image: "/images/character/thinking.png" },
      4: { text: "Add amenities!", image: "/images/character/wave-2.png" },
      5: { text: "Almost done!", image: "/images/character/phone.png" }
    };
    
    if (!prevBtn || !nextBtn || !submitBtn) return;

    const form = document.querySelector('.listing-form');

    function updateWizardNextState(step) {
      if (!wizardValidationEnabled()) {
        nextBtn.disabled = false;
        submitBtn.disabled = false;
        nextBtn.classList.remove('is-disabled');
        submitBtn.classList.remove('is-disabled');
        return;
      }

      // Keep Next clickable — show errors when the user tries to advance.
      nextBtn.disabled = false;
      nextBtn.classList.remove('is-disabled');

      if (step === totalSteps) {
        const result = validateWizardStep(step, { silent: true });
        submitBtn.disabled = !result.valid;
        submitBtn.classList.toggle('is-disabled', !result.valid);
      } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-disabled');
      }
    }

    function tryAdvanceStep() {
      if (!wizardValidationEnabled()) {
        return true;
      }

      const result = validateWizardStep(currentStep);
      if (!result.valid) {
        if (result.firstInvalid) {
          if (typeof result.firstInvalid.focus === 'function') {
            result.firstInvalid.focus({ preventScroll: true });
          }
          result.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        updateWizardNextState(currentStep);
        return false;
      }

      return true;
    }
    
    function showStep(step) {
      // Hide all steps
      document.querySelectorAll('.wizard-content').forEach(el => {
        el.style.display = 'none';
      });
      
      // Show current step
      const currentContent = document.querySelector(`.wizard-content[data-step="${step}"]`);
      if (currentContent) {
        currentContent.style.display = 'block';
      }
      
      // Update progress
      document.querySelectorAll('.wizard-step').forEach((el, index) => {
        el.classList.remove('active', 'completed');
        if (index + 1 < step) {
          el.classList.add('completed');
        } else if (index + 1 === step) {
          el.classList.add('active');
        }
      });
      
      // Update buttons
      prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
      nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
      submitBtn.style.display = step === totalSteps ? 'inline-block' : 'none';
      
      // Update speech bubble and avatar with animation
      if (speechBubble && stepMessages[step]) {
        speechBubble.textContent = stepMessages[step].text;
        speechBubble.style.animation = 'none';
        setTimeout(() => {
          speechBubble.style.animation = 'fadeInUp 0.5s ease';
        }, 10);
      }
      
      if (helperAvatar && stepMessages[step]) {
        helperAvatar.src = stepMessages[step].image;
      }
      
      // Initialize or refresh the map when the location step becomes visible.
      if (step === 2) {
        schedulePropertyLocationMapRefresh();
      }

      updateWizardNextState(step);
      
      // Scroll to top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    nextBtn.addEventListener('click', () => {
      if (currentStep >= totalSteps) {
        return;
      }

      if (!tryAdvanceStep()) {
        return;
      }

      currentStep++;
      showStep(currentStep);
    });
    
    prevBtn.addEventListener('click', () => {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });
    
    // Allow clicking on step indicators to navigate (only to completed or current steps)
    document.querySelectorAll('.wizard-step').forEach((stepEl, index) => {
      stepEl.addEventListener('click', () => {
        const targetStep = index + 1;
        // Allow navigation to any previous step or current step
        if (targetStep <= currentStep) {
          currentStep = targetStep;
          showStep(currentStep);
        }
      });
    });
    
    // Initialize
    if (window.__listingWizardErrorStep) {
      currentStep = Number(window.__listingWizardErrorStep) || 1;
    }
    showStep(currentStep);

    if (form) {
      const handleFieldChange = () => {
        if (!wizardValidationEnabled()) {
          return;
        }

        const stepEl = document.querySelector(`.wizard-content[data-step="${currentStep}"]`);
        clearWizardStepErrors(stepEl);
        updateWizardNextState(currentStep);
      };

      form.addEventListener('input', handleFieldChange);
      form.addEventListener('change', handleFieldChange);
      form.addEventListener('wizard-field-changed', handleFieldChange);

      form.addEventListener('submit', (event) => {
        if (wizardValidationEnabled()) {
          for (let step = 1; step <= totalSteps; step += 1) {
            const result = validateWizardStep(step);
            if (!result.valid) {
              event.preventDefault();
              currentStep = step;
              showStep(step);
              if (result.firstInvalid) {
                result.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }
              return;
            }
          }
        }

        ensurePropertyImagesOnSubmit(event);
      });
    }
  }

  /* ============================ Price Auto-Calculation ============================ */
  function initPriceAutoCalculation() {
    const priceInput = document.getElementById('price');
    const durationSelect = document.getElementById('price_duration');
    const perDay = document.getElementById('price_per_day');
    const perWeek = document.getElementById('price_per_week');
    const perMonth = document.getElementById('price_per_month');
    const perYear = document.getElementById('price_per_year');

    if (!priceInput || !durationSelect || !perDay || !perWeek || !perMonth || !perYear) return;

    const DAYS = { day: 1, week: 7, month: 30, year: 365 };

    const round2 = (n) => Math.round((n + Number.EPSILON) * 100) / 100;

    function recalc() {
      const raw = String(priceInput.value ?? '').trim();
      const base = parseFloat(raw);
      const unit = durationSelect.value;

      if (!raw || Number.isNaN(base) || base < 0 || !DAYS[unit]) {
        // Don't clear if user is manually editing
        return;
      }

      const perDayValue = base / DAYS[unit];
      perDay.value = round2(perDayValue);
      perWeek.value = round2(perDayValue * 7);
      perMonth.value = round2(perDayValue * 30);
      perYear.value = round2(perDayValue * 365);
    }

    // Only recalculate when main price or duration changes
    priceInput.addEventListener('input', recalc);
    durationSelect.addEventListener('change', recalc);
    
    // Initial calculation on page load
    recalc();
    
    // Add visual feedback when user manually edits calculated prices
    [perDay, perWeek, perMonth, perYear].forEach(input => {
      input.addEventListener('focus', function() {
        this.style.background = '#ffffff';
      });
      
      input.addEventListener('blur', function() {
        if (!this.value) {
          this.style.background = '#eff6ff';
        }
      });
    });
  }

  /* ============================ Description counter ============================ */
  function initDescriptionCounter() {
    const description = document.getElementById('description');
    const counter = document.getElementById('description-char-count');
    if (!description || !counter) {
      return;
    }

    const update = () => {
      const length = description.value.trim().length;
      counter.textContent = `${length} / 30 characters minimum`;
      counter.classList.toggle('is-valid', length >= 30);
      counter.classList.toggle('is-invalid', length > 0 && length < 30);
    };

    description.addEventListener('input', update);
    update();
  }

  /* ============================ Init ============================ */
  function initListProperty() {
    console.log('Initializing list property page...');
    setupImagePreview('images', 'image-previews');
    validatePropertyLocation();
    initPriceAutoCalculation();
    initDescriptionCounter();
    initWizard();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initListProperty);
  } else {
    initListProperty();
  }
  

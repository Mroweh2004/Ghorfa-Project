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

    input.addEventListener('change', () => {
      const picked = Array.from(input.files || []);

      const all = [...currentFiles];
      picked.forEach((file) => {
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

        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();

          const [removed] = currentFiles.splice(idx, 1);
          if (removed) {
            revokeObjectUrl(removed);
          }
          renderPreviews();
          syncInputFiles();
        });

        wrap.appendChild(img);
        wrap.appendChild(btn);
        container.appendChild(wrap);
      });
    }

    function syncInputFiles() {
      const dt = new DataTransfer();
      currentFiles.forEach((file) => dt.items.add(file));
      input.files = dt.files;
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
let propertyLocationMap;
let propertyMapClickService;

function initPropertyLocationMap() {
    const mapElement = document.getElementById('property-location-map');
    
    if (!mapElement) {
        console.error('Property location map element not found');
        return;
    }

    const mapContainer = mapElement.closest('[data-reverse-geocode-endpoint]');
    const reverseGeocodeEndpoint = mapContainer?.dataset.reverseGeocodeEndpoint || '/map/reverse-geocode';

    const oldLat = parseFloat(document.getElementById('latitude')?.value) || 33.894917;
    const oldLng = parseFloat(document.getElementById('longitude')?.value) || 35.503083;

    propertyLocationMap = new google.maps.Map(mapElement, {
        center: { lat: oldLat, lng: oldLng },
        zoom: 13,
        mapTypeId: 'roadmap'
    });

    if (document.getElementById('latitude')?.value && document.getElementById('longitude')?.value) {
        new google.maps.Marker({
            position: { lat: oldLat, lng: oldLng },
            map: propertyLocationMap,
            title: 'Selected Location'
        });
        updateCoordinatesStatus(oldLat, oldLng, true);
    }

    propertyMapClickService = new MapClickService(propertyLocationMap, {
        showMarker: true,
        showInfoWindow: true,
        enableReverseGeocoding: true,
        reverseGeocodeEndpoint: reverseGeocodeEndpoint
    });

    propertyMapClickService.onClick((coordinates) => {
        document.getElementById('latitude').value = coordinates.latitude;
        document.getElementById('longitude').value = coordinates.longitude;
        updateCoordinatesStatus(coordinates.latitude, coordinates.longitude, true);
    });

    const enableButton = document.getElementById('enableMapClick');
    const statusSpan = document.getElementById('coordinatesStatus');
    
    if (enableButton) {
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

    form.addEventListener('submit', function(e) {
        const lat = document.getElementById('latitude')?.value;
        const lng = document.getElementById('longitude')?.value;
        
        if (!lat || !lng) {
            e.preventDefault();
            alert('Please set the property location by clicking on the map.');
            const enableButton = document.getElementById('enableMapClick');
            if (enableButton) {
                enableButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (!propertyMapClickService || !propertyMapClickService.isEnabled) {
                    enableButton.click();
                }
            }
            return false;
        }
    });
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
      
      // Trigger map resize on step 2
      if (step === 2 && window.map) {
        setTimeout(() => {
          google.maps.event.trigger(window.map, 'resize');
        }, 100);
      }
      
      // Scroll to top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    nextBtn.addEventListener('click', () => {
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
      }
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
    showStep(1);
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

  /* ============================ Init ============================ */
  function initListProperty() {
    console.log('Initializing list property page...');
    setupImagePreview('images', 'image-previews');
    validatePropertyLocation();
    initPriceAutoCalculation();
    initWizard();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initListProperty);
  } else {
    initListProperty();
  }
  

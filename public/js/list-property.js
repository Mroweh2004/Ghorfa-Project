/* ======================= Countries (Select2) ======================= */
function loadCountries() {
    const selectDrop = document.querySelector('#country');
    if (!selectDrop) return;
  
    fetch('https://countriesnow.space/api/v0.1/countries')
      .then(res => res.json())
      .then(data => {
        let options = '<option value="">Select Country</option>';
        if (data && data.data && Array.isArray(data.data)) {
          data.data.forEach(country => {
            options += `<option value="${country.country}">${country.country}</option>`;
          });
        }
        selectDrop.innerHTML = options;
  
        // Initialize Select2 (requires jQuery + select2 script/css already on page)
        if (window.$ && typeof $('#country').select2 === 'function') {
          $('#country').select2({
            placeholder: "Select Country",
            allowClear: true,
            width: "100%"
          });
          
          // Set old value if it exists
          const oldValue = selectDrop.getAttribute('data-old-value');
          if (oldValue) {
            $('#country').val(oldValue).trigger('change');
          }
        }
      })
      .catch(err => {
        console.error(err);
        selectDrop.innerHTML = '<option value="">Country list unavailable</option>';
      });
  }
  
  /* =================== Image previews with remove (X) =================== */
  
function setupImagePreview(inputId = 'images', containerId = 'image-previews') {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);
    if (!input || !container) return;

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
  
  /* ============================ Init ============================ */
  document.addEventListener('DOMContentLoaded', () => {
    loadCountries();
    setupImagePreview('images', 'image-previews');
  });
  

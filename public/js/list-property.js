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
  
    let currentFiles = [];
  
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
      picked.forEach(f => {
        const dup = all.some(x =>
          x.name === f.name && x.size === f.size && x.lastModified === f.lastModified
        );
        if (!dup) all.push(f);
      });
  
      currentFiles = all;
      renderPreviews();
      syncInputFiles();
    });
  
    function renderPreviews() {
      container.innerHTML = '';
      if (!currentFiles.length) return;
  
      currentFiles.forEach((file, idx) => {
        if (!file.type || !file.type.startsWith('image/')) return;
  
        const url = URL.createObjectURL(file);
  
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
  
          currentFiles.splice(idx, 1);
          URL.revokeObjectURL(url); // free memory
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
      currentFiles.forEach(f => dt.items.add(f));
      input.files = dt.files;
    }
  }
  
  /* ============================ Init ============================ */
  document.addEventListener('DOMContentLoaded', () => {
    loadCountries();
    setupImagePreview('images', 'image-previews');
  });
  
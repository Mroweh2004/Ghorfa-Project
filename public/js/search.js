/* =========================
   Clear filters functionality
========================= */
function clearAllFilters() {
  const form = document.querySelector('.filter-container form');
  if (!form) return;

  const textInputs = form.querySelectorAll('input[type="text"], input[type="number"]');
  textInputs.forEach(input => {
    input.value = '';
  });

  const checkboxes = form.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach(checkbox => {
    checkbox.checked = false;
  });

  const selects = form.querySelectorAll('select');
  selects.forEach(select => {
    select.selectedIndex = 0;
  });

  const inputs = form.querySelectorAll('input');
  inputs.forEach(input => {
    input.setCustomValidity('');
  });
}

/* =========================
   Filters toggle
========================= */

function ShowFilterToggle() {
  const filterToggleBtn = document.querySelector('.filter-toggle-btn');
  const searchShowBtn = document.querySelector('.search-show-btn');
  const searchFilters = document.querySelector('.search-filters');
  const filterOverlay = document.querySelector('.filter-overlay');
  const filterCloseBtn = document.querySelector('.filter-close-btn');
  const isMobileView = () => window.matchMedia('(max-width: 640px)').matches;

  function updateShowButtonVisibility() {
    if (!searchShowBtn) return;
    if (!isMobileView()) {
      searchShowBtn.style.display = '';
      return;
    }

    const isFilterOpen = searchFilters.classList.contains('active');
    searchShowBtn.style.display = isFilterOpen ? 'none' : 'flex';
  }

  function closeFilters() {
    searchFilters.classList.remove('active');
    searchFilters.classList.add('fixed-hide');
    if (filterOverlay) {
      filterOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';
    updateShowButtonVisibility();
  }

  function openFilters() {
    searchFilters.classList.add('active');
    searchFilters.classList.remove('fixed-hide');
    if (filterOverlay) {
      filterOverlay.classList.add('active');
    }
    document.body.style.overflow = 'hidden';
    updateShowButtonVisibility();
  }

  if (filterToggleBtn && searchFilters) {
    filterToggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      
      const form = document.querySelector('.filter-container form');
      if (form) {
        clearAllFilters();
        form.submit();
      }
    });
  }

  if (filterOverlay) {
    filterOverlay.addEventListener('click', () => {
      closeFilters();
    });
  }

  if (filterCloseBtn) {
    filterCloseBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeFilters();
    });
  }

  document.addEventListener('click', (e) => {
    if (!searchFilters.contains(e.target) && 
        !filterToggleBtn?.contains(e.target) && 
        !searchShowBtn?.contains(e.target) &&
        !filterOverlay?.contains(e.target) &&
        !filterCloseBtn?.contains(e.target)) {
      closeFilters();
    }
  });

  if (searchShowBtn && searchFilters) {
    searchShowBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      openFilters();
    });
  }

  window.addEventListener('resize', updateShowButtonVisibility);
  updateShowButtonVisibility();
}


/* =========================
   Settings dropdown
========================= */
function ShowSettingslist() {
  const lists = document.querySelectorAll('.setting-list');

  const setCardMenuState = (card, isOpen) => {
    if (!card) {
      return;
    }

    card.classList.toggle('menu-open', isOpen);
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.setting-btn');
    const anyList = e.target.closest('.setting-list');

    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const card = btn.closest('.listing-card');
      const list = card ? card.querySelector('.setting-list') : null;

      lists.forEach((l) => {
        if (l !== list) {
          l.classList.remove('active');
          setCardMenuState(l.closest('.listing-card'), false);
        }
      });

      if (list) {
        list.classList.toggle('active');
        setCardMenuState(card, list.classList.contains('active'));
      }
      return;
    }

    if (anyList) {
      e.stopPropagation();
      return;
    }

    lists.forEach((l) => {
      l.classList.remove('active');
      setCardMenuState(l.closest('.listing-card'), false);
    });
  });
}

/* =========================
   Price range validation
========================= */
function validatePriceRange() {
  const minInput = document.getElementById('min-price');
  const maxInput = document.getElementById('max-price');
  if (!minInput || !maxInput) return;

  if (minInput.value !== '' && maxInput.value !== '') {
    const min = parseFloat(minInput.value);
    const max = parseFloat(maxInput.value);
    if (min > max) {
      minInput.setCustomValidity('Min price cannot be greater than max price.');
    } else {
      minInput.setCustomValidity('');
    }
  } else {
    minInput.setCustomValidity('');
  }
}

/* =========================
   Server-side sorting
========================= */
function initPropertySort(selectId = 'sort-options') {
  const select = document.getElementById(selectId);
  if (!select) return;

  // Set initial value from URL parameter
  const urlParams = new URLSearchParams(window.location.search);
  const urlSort = urlParams.get('sort');
  if (urlSort) {
    select.value = urlSort;
  }

  // Reload page with sort parameter when changed
  select.addEventListener('change', (e) => {
    const sortValue = e.target.value;
    const currentParams = new URLSearchParams(window.location.search);
    
    // Update or add sort parameter
    if (sortValue && sortValue !== 'recommended') {
      currentParams.set('sort', sortValue);
    } else {
      // Remove sort parameter for 'recommended' (default)
      currentParams.delete('sort');
    }
    
    // Preserve page parameter if exists, otherwise remove it (start from page 1)
    if (currentParams.has('page')) {
      currentParams.set('page', '1');
    }
    
    // Reload page with new parameters
    const newUrl = `${window.location.pathname}?${currentParams.toString()}`;
    window.location.href = newUrl;
  });
}

/* =========================
   Boot
========================= */
function initSearchPage() {
  ShowFilterToggle();
  ShowSettingslist();
  initPropertySort();
  if (typeof window.initLikeButtons === 'function') {
    window.initLikeButtons();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSearchPage);
} else {
  initSearchPage();
}


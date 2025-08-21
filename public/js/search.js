/* =========================
   Filters toggle
========================= */

function ShowFilterToggle() {
  const filterToggleBtn = document.querySelector('.filter-toggle-btn');
  const searchFilters = document.querySelector('.search-filters');

  if (filterToggleBtn && searchFilters) {
    filterToggleBtn.addEventListener('click', () => {
      searchFilters.classList.toggle('active');

      // When closing, clear all filter values
      const inputs = searchFilters.querySelectorAll('input');
      inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
          input.checked = false;
        } else {
          input.value = '';
        }

        // Remove custom validity if any
        if (typeof input.setCustomValidity === 'function') {
          input.setCustomValidity('');
        }
      });
    });

    document.addEventListener('click', (e) => {
      if (!searchFilters.contains(e.target) && !filterToggleBtn.contains(e.target)) {
        searchFilters.classList.remove('active');
      }
    });
  }
}


/* =========================
   Settings dropdown
========================= */
function ShowSettingslist() {
  const lists = document.querySelectorAll('.setting-list');

  // Toggle each menu via its related .setting-btn using event delegation
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.setting-btn');
    const anyList = e.target.closest('.setting-list');

    // Click on a settings button
    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      // Find the list inside the same card
      const card = btn.closest('.listing-card');
      const list = card ? card.querySelector('.setting-list') : null;

      // Close others
      lists.forEach(l => {
        if (l !== list) l.classList.remove('active');
      });

      if (list) list.classList.toggle('active');
      return;
    }

    // Click inside an open list: don't close immediately
    if (anyList) {
      e.stopPropagation();
      return;
    }

    // Click elsewhere: close all
    lists.forEach(l => l.classList.remove('active'));
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
   Front-end sorting
========================= */
function initPropertySort(selectId = 'sort-options', gridSelector = '.listings-grid') {
  const select = document.getElementById(selectId);
  const grid = document.querySelector(gridSelector);
  if (!select || !grid) return;

  // Keep a snapshot for "recommended"
  const original = Array.from(grid.children);

  // Helpers
  const readNum = (val, fallback = 0) => {
    const n = Number(val);
    return Number.isFinite(n) ? n : fallback;
  };

  const byPriceAsc  = (a, b) => readNum(a.dataset.price, Infinity) - readNum(b.dataset.price, Infinity);
  const byPriceDesc = (a, b) => readNum(b.dataset.price, -Infinity) - readNum(a.dataset.price, -Infinity);
  const byDateDesc  = (a, b) => readNum(b.dataset.created) - readNum(a.dataset.created); // Newest first
  const byDateAsc   = (a, b) => readNum(a.dataset.created) - readNum(b.dataset.created); // Oldest first

  function sortCards(mode) {
    let cards = Array.from(grid.children);

    switch (mode) {
      case 'price-low':  cards.sort(byPriceAsc);  break;
      case 'price-high': cards.sort(byPriceDesc); break;
      case 'newest':     cards.sort(byDateDesc);  break;
      case 'latest':     cards.sort(byDateAsc);   break; // per your spec
      case 'recommended':
      default:
        cards = original.slice();
        break;
    }

    const frag = document.createDocumentFragment();
    cards.forEach(card => frag.appendChild(card));
    grid.appendChild(frag);
  }

  // Read sort from URL (?sort=price-low) or localStorage
  const urlParams = new URLSearchParams(window.location.search);
  const urlSort = urlParams.get('sort');
  const saved = localStorage.getItem('search_sort');

  // Default: newest (your requirement)
  const initial = urlSort || saved || 'newest';
  select.value = initial;
  sortCards(initial);

  // Listen to changes
  select.addEventListener('change', (e) => {
    const mode = e.target.value;
    // Persist to URL (without reload)
    const params = new URLSearchParams(window.location.search);
    params.set('sort', mode);
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState(null, '', newUrl);

    // Persist to localStorage too
    localStorage.setItem('search_sort', mode);

    sortCards(mode);
  });
}

/* =========================
   Boot
========================= */
document.addEventListener('DOMContentLoaded', () => {
  ShowFilterToggle();
  ShowSettingslist();
  initPropertySort(); // attaches to #sort-options and .listings-grid
});

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

  function closeFilters() {
    searchFilters.classList.remove('active');
    searchFilters.classList.add('fixed-hide');
    if (filterOverlay) {
      filterOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';
  }

  function openFilters() {
    searchFilters.classList.add('active');
    searchFilters.classList.remove('fixed-hide');
    if (filterOverlay) {
      filterOverlay.classList.add('active');
    }
    document.body.style.overflow = 'hidden';
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
}


/* =========================
   Settings dropdown
========================= */
function ShowSettingslist() {
  const lists = document.querySelectorAll('.setting-list');

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.setting-btn');
    const anyList = e.target.closest('.setting-list');

    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const card = btn.closest('.listing-card');
      const list = card ? card.querySelector('.setting-list') : null;

      lists.forEach(l => {
        if (l !== list) l.classList.remove('active');
      });

      if (list) list.classList.toggle('active');
      return;
    }

    if (anyList) {
      e.stopPropagation();
      return;
    }

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

  const readNum = (val, fallback = 0) => {
    const n = Number(val);
    return Number.isFinite(n) ? n : fallback;
  };

  const byPriceAsc  = (a, b) => readNum(a.dataset.price, Infinity) - readNum(b.dataset.price, Infinity);
  const byPriceDesc = (a, b) => readNum(b.dataset.price, -Infinity) - readNum(a.dataset.price, -Infinity);
  const byDateDesc  = (a, b) => readNum(b.dataset.created) - readNum(a.dataset.created);
  const byDateAsc   = (a, b) => readNum(a.dataset.created) - readNum(b.dataset.created);
  const byLikesDesc = (a, b) => readNum(b.dataset.likes, 0) - readNum(a.dataset.likes, 0);

  function sortCards(mode) {
    let cards = Array.from(grid.children);

    switch (mode) {
      case 'price-low':  cards.sort(byPriceAsc);  break;
      case 'price-high': cards.sort(byPriceDesc); break;
      case 'newest':     cards.sort(byDateDesc);  break;
      case 'latest':     cards.sort(byDateAsc);   break; 
      case 'recommended':
      default:
        cards.sort(byLikesDesc);
        break;
    }

    const frag = document.createDocumentFragment();
    cards.forEach(card => frag.appendChild(card));
    grid.appendChild(frag);
  }

  const urlParams = new URLSearchParams(window.location.search);
  const urlSort = urlParams.get('sort');
  const saved = localStorage.getItem('search_sort');
  const initial = urlSort || saved || 'newest';
  select.value = initial;
  sortCards(initial);

  select.addEventListener('change', (e) => {
    const mode = e.target.value;
    const params = new URLSearchParams(window.location.search);
    params.set('sort', mode);
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState(null, '', newUrl);
    localStorage.setItem('search_sort', mode);
    sortCards(mode);
  });
}

/* =========================
   Login redirect buttons
========================= */
function initLoginButtons() {
  const loginButtons = document.querySelectorAll('.favorite-btn[data-login-url]');
  loginButtons.forEach(button => {
    button.addEventListener('click', () => {
      const loginUrl = button.getAttribute('data-login-url');
      window.location.href = loginUrl;
    });
  });
}

/* =========================
   Boot
========================= */
function initSearchPage() {
  ShowFilterToggle();
  ShowSettingslist();
  initPropertySort();
  initLikeButtons();
  initLoginButtons();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSearchPage);
} else {
  initSearchPage();
}

/* =========================
   Like functionality
========================= */
function initLikeButtons() {
  const likeButtons = document.querySelectorAll(".like-btn");
  
  likeButtons.forEach(button => {
    button.addEventListener("click", async (e) => {
      e.preventDefault();
      
      const propertyId = button.getAttribute("data-property-id");
      const heartIcon = button.querySelector("i");
      
      try {
        let response = await fetch(`/properties/${propertyId}/like`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json",
            "Content-Type": "application/json"
          }
        });

        let data = await response.json();
        
        const likeCountElement = document.getElementById(`like-count-${propertyId}`);
        if (likeCountElement) {
          likeCountElement.textContent = data.count;
        }

        if (data.status === "liked") {
          heartIcon.className = "fa-solid fa-heart";
          button.setAttribute("data-liked", "true");
        } else {
          heartIcon.className = "fa-regular fa-heart";
          button.setAttribute("data-liked", "false");
          
          const card = button.closest('.listing-card');
          if (card && window.location.pathname.includes('/favorites')) {
            card.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
              card.remove();
              const remainingCards = document.querySelectorAll('.listing-card');
              if (remainingCards.length === 0) {
                location.reload();
              }
            }, 300);
          }
        }

        const card = button.closest('.listing-card');
        if (card) {
          card.setAttribute('data-likes', data.count);
        }

        const sortSelect = document.getElementById('sort-options');
        if (sortSelect && (sortSelect.value === 'recommended' || sortSelect.value === '')) {
          initPropertySort();
        }
      } catch (error) {
        console.error("Error toggling like:", error);
      }
    });
  });
}

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

function ShowSettingslist() {
    const settingBtns = document.getElementsByClassName('setting-btn');
    const settingLists = document.getElementsByClassName('setting-list');

    for (let i = 0; i < settingLists.length; i++) {
        const listItems = settingLists[i].getElementsByTagName('a');
        for (let k = 0; k < listItems.length; k++) {
            listItems[k].addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        settingBtns[i].addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            for (let j = 0; j < settingLists.length; j++) {
                if (j !== i) {
                    settingLists[j].classList.remove('active');
                }
            }
            
            settingLists[i].classList.toggle('active');
        });
    }

    document.addEventListener('click', function() {
        for (let i = 0; i < settingLists.length; i++) {
            settingLists[i].classList.remove('active');
        }
    });
}

function validatePriceRange() {
    const minInput = document.getElementById('min-price');
    const maxInput = document.getElementById('max-price');
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

document.addEventListener('DOMContentLoaded', function() {
    ShowFilterToggle();
    ShowSettingslist();
});
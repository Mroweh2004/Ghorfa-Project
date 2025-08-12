function ShowFilterToggle() {
    const filterToggleBtn = document.querySelector('.filter-toggle-btn');
    const searchFilters = document.querySelector('.search-filters');

    if (filterToggleBtn && searchFilters) {
        filterToggleBtn.addEventListener('click', () => {
            searchFilters.classList.toggle('active');
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

document.addEventListener('DOMContentLoaded', function() {
    ShowFilterToggle();
    ShowSettingslist();
});
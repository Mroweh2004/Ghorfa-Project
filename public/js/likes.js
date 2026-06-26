/**
 * Shared property like / favorite button handling.
 */
(function () {
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function updateLikeButton(button, status, count) {
        const isLiked = status === 'liked';
        const heartIcon = button.querySelector('i');
        const labelSpan = button.querySelector('span:not(.like-count)');

        button.setAttribute('data-liked', isLiked ? 'true' : 'false');
        button.setAttribute('aria-pressed', isLiked ? 'true' : 'false');
        button.classList.toggle('is-liked', isLiked);

        if (heartIcon) {
            heartIcon.className = isLiked ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
        }

        if (labelSpan) {
            labelSpan.textContent = isLiked ? 'Liked' : 'Like';
        } else if (button.classList.contains('btn-like')) {
            const label = isLiked ? 'Liked' : 'Like';
            button.innerHTML = `<i class="fa-${isLiked ? 'solid' : 'regular'} fa-heart"></i> ${label}`;
        }

        const propertyId = button.getAttribute('data-property-id');
        if (propertyId) {
            const countEl = document.getElementById(`like-count-${propertyId}`);
            if (countEl) {
                countEl.textContent = count;
            }
        }

        const sectionCount = button.closest('.like-section')?.querySelector('.like-count');
        if (sectionCount) {
            sectionCount.textContent = `${count} ${count === 1 ? 'like' : 'likes'}`;
        }

        const card = button.closest('.listing-card');
        if (card) {
            card.setAttribute('data-likes', count);

            if (!isLiked && window.location.pathname.includes('/favorites')) {
                card.classList.add('listing-card--removing');
                setTimeout(() => {
                    card.remove();
                    if (!document.querySelector('.listing-card')) {
                        window.location.reload();
                    }
                }, 300);
            }
        }
    }

    async function toggleLike(button) {
        const propertyId = button.getAttribute('data-property-id');
        if (!propertyId) {
            return;
        }

        if (button.disabled || button.classList.contains('is-loading')) {
            return;
        }

        button.disabled = true;
        button.classList.add('is-loading');

        try {
            const response = await fetch(`/properties/${propertyId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (response.status === 401 || response.status === 419) {
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                throw new Error(`Request failed (${response.status})`);
            }

            const data = await response.json();
            updateLikeButton(button, data.status, data.count);
        } catch (error) {
            console.error('Error toggling like:', error);
            alert('Could not update favorite. Please try again.');
        } finally {
            button.disabled = false;
            button.classList.remove('is-loading');
        }
    }

    function bindLikeButton(button) {
        if (!button || button.dataset.likeBound === '1') {
            return;
        }

        button.dataset.likeBound = '1';

        if (!button.getAttribute('type')) {
            button.setAttribute('type', 'button');
        }

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleLike(button);
        });
    }

    function bindGuestButton(button) {
        if (!button || button.dataset.likeBound === '1') {
            return;
        }

        button.dataset.likeBound = '1';

        if (!button.getAttribute('type')) {
            button.setAttribute('type', 'button');
        }

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const loginUrl = button.getAttribute('data-login-url');
            if (loginUrl) {
                window.location.href = loginUrl;
            }
        });
    }

    function initLikeButtons(root = document) {
        root.querySelectorAll('.like-btn').forEach(bindLikeButton);
        root.querySelectorAll('.favorite-btn[data-login-url]').forEach(bindGuestButton);
    }

    window.initLikeButtons = initLikeButtons;

    window.togglePropertyLike = async function (propertyId, buttonElement) {
        if (!buttonElement) {
            return;
        }

        if (!buttonElement.classList.contains('like-btn')) {
            buttonElement.classList.add('like-btn');
        }

        if (!buttonElement.getAttribute('data-property-id')) {
            buttonElement.setAttribute('data-property-id', propertyId);
        }

        bindLikeButton(buttonElement);
        await toggleLike(buttonElement);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initLikeButtons());
    } else {
        initLikeButtons();
    }
})();

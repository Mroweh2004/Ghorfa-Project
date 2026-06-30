/**
 * Keeps CSRF tokens in sync and refreshes them before stale form submits.
 */
(function () {
    'use strict';

    const REFRESH_INTERVAL_MS = 45 * 60 * 1000;

    function getMetaToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function syncToken(token) {
        if (!token) {
            return;
        }

        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.content = token;
        }

        document.querySelectorAll('input[name="_token"]').forEach(function (input) {
            input.value = token;
        });
    }

    async function refreshCsrfToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return null;
            }

            const data = await response.json();
            if (data.token) {
                syncToken(data.token);
                return data.token;
            }
        } catch (error) {
            console.warn('CSRF refresh failed:', error);
        }

        return null;
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method !== 'POST') {
            return;
        }

        const tokenInput = form.querySelector('input[name="_token"]');
        const metaToken = getMetaToken();

        if (tokenInput && metaToken) {
            tokenInput.value = metaToken;
        }
    }, true);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshCsrfToken();
        }
    });

    window.setInterval(refreshCsrfToken, REFRESH_INTERVAL_MS);
    window.refreshCsrfToken = refreshCsrfToken;
})();

(function () {
    'use strict';

    const markAllBtn = document.getElementById('markAllReadHistoryBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    function markAsRead(notificationId) {
        if (!csrfToken || !notificationId) {
            return Promise.resolve();
        }

        return fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
        }).catch(function () {});
    }

    document.querySelectorAll('.notification-history-item[data-notification-id]').forEach(function (item) {
        item.addEventListener('click', function () {
            const id = item.dataset.notificationId;
            if (!id || item.classList.contains('unread') === false) {
                return;
            }
            markAsRead(id).then(function () {
                item.classList.remove('unread');
                const dot = item.querySelector('.notification-unread-dot');
                if (dot) {
                    dot.remove();
                }
            });
        });
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            if (!csrfToken) {
                return;
            }

            markAllBtn.disabled = true;

            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (!data.success) {
                        return;
                    }

                    document.querySelectorAll('.notification-history-item.unread').forEach(function (item) {
                        item.classList.remove('unread');
                        const dot = item.querySelector('.notification-unread-dot');
                        if (dot) {
                            dot.remove();
                        }
                    });

                    markAllBtn.remove();
                    const subtitle = document.querySelector('.notifications-history__subtitle');
                    if (subtitle) {
                        subtitle.textContent = 'Your full notification history.';
                    }
                })
                .finally(function () {
                    markAllBtn.disabled = false;
                });
        });
    }
})();

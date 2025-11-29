/* =========================
   Notification System
========================= */

(function() {
    'use strict';

    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const notificationContainer = document.getElementById('notificationContainer');

    if (!notificationBell || !notificationDropdown || !notificationList) {
        console.warn('Notification elements not found. Elements:', {
            bell: !!notificationBell,
            dropdown: !!notificationDropdown,
            list: !!notificationList
        });
        return;
    }

    console.log('Notification system initialized');

    let pollInterval = null;
    const POLL_INTERVAL = 30000; // 30 seconds
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.content;
        }
        return null;
    }

    async function fetchNotifications() {
        try {
            const token = getCSRFToken();
            const response = await fetch('/notifications', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to fetch notifications');
            }

            const data = await response.json();
            
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    async function fetchUnreadCount() {
        try {
            const token = getCSRFToken();
            const response = await fetch('/notifications/unread-count', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to fetch unread count');
            }

            const data = await response.json();
            
            if (data.success) {
                updateNotificationBadge(data.count);
            }
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    }

    function updateNotificationBadge(count) {
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
            notificationBell.classList.add('has-notifications');
        } else {
            notificationBadge.style.display = 'none';
            notificationBell.classList.remove('has-notifications');
        }
    }

    function getNotificationIcon(type) {
        const icons = {
            'like': 'fa-heart',
            'approve': 'fa-check-circle',
            'reject': 'fa-times-circle',
            'pending': 'fa-clock',
        };
        return icons[type] || 'fa-bell';
    }

    function getNotificationColor(type) {
        const colors = {
            'like': '#ef4444',
            'approve': '#10b981',
            'reject': '#ef4444',
            'pending': '#f59e0b',
        };
        return colors[type] || '#6b7280';
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (seconds < 60) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        if (days < 7) return `${days}d ago`;
        return date.toLocaleDateString();
    }

    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications</p>
                </div>
            `;
            return;
        }

        notificationList.innerHTML = notifications.map(notif => `
            <div class="notification-item ${notif.read ? '' : 'unread'}" data-notification-id="${notif.id}">
                <div class="notification-icon" style="background: ${getNotificationColor(notif.type)}20; color: ${getNotificationColor(notif.type)};">
                    <i class="fas ${getNotificationIcon(notif.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${escapeHtml(notif.title)}</div>
                    <div class="notification-message">${escapeHtml(notif.message)}</div>
                    <div class="notification-time">${formatTime(notif.created_at)}</div>
                </div>
                ${!notif.read ? '<div class="notification-unread-dot"></div>' : ''}
            </div>
        `).join('');

        notificationList.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                const notificationId = item.dataset.notificationId;
                markAsRead(notificationId);
            });
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function markAsRead(notificationId) {
        try {
            const token = getCSRFToken();
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                    const dot = item.querySelector('.notification-unread-dot');
                    if (dot) dot.remove();
                }
                await fetchUnreadCount();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async function markAllAsRead() {
        try {
            const token = getCSRFToken();
            const response = await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                notificationList.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('unread');
                    const dot = item.querySelector('.notification-unread-dot');
                    if (dot) dot.remove();
                });
                await fetchUnreadCount();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    function toggleDropdown() {
        notificationDropdown.classList.toggle('active');
        if (notificationDropdown.classList.contains('active')) {
            fetchNotifications();
        }
    }

    notificationBell.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown();
    });

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            markAllAsRead();
        });
    }

    document.addEventListener('click', (e) => {
        if (!notificationContainer.contains(e.target)) {
            notificationDropdown.classList.remove('active');
        }
    });

    function startPolling() {
        fetchUnreadCount();
        pollInterval = setInterval(fetchUnreadCount, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    function init() {
        console.log('Initializing notification system...');
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM loaded, starting notification polling');
                startPolling();
            });
        } else {
            console.log('DOM already loaded, starting notification polling');
            startPolling();
        }
    }

    init();

    window.addEventListener('beforeunload', stopPolling);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
        }
    });
})();


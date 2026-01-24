/* =========================
   Admin Dashboard - Dynamic Pending Applications
========================= */

(function() {
    'use strict';

    let pollInterval = null;
    let seenApplicationIds = new Set();
    const POLL_INTERVAL = 5000; // 5 seconds
    
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.content;
        }
        const tokenInput = document.querySelector('input[name="_token"]');
        if (tokenInput) {
            return tokenInput.value;
        }
        console.error('CSRF token not found!');
        return null;
    }
    
    function getElements() {
        return {
            applicationsSection: document.querySelector('.applications-section'),
            applicationsTable: document.querySelector('.applications-table tbody'),
            applicationsHeader: document.querySelector('.applications-section h2')
        };
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'error' ? '#fee2e2' : '#d1fae5'};
            color: ${type === 'error' ? '#991b1b' : '#065f46'};
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
            border: 1px solid ${type === 'error' ? '#fca5a5' : '#a7f3d0'};
        `;
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    function renderApplications(applications) {
        const { applicationsTable, applicationsHeader } = getElements();
        if (!applicationsTable) return;
        
        if (applications.length === 0) {
            applicationsTable.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                        No pending applications
                    </td>
                </tr>
            `;
            if (applicationsHeader) {
                applicationsHeader.textContent = 'Pending Landlord Applications (0)';
            }
            return;
        }

        applicationsTable.innerHTML = applications.map(app => `
            <tr data-application-id="${app.id}">
                <td>${escapeHtml(app.user_name)}</td>
                <td>${escapeHtml(app.user_email)}</td>
                <td>${escapeHtml(app.phone || 'N/A')}</td>
                <td>${escapeHtml(app.applied_at)}</td>
                <td style="display: flex; gap: 0.5rem;">
                    <button 
                        type="button" 
                        class="btn btn-success approve-btn" 
                        data-application-id="${app.id}"
                        onclick="handleApprove(${app.id})"
                    >
                        Approve
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-danger reject-btn" 
                        data-application-id="${app.id}"
                        onclick="handleReject(${app.id})"
                    >
                        Reject
                    </button>
                </td>
            </tr>
        `).join('');

        if (applicationsHeader) {
            applicationsHeader.textContent = `Pending Landlord Applications (${applications.length})`;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function initializeSeenApplications(applications) {
        seenApplicationIds = new Set(applications.map(app => app.id));
    }

    function detectNewApplications(applications) {
        const newApplications = applications.filter(app => !seenApplicationIds.has(app.id));
        return newApplications;
    }

    async function fetchPendingApplications() {
        try {
            const route = document.querySelector('[data-pending-applications-route]')?.dataset.pendingApplicationsRoute || '/admin/pending-applications';
            const response = await fetch(route, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch applications');
            }

            const data = await response.json();
            
            if (data.success) {
                const applications = data.applications || [];
                const currentApplicationIds = new Set(applications.map(app => app.id));
                
                if (seenApplicationIds.size === 0) {
                    initializeSeenApplications(applications);
                } else {
                    const newApplications = detectNewApplications(applications);
                    if (newApplications.length > 0) {
                        const newCount = newApplications.length;
                        const names = newApplications.map(app => app.user_name).join(', ');
                        showNotification(
                            `New landlord application${newCount > 1 ? 's' : ''} from: ${names}`,
                            'success'
                        );
                        newApplications.forEach(app => seenApplicationIds.add(app.id));
                    }
                    
                    seenApplicationIds.forEach(id => {
                        if (!currentApplicationIds.has(id)) {
                            seenApplicationIds.delete(id);
                        }
                    });
                }
                
                renderApplications(applications);
            }
        } catch (error) {
            console.error('Error fetching pending applications:', error);
        }
    }

    async function handleApprove(applicationId) {
        if (!confirm('Approve this landlord application?')) {
            return;
        }

        const row = document.querySelector(`tr[data-application-id="${applicationId}"]`);
        const button = row?.querySelector('.approve-btn');
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
        }

        try {
            const token = getCSRFToken();
            if (!token) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const formData = new FormData();
            formData.append('_token', token);

            const response = await fetch(`/admin/landlord-applications/${applicationId}/approve`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData
            });

            let data;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error(`Server returned non-JSON response. Status: ${response.status}`);
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.success) {
                showNotification(data.message || 'Application approved successfully!', 'success');
                seenApplicationIds.delete(applicationId);
                await fetchPendingApplications();
            } else {
                throw new Error(data.message || 'Failed to approve application');
            }
        } catch (error) {
            console.error('Error approving application:', error);
            console.error('Application ID:', applicationId);
            console.error('CSRF Token:', getCSRFToken() ? 'Present' : 'Missing');
            showNotification(error.message || 'Failed to approve application. Please try again.', 'error');
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Approve';
            }
        }
    }

    async function handleReject(applicationId) {
        if (!confirm('Reject this landlord application?')) {
            return;
        }

        const row = document.querySelector(`tr[data-application-id="${applicationId}"]`);
        const button = row?.querySelector('.reject-btn');
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rejecting...';
        }

        try {
            const token = getCSRFToken();
            if (!token) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const formData = new FormData();
            formData.append('_token', token);
            formData.append('admin_notes', '');

            const response = await fetch(`/admin/landlord-applications/${applicationId}/reject`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData
            });

            let data;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error(`Server returned non-JSON response. Status: ${response.status}`);
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.success) {
                showNotification(data.message || 'Application rejected.', 'success');
                seenApplicationIds.delete(applicationId);
                await fetchPendingApplications();
            } else {
                throw new Error(data.message || 'Failed to reject application');
            }
        } catch (error) {
            console.error('Error rejecting application:', error);
            console.error('Application ID:', applicationId);
            console.error('CSRF Token:', getCSRFToken() ? 'Present' : 'Missing');
            showNotification(error.message || 'Failed to reject application. Please try again.', 'error');
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Reject';
            }
        }
    }

    function startPolling() {
        const { applicationsSection, applicationsTable } = getElements();
        if (!applicationsSection || !applicationsTable) {
            return;
        }
        fetchPendingApplications();
        pollInterval = setInterval(fetchPendingApplications, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    window.handleApprove = handleApprove;
    window.handleReject = handleReject;

    const { applicationsSection, applicationsTable } = getElements();
    if (applicationsSection && applicationsTable) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startPolling);
        } else {
            startPolling();
        }

        window.addEventListener('beforeunload', stopPolling);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
            } else {
                startPolling();
            }
        });
    }
})();

/* =========================
   Property Approval Functions
========================= */

async function handleApproveProperty(propertyId) {
    const button = document.querySelector(`.approve-property-btn[data-property-id="${propertyId}"]`);
    if (button) {
        button.disabled = true;
        button.innerHTML = 'Approving...';
    }

    try {
        const response = await fetch(`/admin/properties/${propertyId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to approve property');
        }

        if (data.success) {
            showNotification(data.message || 'Property approved successfully!', 'success');
            // Remove the row from the table
            const row = document.querySelector(`tr[data-property-id="${propertyId}"]`);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
            // Update pending count badge
            updatePendingCount();
        } else {
            throw new Error(data.message || 'Failed to approve property');
        }
    } catch (error) {
        console.error('Error approving property:', error);
        showNotification(error.message || 'Failed to approve property. Please try again.', 'error');
        if (button) {
            button.disabled = false;
            button.innerHTML = 'Approve';
        }
    }
}

async function handleRejectProperty(propertyId) {
    const adminNotes = prompt('Please provide a reason for rejection (optional):');
    
    const button = document.querySelector(`.reject-property-btn[data-property-id="${propertyId}"]`);
    if (button) {
        button.disabled = true;
        button.innerHTML = 'Rejecting...';
    }

    try {
        const response = await fetch(`/admin/properties/${propertyId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                admin_notes: adminNotes || null
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to reject property');
        }

        if (data.success) {
            showNotification(data.message || 'Property rejected.', 'success');
            // Remove the row from the table
            const row = document.querySelector(`tr[data-property-id="${propertyId}"]`);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
            // Update pending count badge
            updatePendingCount();
        } else {
            throw new Error(data.message || 'Failed to reject property');
        }
    } catch (error) {
        console.error('Error rejecting property:', error);
        showNotification(error.message || 'Failed to reject property. Please try again.', 'error');
        if (button) {
            button.disabled = false;
            button.innerHTML = 'Reject';
        }
    }
}

function updatePendingCount() {
    const rows = document.querySelectorAll('.properties-table tbody tr:not(.empty-state-cell)');
    const count = Array.from(rows).filter(row => !row.querySelector('.empty-state-cell')).length;
    const badge = document.querySelector('#properties-section .badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#fee2e2' : '#d1fae5'};
        color: ${type === 'error' ? '#991b1b' : '#065f46'};
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
        border: 1px solid ${type === 'error' ? '#fca5a5' : '#a7f3d0'};
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
function MoveBetweenSections(){
    // Hide all sections by default
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Get all navigation links that point to sections
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    // Function to show a specific section and hide others
    function showSection(sectionId) {
        // Hide all sections
        sections.forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });
        
        // Show the target section
        const targetSection = document.querySelector(sectionId);
        if (targetSection) {
            targetSection.style.display = 'block';
            targetSection.classList.add('active');
        }
        
        // Update active nav link
        navLinks.forEach(link => {
            link.parentElement.classList.remove('active');
        });
        const activeLink = document.querySelector(`.nav-link[href="${sectionId}"]`);
        if (activeLink) {
            activeLink.parentElement.classList.add('active');
        }
    }
    
    // Add click event listeners to nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                showSection(targetId);
                // Smooth scroll to section
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
    
    // Show the first section or dashboard section by default
    // Check if there's a hash in the URL
    if (window.location.hash) {
        showSection(window.location.hash);
    } else {
        // Show the first section by default, or applications section
        const firstSection = sections[0];
        if (firstSection) {
            showSection('#' + firstSection.id);
        }
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('hashchange', function() {
        if (window.location.hash) {
            showSection(window.location.hash);
        }
    });
}

window.handleApproveProperty = handleApproveProperty;
window.handleRejectProperty = handleRejectProperty;

// Initialize section navigation when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', MoveBetweenSections);
} else {
    MoveBetweenSections();
}


/**
 * CLOTHR Admin Dashboard JavaScript
 */

// CSRF Token Setup
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Initialize AJAX with CSRF token
document.addEventListener('DOMContentLoaded', function() {
    // Set CSRF token for all AJAX requests
    if (getCsrfToken()) {
        fetch.headers = fetch.headers || {};
        fetch.headers['X-CSRF-TOKEN'] = getCsrfToken();
    }
});

/**
 * Update Order Status via AJAX
 */
function updateOrderStatus(orderId, status) {
    if (!confirm('Update order status to ' + status + '?')) {
        return;
    }

    const url = `/admin/orders/${orderId}/status`;
    const data = {
        status: status,
        _token: getCsrfToken()
    };

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order status updated successfully!', 'success');
        } else {
            showNotification('Failed to update order status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating order status', 'error');
    });
}

/**
 * Update User Role via AJAX
 */
function updateUserRole(userId, role) {
    if (!confirm('Change user role to ' + role + '?')) {
        return;
    }

    const url = `/admin/users/${userId}/role`;
    const data = {
        role: role,
        _token: getCsrfToken()
    };

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('User role updated successfully!', 'success');
        } else {
            showNotification('Failed to update user role', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating user role', 'error');
    });
}

/**
 * Show Notification
 */
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '10px';
    notification.style.right = '10px';
    notification.style.zIndex = '10000';
    notification.style.maxWidth = '400px';

    const contentArea = document.querySelector('.content') || document.body;
    contentArea.prepend(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 3000);
}

/**
 * Toggle All Checkboxes (for bulk select)
 */
function toggleAllCheckboxes(checkbox) {
    const checkboxes = document.querySelectorAll('.review-checkbox, .product-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

/**
 * Bulk Approve Reviews
 */
function bulkApprove() {
    const selected = document.querySelectorAll('.review-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one review to approve');
        return;
    }

    if (!confirm(`Approve ${selected.length} reviews?`)) {
        return;
    }

    const reviewIds = Array.from(selected).map(cb => cb.value);
    const url = '/admin/reviews/bulk-approve';
    const data = {
        review_ids: reviewIds,
        _token: getCsrfToken()
    };

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`${selected.length} reviews approved successfully!`, 'success');
            // Reload page after 1 second
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to approve reviews', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while approving reviews', 'error');
    });
}

/**
 * Format Currency
 */
function formatCurrency(value) {
    return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

/**
 * Format Date
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Form Validation Helper
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

/**
 * Delete confirmation helper
 */
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

/**
 * Export to CSV
 */
function exportToCSV(filename) {
    const url = filename || '/admin/reports/export';
    window.location.href = url;
    showNotification('Export started...', 'success');
}

/**
 * Search Products on Input
 */
function searchProducts(query) {
    const cards = document.querySelectorAll('.product-card');
    const lowerQuery = query.toLowerCase();

    cards.forEach(card => {
        const name = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
        const category = card.querySelector('.product-category')?.textContent.toLowerCase() || '';

        if (name.includes(lowerQuery) || category.includes(lowerQuery)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Auto-generate slug from product name
 */
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.value || slugInput.value === '') {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        });
    }
});

/**
 * Image preview before upload
 */
document.addEventListener('DOMContentLoaded', function() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');

    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = Array.from(this.files);
            console.log(`Selected ${files.length} image(s)`);

            // Optional: Show preview
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    console.log('Image loaded:', file.name);
                };
                reader.readAsDataURL(file);
            });
        });
    });
});

/**
 * Real-time search/filter
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('.search-input');

    searchInputs.forEach(input => {
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    });
});

/**
 * Notification Bell - Load notification count from DB
 */
function loadNotifications() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationCount = document.getElementById('notificationCount');

    if (!notificationCount) return;

    // This would fetch from an API endpoint
    // For now, we'll set it to the low stock count from dashboard
    // You can create an API endpoint for this later

    notificationBell?.addEventListener('click', function() {
        // Show notification dropdown (to be implemented)
        console.log('Notifications clicked');
    });
}

// Initialize notifications on page load
document.addEventListener('DOMContentLoaded', loadNotifications);

/**
 * Success/Error message auto-dismiss
 */
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 4000);
        }
    });
});

/**
 * Mobile Menu Toggle (if needed)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

/**
 * Table Row Selection
 */
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleAllCheckboxes(this);
        });
    }
});

/**
 * Export filtered data helper
 */
function exportFilteredData(type) {
    const filters = new FormData(document.querySelector('.search-form') || new FormData());
    const params = new URLSearchParams(filters);
    window.location.href = `/admin/${type}/export?${params.toString()}`;
}

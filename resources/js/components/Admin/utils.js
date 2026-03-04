/**
 * CLOTHR Admin — Shared Utilities
 * Imported by each admin page component.
 */

/** Read CSRF token from the page <meta> tag */
export const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content || '';

/**
 * Fetch wrapper for all admin API calls.
 * - Sends CSRF token automatically
 * - Accepts JSON by default
 * - Throws parsed JSON error on non-2xx responses
 */
export const api = async (url, options = {}) => {
    const isFormData = options.body instanceof FormData;

    const res = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(!isFormData && { 'Content-Type': 'application/json' }),
            ...(options.headers || {}),
        },
    });

    if (!res.ok) {
        const err = await res.json().catch(() => ({ message: 'Request failed.' }));
        throw err;
    }

    if (res.status === 204) return null;
    return res.json();
};

/** Format a value as USD: $1,234.56 */
export const money = (v) =>
    '$' + parseFloat(v || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

/** Format an ISO date string: Jan 1, 2026 */
export const dateStr = (d) =>
    new Date(d).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
    });

/** CSS badge class for each order status */
export const statusClass = (s) => ({
    pending: 'badge-pending',
    processing: 'badge-processing',
    shipped: 'badge-shipped',
    delivered: 'badge-delivered',
    cancelled: 'badge-cancelled',
}[s] || 'badge-info');

import React, { useEffect, useState } from 'react';

const STATUS_COLORS = {
    pending:    { bg: '#fffbeb', color: '#d97706', border: '#fde68a' },
    processing: { bg: '#eff6ff', color: '#2563eb', border: '#bfdbfe' },
    shipped:    { bg: '#f0fdf4', color: '#16a34a', border: '#bbf7d0' },
    delivered:  { bg: '#f0fdf4', color: '#15803d', border: '#86efac' },
    cancelled:  { bg: '#fef2f2', color: '#dc2626', border: '#fecaca' },
};

const STATUS_OPTIONS = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

function AdminOrders() {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState(null);
    const [selectedOrder, setSelectedOrder] = useState(null);
    const [updatingStatus, setUpdatingStatus] = useState(false);

    useEffect(() => {
        fetchOrders();
    }, [page, search, statusFilter]);

    function fetchOrders() {
        setLoading(true);
        let url = `/api/admin/orders?page=${page}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (statusFilter) url += `&status=${statusFilter}`;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setOrders(data.data || []);
                setMeta(data);
                setLoading(false);
            })
            .catch(() => {
                setError('Failed to load orders.');
                setLoading(false);
            });
    }

    function handleSearch(e) {
        setSearch(e.target.value);
        setPage(1);
    }

    function handleStatusFilter(e) {
        setStatusFilter(e.target.value);
        setPage(1);
    }

    function updateStatus(orderId, newStatus) {
        setUpdatingStatus(true);
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ order_status: newStatus }),
        })
            .then(r => r.json())
            .then(() => {
                setUpdatingStatus(false);
                setSelectedOrder(prev => prev ? { ...prev, order_status: newStatus } : prev);
                fetchOrders();
            })
            .catch(() => setUpdatingStatus(false));
    }

    const fmt = v => '$' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });

    const StatusBadge = ({ status }) => {
        const s = STATUS_COLORS[status] || { bg: '#f3f4f6', color: '#374151', border: '#e5e7eb' };
        return (
            <span style={{
                background: s.bg, color: s.color, border: `1px solid ${s.border}`,
                padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                textTransform: 'capitalize',
            }}>{status}</span>
        );
    };

    if (error) return <div style={{ color: '#dc2626', padding: 24 }}>{error}</div>;

    return (
        <div style={{ padding: 24 }}>
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h2 style={{ fontSize: 22, fontWeight: 700, color: '#1e293b' }}>Orders</h2>
                <span style={{ color: '#64748b', fontSize: 14 }}>
                    {meta ? `${meta.total} total orders` : ''}
                </span>
            </div>

            {/* Filters */}
            <div style={{ display: 'flex', gap: 12, marginBottom: 20 }}>
                <input
                    type="text"
                    placeholder="Search by name, email, order ID..."
                    value={search}
                    onChange={handleSearch}
                    style={{
                        padding: '8px 14px', border: '1px solid #e2e8f0', borderRadius: 8,
                        fontSize: 14, width: 280, outline: 'none',
                    }}
                />
                <select
                    value={statusFilter}
                    onChange={handleStatusFilter}
                    style={{
                        padding: '8px 14px', border: '1px solid #e2e8f0', borderRadius: 8,
                        fontSize: 14, background: '#fff', outline: 'none',
                    }}
                >
                    <option value="">All Statuses</option>
                    {STATUS_OPTIONS.map(s => (
                        <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                    ))}
                </select>
            </div>

            {/* Table */}
            <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #e2e8f0', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ background: '#f8fafc', borderBottom: '1px solid #e2e8f0' }}>
                            {['Order ID', 'Customer', 'Date', 'Items', 'Total', 'Status', 'Actions'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>Loading...</td></tr>
                        ) : orders.length === 0 ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No orders found.</td></tr>
                        ) : orders.map(order => (
                            <tr key={order.order_id} style={{ borderBottom: '1px solid #f1f5f9' }}
                                onMouseEnter={e => e.currentTarget.style.background = '#f8fafc'}
                                onMouseLeave={e => e.currentTarget.style.background = '#fff'}>
                                <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#2563eb' }}>
                                    #{order.order_id}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 14 }}>
                                    <div style={{ fontWeight: 500, color: '#1e293b' }}>{order.first_name} {order.last_name}</div>
                                    <div style={{ fontSize: 12, color: '#94a3b8' }}>{order.email}</div>
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#64748b' }}>
                                    {order.order_date ? new Date(order.order_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 14, color: '#374151' }}>
                                    {order.items_count ?? '—'}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#1e293b' }}>
                                    {fmt(order.total_amount)}
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    <StatusBadge status={order.order_status} />
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    <button
                                        onClick={() => setSelectedOrder(order)}
                                        style={{
                                            padding: '6px 14px', background: '#2563eb', color: '#fff',
                                            border: 'none', borderRadius: 6, fontSize: 13, cursor: 'pointer', fontWeight: 500,
                                        }}>
                                        View
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {meta && meta.last_page > 1 && (
                <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                    <button disabled={page === 1} onClick={() => setPage(p => p - 1)}
                        style={{ padding: '6px 14px', border: '1px solid #e2e8f0', borderRadius: 6, cursor: page === 1 ? 'not-allowed' : 'pointer', background: '#fff', color: page === 1 ? '#94a3b8' : '#1e293b' }}>
                        Previous
                    </button>
                    <span style={{ padding: '6px 14px', fontSize: 14, color: '#64748b' }}>
                        Page {meta.current_page} of {meta.last_page}
                    </span>
                    <button disabled={page === meta.last_page} onClick={() => setPage(p => p + 1)}
                        style={{ padding: '6px 14px', border: '1px solid #e2e8f0', borderRadius: 6, cursor: page === meta.last_page ? 'not-allowed' : 'pointer', background: '#fff', color: page === meta.last_page ? '#94a3b8' : '#1e293b' }}>
                        Next
                    </button>
                </div>
            )}

            {/* Order Detail Modal */}
            {selectedOrder && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1000, display: 'flex', alignItems: 'center', justifyContent: 'center' }}
                    onClick={() => setSelectedOrder(null)}>
                    <div style={{ background: '#fff', borderRadius: 16, width: 560, maxHeight: '85vh', overflowY: 'auto', padding: 28, position: 'relative' }}
                        onClick={e => e.stopPropagation()}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <h3 style={{ fontSize: 18, fontWeight: 700, color: '#1e293b' }}>Order #{selectedOrder.order_id}</h3>
                            <button onClick={() => setSelectedOrder(null)}
                                style={{ background: 'none', border: 'none', fontSize: 20, cursor: 'pointer', color: '#94a3b8' }}>✕</button>
                        </div>

                        {/* Customer Info */}
                        <div style={{ background: '#f8fafc', borderRadius: 10, padding: 16, marginBottom: 16 }}>
                            <div style={{ fontWeight: 600, color: '#374151', marginBottom: 8 }}>Customer</div>
                            <div style={{ fontSize: 14, color: '#1e293b' }}>{selectedOrder.first_name} {selectedOrder.last_name}</div>
                            <div style={{ fontSize: 13, color: '#64748b' }}>{selectedOrder.email}</div>
                            <div style={{ fontSize: 13, color: '#64748b' }}>{selectedOrder.phone_number}</div>
                        </div>

                        {/* Shipping */}
                        <div style={{ background: '#f8fafc', borderRadius: 10, padding: 16, marginBottom: 16 }}>
                            <div style={{ fontWeight: 600, color: '#374151', marginBottom: 8 }}>Shipping Address</div>
                            <div style={{ fontSize: 14, color: '#1e293b' }}>{selectedOrder.shipping_address || '—'}</div>
                            {selectedOrder.tracking_num && (
                                <div style={{ fontSize: 13, color: '#64748b', marginTop: 4 }}>Tracking: {selectedOrder.tracking_num}</div>
                            )}
                        </div>

                        {/* Order Info */}
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                            <div style={{ background: '#f8fafc', borderRadius: 10, padding: 14 }}>
                                <div style={{ fontSize: 12, color: '#94a3b8', marginBottom: 4 }}>Order Date</div>
                                <div style={{ fontSize: 14, fontWeight: 500 }}>{selectedOrder.order_date ? new Date(selectedOrder.order_date).toLocaleDateString() : '—'}</div>
                            </div>
                            <div style={{ background: '#f8fafc', borderRadius: 10, padding: 14 }}>
                                <div style={{ fontSize: 12, color: '#94a3b8', marginBottom: 4 }}>Total</div>
                                <div style={{ fontSize: 14, fontWeight: 700, color: '#2563eb' }}>{fmt(selectedOrder.total_amount)}</div>
                            </div>
                        </div>

                        {/* Update Status */}
                        <div style={{ marginBottom: 16 }}>
                            <div style={{ fontWeight: 600, color: '#374151', marginBottom: 8 }}>Update Status</div>
                            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                                {STATUS_OPTIONS.map(s => (
                                    <button key={s} disabled={updatingStatus || selectedOrder.order_status === s}
                                        onClick={() => updateStatus(selectedOrder.order_id, s)}
                                        style={{
                                            padding: '6px 14px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                                            cursor: selectedOrder.order_status === s ? 'default' : 'pointer',
                                            border: `1px solid ${STATUS_COLORS[s]?.border || '#e2e8f0'}`,
                                            background: selectedOrder.order_status === s ? (STATUS_COLORS[s]?.bg || '#f3f4f6') : '#fff',
                                            color: STATUS_COLORS[s]?.color || '#374151',
                                            textTransform: 'capitalize',
                                            opacity: updatingStatus ? 0.6 : 1,
                                        }}>
                                        {selectedOrder.order_status === s ? '✓ ' : ''}{s}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {selectedOrder.order_description && (
                            <div style={{ background: '#fffbeb', borderRadius: 10, padding: 14, fontSize: 13, color: '#92400e' }}>
                                <strong>Note:</strong> {selectedOrder.order_description}
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

export default AdminOrders;
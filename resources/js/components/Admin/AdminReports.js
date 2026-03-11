import React, { useEffect, useState } from 'react';

function StatCard({ label, value, sub, color }) {
    return (
        <div style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: 12, padding: 20 }}>
            <div style={{ fontSize: 13, color: '#94a3b8', marginBottom: 6 }}>{label}</div>
            <div style={{ fontSize: 28, fontWeight: 700, color: color || '#1e293b' }}>{value}</div>
            {sub && <div style={{ fontSize: 12, color: '#94a3b8', marginTop: 4 }}>{sub}</div>}
        </div>
    );
}

function AdminReports() {
    const [stats, setStats] = useState(null);
    const [orders, setOrders] = useState([]);
    const [topProducts, setTopProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [dateRange, setDateRange] = useState('all');

    useEffect(() => { fetchReports(); }, [dateRange]);

    function fetchReports() {
        setLoading(true);
        Promise.all([
            fetch(`/api/admin/reports/summary?range=${dateRange}`, { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(`/api/admin/reports/top-products?range=${dateRange}`, { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(`/api/admin/reports/orders-by-status`, { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
        ])
        .then(([summary, top, orderStats]) => {
            setStats({ ...summary, orderStats });
            setTopProducts(top);
            setLoading(false);
        })
        .catch(() => { setError('Failed to load reports.'); setLoading(false); });
    }

    const STATUS_COLORS = {
        pending:    { bg: '#fef9c3', color: '#854d0e', border: '#fef08a' },
        processing: { bg: '#dbeafe', color: '#1e40af', border: '#bfdbfe' },
        shipped:    { bg: '#ede9fe', color: '#5b21b6', border: '#ddd6fe' },
        delivered:  { bg: '#dcfce7', color: '#166534', border: '#bbf7d0' },
        cancelled:  { bg: '#fee2e2', color: '#991b1b', border: '#fecaca' },
    };

    if (error) return <div style={{ color: '#dc2626', padding: 24 }}>{error}</div>;

    return (
        <div style={{ padding: 24 }}>
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h2 style={{ fontSize: 22, fontWeight: 700, color: '#1e293b' }}>Reports</h2>
                <select value={dateRange} onChange={e => setDateRange(e.target.value)}
                    style={{ padding: '8px 14px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, background: '#fff', cursor: 'pointer' }}>
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>

            {loading ? (
                <div style={{ textAlign: 'center', padding: 60, color: '#94a3b8', fontSize: 16 }}>Loading reports...</div>
            ) : (
                <>
                    {/* Revenue Stats */}
                    <div style={{ marginBottom: 8 }}>
                        <div style={{ fontSize: 13, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 12 }}>Revenue</div>
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16, marginBottom: 24 }}>
                            <StatCard label="Total Revenue" value={`PHP ${Number(stats?.total_revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`} color="#16a34a" />
                            <StatCard label="Total Orders" value={stats?.total_orders || 0} color="#2563eb" />
                            <StatCard label="Avg Order Value" value={`PHP ${Number(stats?.avg_order_value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`} color="#7c3aed" />
                            <StatCard label="Total Discounts" value={`PHP ${Number(stats?.total_discounts || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`} color="#f59e0b" />
                        </div>
                    </div>

                    {/* Orders by Status */}
                    <div style={{ marginBottom: 8 }}>
                        <div style={{ fontSize: 13, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 12 }}>Orders by Status</div>
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: 12, marginBottom: 24 }}>
                            {(stats?.orderStats || []).map(s => {
                                const style = STATUS_COLORS[s.order_status?.toLowerCase()] || { bg: '#f1f5f9', color: '#374151', border: '#e2e8f0' };
                                return (
                                    <div key={s.order_status} style={{ background: style.bg, border: `1px solid ${style.border}`, borderRadius: 12, padding: 16, textAlign: 'center' }}>
                                        <div style={{ fontSize: 11, fontWeight: 700, color: style.color, textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 8 }}>{s.order_status}</div>
                                        <div style={{ fontSize: 26, fontWeight: 700, color: style.color }}>{s.count}</div>
                                    </div>
                                );
                            })}
                            {(!stats?.orderStats || stats.orderStats.length === 0) && (
                                <div style={{ gridColumn: '1/-1', padding: 20, textAlign: 'center', color: '#94a3b8', background: '#f8fafc', borderRadius: 12, border: '1px solid #e2e8f0' }}>
                                    No order data yet.
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Top Products */}
                    <div style={{ marginBottom: 8 }}>
                        <div style={{ fontSize: 13, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 12 }}>Top Selling Products</div>
                        <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #e2e8f0', overflow: 'hidden' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ background: '#f8fafc', borderBottom: '1px solid #e2e8f0' }}>
                                        {['Rank', 'Product', 'Category', 'Units Sold', 'Revenue'].map(h => (
                                            <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {topProducts.length === 0 ? (
                                        <tr><td colSpan={5} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No sales data yet.</td></tr>
                                    ) : topProducts.map((p, i) => (
                                        <tr key={p.product_id} style={{ borderBottom: '1px solid #f1f5f9' }}
                                            onMouseEnter={e => e.currentTarget.style.background = '#f8fafc'}
                                            onMouseLeave={e => e.currentTarget.style.background = '#fff'}>
                                            <td style={{ padding: '12px 16px' }}>
                                                <span style={{
                                                    display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
                                                    width: 28, height: 28, borderRadius: '50%', fontSize: 13, fontWeight: 700,
                                                    background: i === 0 ? '#fef9c3' : i === 1 ? '#f1f5f9' : i === 2 ? '#fef3c7' : '#f8fafc',
                                                    color: i === 0 ? '#854d0e' : i === 1 ? '#475569' : i === 2 ? '#92400e' : '#64748b',
                                                }}>
                                                    {i + 1}
                                                </span>
                                            </td>
                                            <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#1e293b' }}>{p.name}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 13, color: '#64748b' }}>{p.category_name || '-'}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#2563eb' }}>{p.total_sold}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#16a34a' }}>
                                                PHP {Number(p.total_revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Inventory Alerts */}
                    <div style={{ marginTop: 24 }}>
                        <div style={{ fontSize: 13, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 12 }}>Low Stock Alerts</div>
                        <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #e2e8f0', overflow: 'hidden' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ background: '#f8fafc', borderBottom: '1px solid #e2e8f0' }}>
                                        {['Product', 'Category', 'Stock'].map(h => (
                                            <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {(!stats?.low_stock || stats.low_stock.length === 0) ? (
                                        <tr><td colSpan={3} style={{ padding: 40, textAlign: 'center', color: '#16a34a', fontSize: 14 }}>All products are well stocked!</td></tr>
                                    ) : stats.low_stock.map(p => (
                                        <tr key={p.product_id} style={{ borderBottom: '1px solid #f1f5f9' }}>
                                            <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#1e293b' }}>{p.name}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 13, color: '#64748b' }}>{p.category_name || '-'}</td>
                                            <td style={{ padding: '12px 16px' }}>
                                                <span style={{
                                                    background: p.available_qty === 0 ? '#fee2e2' : '#fef9c3',
                                                    color: p.available_qty === 0 ? '#991b1b' : '#854d0e',
                                                    border: `1px solid ${p.available_qty === 0 ? '#fecaca' : '#fef08a'}`,
                                                    padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 700,
                                                }}>
                                                    {p.available_qty === 0 ? 'Out of Stock' : `${p.available_qty} left`}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
}

export default AdminReports;
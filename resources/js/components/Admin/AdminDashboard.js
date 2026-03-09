import React, { useEffect, useState } from "react";

function AdminDashboard() {
    const [stats, setStats] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetch("/api/admin/stats", { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(setStats)
            .catch(() => setError("Failed to load dashboard data."));
    }, []);

    useEffect(() => {
        if (!stats) return;
        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => drawCharts(stats);
            document.head.appendChild(script);
        } else {
            drawCharts(stats);
        }
    }, [stats]);

    function drawCharts(stats) {
        ['salesTrendChart','categoryRevenueChart','topProductsChart'].forEach(id => {
            const el = document.getElementById(id);
            if (el && el._chartInstance) el._chartInstance.destroy();
        });

        const ctx1 = document.getElementById('salesTrendChart');
        if (ctx1) {
            ctx1._chartInstance = new Chart(ctx1.getContext('2d'), {
                type: 'line',
                data: {
                    labels: stats.salesTrend?.labels || [],
                    datasets: [{
                        label: 'Sales Revenue',
                        data: (stats.salesTrend?.data || []).map(Number),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        tension: 0.4, fill: true,
                        pointRadius: 4, pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff', pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } }
                }
            });
        }

        const ctx2 = document.getElementById('categoryRevenueChart');
        if (ctx2) {
            ctx2._chartInstance = new Chart(ctx2.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: stats.categoryRevenue?.labels || [],
                    datasets: [{
                        data: (stats.categoryRevenue?.data || []).map(Number),
                        backgroundColor: ['#2563eb','#1e40af','#1e3a8a','#fbbf24','#f97316','#ef4444'],
                        borderColor: '#fff', borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        const ctx3 = document.getElementById('topProductsChart');
        if (ctx3) {
            ctx3._chartInstance = new Chart(ctx3.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: stats.topProducts?.labels || [],
                    datasets: [{
                        label: 'Items Sold',
                        data: (stats.topProducts?.data || []).map(Number),
                        backgroundColor: '#2563eb', borderRadius: 6, borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    }

    const fmt = v => '$' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const num = v => parseInt(v || 0).toLocaleString();

    if (error) return <div style={{ padding: 24, color: '#ef4444' }}>{error}</div>;
    if (!stats) return <div style={{ padding: 40, textAlign: 'center', color: '#6b7280' }}>Loading dashboard...</div>;

    const os = stats.orderStatus || {};

    return (
        <div style={{ padding: 24 }}>
            <div className="stats-grid">
                <div className="stat-card">
                    <div className="stat-icon stat-revenue"><i className="bi bi-currency-dollar"></i></div>
                    <div className="stat-content">
                        <p className="stat-label">Total Revenue</p>
                        <p className="stat-value">{fmt(stats.totalRevenue)}</p>
                        <span className="stat-change positive">+12% vs last month</span>
                    </div>
                </div>
                <div className="stat-card">
                    <div className="stat-icon stat-orders"><i className="bi bi-bag"></i></div>
                    <div className="stat-content">
                        <p className="stat-label">Today's Orders</p>
                        <p className="stat-value">{num(stats.todaysOrders)}</p>
                        <span className="stat-change positive">+8 vs yesterday</span>
                    </div>
                </div>
                <div className="stat-card">
                    <div className="stat-icon stat-avg"><i className="bi bi-graph-up"></i></div>
                    <div className="stat-content">
                        <p className="stat-label">Average Order Value</p>
                        <p className="stat-value">{fmt(stats.avgOrderValue)}</p>
                        <span className="stat-change positive">+5% vs last month</span>
                    </div>
                </div>
                <div className="stat-card">
                    <div className="stat-icon stat-customers"><i className="bi bi-people"></i></div>
                    <div className="stat-content">
                        <p className="stat-label">Total Customers</p>
                        <p className="stat-value">{num(stats.totalCustomers)}</p>
                        <span className="stat-change positive">+23 new this month</span>
                    </div>
                </div>
            </div>

            <div className="section-title">Order Status</div>
            <div className="status-grid">
                <div className="status-card pending">
                    <div className="status-number">{num(os.pending)}</div>
                    <div className="status-label">Pending</div>
                </div>
                <div className="status-card processing">
                    <div className="status-number">{num(os.processing)}</div>
                    <div className="status-label">Processing</div>
                </div>
                <div className="status-card shipped">
                    <div className="status-number">{num(os.shipped)}</div>
                    <div className="status-label">Shipped</div>
                </div>
                <div className="status-card delivered">
                    <div className="status-number">{num(os.delivered)}</div>
                    <div className="status-label">Delivered</div>
                </div>
                <div className="status-card cancelled">
                    <div className="status-number">{num(os.cancelled)}</div>
                    <div className="status-label">Cancelled</div>
                </div>
            </div>

            <div className="charts-container">
                <div className="chart-card">
                    <h3 className="chart-title">Sales Trend (Last 8 Days)</h3>
                    <canvas id="salesTrendChart" height="300"></canvas>
                </div>
                <div className="chart-card">
                    <h3 className="chart-title">Revenue by Category</h3>
                    <canvas id="categoryRevenueChart" height="300"></canvas>
                </div>
            </div>

            <div className="charts-container">
                <div className="chart-card">
                    <h3 className="chart-title">Top Selling Products</h3>
                    <canvas id="topProductsChart" height="250"></canvas>
                </div>
                <div className="alert-card">
                    <h3 className="alert-title">Quick Stats &amp; Alerts</h3>
                    <div className="alert-item">
                        <span className="alert-label">Low Stock Items</span>
                        <span className="alert-value warning">{num(stats.lowStockCount)}</span>
                    </div>
                    <div className="alert-item">
                        <span className="alert-label">Total Products</span>
                        <span className="alert-value">{num(stats.totalProducts)}</span>
                    </div>
                    <div className="alert-item">
                        <span className="alert-label">Total Orders</span>
                        <span className="alert-value">{num(stats.totalOrdersCount)}</span>
                    </div>
                    <div className="alert-item">
                        <span className="alert-label">Avg Rating</span>
                        <span className="alert-value">⭐ {stats.avgRating}</span>
                    </div>
                    <div className="alert-item">
                        <span className="alert-label">Active Users</span>
                        <span className="alert-value">{num(stats.activeUsers)}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default AdminDashboard;
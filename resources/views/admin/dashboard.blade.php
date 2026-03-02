@extends('admin.layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')
    <!-- Dashboard Stats Row 1 -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-revenue">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Revenue</p>
                <p class="stat-value">${{ number_format($totalRevenue, 2) }}</p>
                <span class="stat-change positive">+12% vs last month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-orders">
                <i class="bi bi-bag"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Today's Orders</p>
                <p class="stat-value">{{ $todaysOrders }}</p>
                <span class="stat-change positive">+8 vs yesterday</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-avg">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Average Order Value</p>
                <p class="stat-value">${{ number_format($avgOrderValue, 2) }}</p>
                <span class="stat-change positive">+5% vs last month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-customers">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Customers</p>
                <p class="stat-value">{{ $totalCustomers }}</p>
                <span class="stat-change positive">+23 new this month</span>
            </div>
        </div>
    </div>

    <!-- Order Status Row -->
    <div class="section-title">Order Status</div>
    <div class="status-grid">
        <div class="status-card pending">
            <div class="status-number">{{ $pending }}</div>
            <div class="status-label">Pending</div>
        </div>
        <div class="status-card processing">
            <div class="status-number">{{ $processing }}</div>
            <div class="status-label">Processing</div>
        </div>
        <div class="status-card shipped">
            <div class="status-number">{{ $shipped }}</div>
            <div class="status-label">Shipped</div>
        </div>
        <div class="status-card delivered">
            <div class="status-number">{{ $delivered }}</div>
            <div class="status-label">Delivered</div>
        </div>
        <div class="status-card cancelled">
            <div class="status-number">{{ $cancelled }}</div>
            <div class="status-label">Cancelled</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-container">
        <div class="chart-card">
            <h3 class="chart-title">Sales Trend (Last 8 Days)</h3>
            <canvas id="salesTrendChart" height="300"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Revenue by Category</h3>
            <canvas id="categoryRevenueChart" height="300"></canvas>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="charts-container">
        <div class="chart-card">
            <h3 class="chart-title">Top Selling Products</h3>
            <canvas id="topProductsChart" height="250"></canvas>
        </div>
        <div class="alert-card">
            <h3 class="alert-title">Quick Stats & Alerts</h3>
            <div class="alert-item">
                <span class="alert-label">Low Stock Items</span>
                <span class="alert-value warning">{{ $lowStockCount }}</span>
            </div>
            <div class="alert-item">
                <span class="alert-label">Total Products</span>
                <span class="alert-value">{{ $totalProducts }}</span>
            </div>
            <div class="alert-item">
                <span class="alert-label">Total Orders</span>
                <span class="alert-value">{{ $totalOrdersCount }}</span>
            </div>
            <div class="alert-item">
                <span class="alert-label">Avg Rating</span>
                <span class="alert-value">⭐ {{ number_format($avgRating, 1) }}</span>
            </div>
            <div class="alert-item">
                <span class="alert-label">Active Users</span>
                <span class="alert-value">{{ $activeUsers }}</span>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Sales Trend Chart
            const ctx1 = document.getElementById('salesTrendChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesTrendLabels) !!},
                    datasets: [{
                        label: 'Sales Revenue',
                        data: {!! json_encode($salesTrendData) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: function(value) { return '$' + value; } }
                        }
                    }
                }
            });

            // Category Revenue Donut Chart
            const ctx2 = document.getElementById('categoryRevenueChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryRevenueData) !!},
                        backgroundColor: [
                            '#2563eb',
                            '#1e40af',
                            '#1e3a8a',
                            '#fbbf24',
                            '#f97316',
                            '#ef4444',
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Top Products Bar Chart
            const ctx3 = document.getElementById('topProductsChart').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topProductLabels) !!},
                    datasets: [{
                        label: 'Items Sold',
                        data: {!! json_encode($topProductData) !!},
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection

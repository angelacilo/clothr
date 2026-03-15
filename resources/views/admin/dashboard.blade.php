@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Sales analytics & insights')

@section('content')
<div class="dashboard-container">
    <!-- Row 1: KPI Cards -->
    <div class="grid kpi-grid">
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background-color: #eff6ff; color: #3b82f6;">
                    <i data-lucide="philippine-peso"></i>
                </div>
                <span style="color: #10b981; font-weight: 600; font-size: 12px;">Live</span>
            </div>
            <span class="kpi-value">₱{{ number_format($stats['total_sales'], 2) }}</span>
            <span class="kpi-label">Total Revenue</span>
            <span class="kpi-subtext" style="color: var(--text-medium);">All time total</span>
        </div>
        
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background-color: #f3e8ff; color: #a855f7;">
                    <i data-lucide="shopping-bag"></i>
                </div>
                <span style="color: #10b981; font-weight: 600; font-size: 12px;">Active</span>
            </div>
            <span class="kpi-value">{{ $stats['orders'] }}</span>
            <span class="kpi-label">Total Orders</span>
            <span class="kpi-subtext" style="color: var(--text-medium);">Count of orders</span>
        </div>
        
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background-color: #ccfbf1; color: #14b8a6;">
                    <i data-lucide="trending-up"></i>
                </div>
            </div>
            <span class="kpi-value">₱{{ $stats['orders'] > 0 ? number_format($stats['total_sales'] / $stats['orders'], 2) : '0.00' }}</span>
            <span class="kpi-label">Average Order Value</span>
            <span class="kpi-subtext" style="color: var(--text-medium);">Global average</span>
        </div>
        
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background-color: #ffedd5; color: #f97316;">
                    <i data-lucide="users"></i>
                </div>
            </div>
            <span class="kpi-value">{{ $stats['customers'] }}</span>
            <span class="kpi-label">Total Customers</span>
            <span class="kpi-subtext" style="color: #10b981;">Registered users</span>
        </div>
    </div>

    <!-- Row 2: Order Status Pills (Real Data) -->
    <div style="display: flex; gap: 16px; margin-bottom: 24px;">
        <div class="card" style="flex: 1; display: flex; flex-direction: column; align-items: center; background-color: #fef9c3; border-color: #fde047;">
            <i data-lucide="clock" style="margin-bottom: 8px; color: #854d0e;"></i>
            <span style="font-size: 20px; font-weight: 700;">{{ $statusCounts['pending'] }}</span>
            <span style="font-size: 12px; color: #854d0e; font-weight: 600;">Pending</span>
        </div>
        <div class="card" style="flex: 1; display: flex; flex-direction: column; align-items: center; background-color: #dbeafe; border-color: #93c5fd;">
            <i data-lucide="settings" style="margin-bottom: 8px; color: #1e40af;"></i>
            <span style="font-size: 20px; font-weight: 700;">{{ $statusCounts['processing'] }}</span>
            <span style="font-size: 12px; color: #1e40af; font-weight: 600;">Processing</span>
        </div>
        <div class="card" style="flex: 1; display: flex; flex-direction: column; align-items: center; background-color: #f3e8ff; border-color: #d8b4fe;">
            <i data-lucide="truck" style="margin-bottom: 8px; color: #6b21a8;"></i>
            <span style="font-size: 20px; font-weight: 700;">{{ $statusCounts['shipped'] }}</span>
            <span style="font-size: 12px; color: #6b21a8; font-weight: 600;">Shipped</span>
        </div>
        <div class="card" style="flex: 1; display: flex; flex-direction: column; align-items: center; background-color: #dcfce7; border-color: #86efac;">
            <i data-lucide="check-circle" style="margin-bottom: 8px; color: #166534;"></i>
            <span style="font-size: 20px; font-weight: 700;">{{ $statusCounts['delivered'] }}</span>
            <span style="font-size: 12px; color: #166534; font-weight: 600;">Delivered</span>
        </div>
        <div class="card" style="flex: 1; display: flex; flex-direction: column; align-items: center; background-color: #fee2e2; border-color: #fca5a5;">
            <i data-lucide="x-circle" style="margin-bottom: 8px; color: #991b1b;"></i>
            <span style="font-size: 20px; font-weight: 700;">{{ $statusCounts['cancelled'] }}</span>
            <span style="font-size: 12px; color: #991b1b; font-weight: 600;">Cancelled</span>
        </div>
    </div>

    <!-- Row 3: Charts (Real Data) -->
    <div class="grid chart-row">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700;">Sales Trend</h3>
                    <p style="color: var(--text-medium); font-size: 14px;">Last 8 days performance</p>
                </div>
            </div>
            <canvas id="salesTrendChart" height="250"></canvas>
        </div>
        <div class="card">
            <div>
                <h3 style="font-size: 18px; font-weight: 700;">Revenue by Category</h3>
                <p style="color: var(--text-medium); font-size: 14px; margin-bottom: 20px;">Distribution breakdown</p>
            </div>
            <canvas id="revenueCategoryChart" height="250"></canvas>
        </div>
    </div>

    <!-- Row 4: Panels -->
    <div class="grid panel-row">
        <div class="card" style="grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700;">Recent Orders</h3>
                <a href="{{ route('admin.orders') }}" style="font-size: 12px; color: var(--primary); font-weight: 600;">View All</a>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--border-color); color: var(--text-medium);">
                        <th style="padding: 12px 0;">ORDER</th>
                        <th style="padding: 12px 0;">CUSTOMER</th>
                        <th style="padding: 12px 0;">DATE</th>
                        <th style="padding: 12px 0;">TOTAL</th>
                        <th style="padding: 12px 0;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_orders as $order)
                        @php
                            $statusColors = [
                                'Pending' => ['bg' => '#fffbeb', 'color' => '#92400e'],
                                'Processing' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                                'Shipped' => ['bg' => '#f3e8ff', 'color' => '#6b21a8'],
                                'Delivered' => ['bg' => '#f0fdf4', 'color' => '#166534'],
                                'Cancelled' => ['bg' => '#fef2f2', 'color' => '#991b1b'],
                            ];
                            $sc = $statusColors[$order->status] ?? ['bg' => '#f3f4f6', 'color' => '#374151'];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 0; font-weight: 600;">#{{ 1000 + $order->id }}</td>
                            <td style="padding: 12px 0;">{{ $order->customer_info['first_name'] ?? ($order->customer_info['firstName'] ?? 'Guest') }}</td>
                            <td style="padding: 12px 0; color: var(--text-medium);">{{ $order->created_at->format('M d') }}</td>
                            <td style="padding: 12px 0; font-weight: 700;">₱{{ number_format($order->total, 2) }}</td>
                            <td style="padding: 12px 0;">
                                <span style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Category Performance</h3>
            <p style="color: var(--text-medium); font-size: 12px; margin-bottom: 20px;">Sales by category</p>
            <canvas id="categoryPerfChart" height="300"></canvas>
        </div>
        <div class="panel-column">
            <div class="card" style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center;">
                    <i data-lucide="alert-triangle" style="color: #f59e0b; margin-right: 8px; width: 18px;"></i>
                    Alerts & Notifications
                </h3>
                
                @if($lowStockCount > 0)
                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                    <p style="font-size: 13px; color: #92400e; margin-bottom: 12px;">{{ $lowStockCount }} items have less than 10 units in stock</p>
                    <a href="{{ route('admin.products') }}" class="btn" style="background-color: #f97316; color: white; padding: 6px 16px; font-size: 12px;">View</a>
                </div>
                @endif
                
                <div style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 8px; padding: 16px;">
                    <p style="font-size: 13px; color: #166534; font-weight: 600;">All systems operational</p>
                    <p style="font-size: 12px; color: #15803d;">No critical issues detected</p>
                </div>
            </div>
            
            <div class="card">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Quick Stats</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-medium);">
                            <i data-lucide="package" style="width: 16px;"></i>
                            <span style="font-size: 14px;">Total Products</span>
                        </div>
                        <span style="font-weight: 700;">{{ $stats['products'] }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-medium);">
                            <i data-lucide="shopping-cart" style="width: 16px;"></i>
                            <span style="font-size: 14px;">Today's Orders</span>
                        </div>
                        <span style="font-weight: 700;">{{ $todayOrders }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-medium);">
                            <i data-lucide="philippine-peso" style="width: 16px;"></i>
                            <span style="font-size: 14px;">Today's Revenue</span>
                        </div>
                        <span style="font-weight: 700;">₱{{ number_format($todayRevenue, 2) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-medium);">
                            <i data-lucide="user-check" style="width: 16px;"></i>
                            <span style="font-size: 14px;">Total Customers</span>
                        </div>
                        <span style="font-weight: 700;">{{ $stats['customers'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sales Trend Chart (Real Data)
    const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyLabels) !!},
            datasets: [{
                label: 'Sales (₱)',
                data: {!! json_encode($dailySales) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value.toLocaleString(); }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Revenue by Category Chart (Real Data)
    const ctxCat = document.getElementById('revenueCategoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                data: {!! json_encode($categoryRevenue) !!},
                backgroundColor: ['#ec4899', '#3b82f6', '#f59e0b', '#a855f7', '#10b981', '#ef4444', '#06b6d4'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ₱' + context.parsed.toLocaleString();
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Category Performance (Real Data)
    const ctxPerf = document.getElementById('categoryPerfChart').getContext('2d');
    new Chart(ctxPerf, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                label: 'Revenue (₱)',
                data: {!! json_encode($categoryRevenue) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            indexAxis: 'y',
            scales: {
                x: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value.toLocaleString(); }
                    }
                },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection

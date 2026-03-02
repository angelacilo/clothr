@extends('admin.layouts.app')

@section('page-title', 'Reports')
@section('page-subtitle', 'Analytics & insights')

@section('content')
    <div class="page-header">
        <div class="page-header-right">
            <a href="{{ route('admin.reports') }}?export=true" class="btn btn-primary">
                <i class="bi bi-download"></i> Export to CSV
            </a>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-container">
        <div class="chart-card">
            <h3 class="chart-title">Monthly Revenue</h3>
            <canvas id="monthlyRevenueChart" height="300"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Orders by Status</h3>
            <canvas id="statusChart" height="300"></canvas>
        </div>
    </div>

    <!-- Top Categories Table -->
    <div class="table-card">
        <h3 class="card-title">Top Categories</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Products Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topCategories as $category)
                    <tr>
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $category->products_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script>
            // Monthly Revenue Chart
            const ctx1 = document.getElementById('monthlyRevenueChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: {!! json_encode($monthlyData) !!},
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
                        legend: { display: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: function(value) { return '$' + value; } }
                        }
                    }
                }
            });

            // Status Pie Chart
            const ctx2 = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($statusLabels) !!},
                    datasets: [{
                        data: {!! json_encode($statusData) !!},
                        backgroundColor: [
                            '#fffbeb',
                            '#eff6ff',
                            '#f0fdf4',
                            '#ecfdf5',
                            '#fef2f2'
                        ],
                        borderColor: [
                            '#fcd34d',
                            '#3b82f6',
                            '#22c55e',
                            '#14b8a6',
                            '#ef4444'
                        ],
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
        </script>
    @endpush
@endsection

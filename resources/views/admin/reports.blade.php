@extends('layouts.admin')

@section('title', 'Reports')
@section('subtitle', 'Generate and download reports for your store')

@section('content')
<div class="reports-container">
    <div class="report-grid">
        @php
            $reports = [
                ['name' => 'Sales Report', 'desc' => 'Detailed sales analysis and revenue breakdown', 'icon' => 'trending-up', 'color' => '#3b82f6', 'format' => 'PDF'],
                ['name' => 'Inventory Report', 'desc' => 'Stock levels and product availability', 'icon' => 'package', 'color' => '#10b981', 'format' => 'Excel'],
                ['name' => 'Customer Report', 'desc' => 'Customer data and purchasing patterns', 'icon' => 'users', 'color' => '#a855f7', 'format' => 'CSV'],
                ['name' => 'Performance Report', 'desc' => 'Key metrics and business insights', 'icon' => 'activity', 'color' => '#f59e0b', 'format' => 'PDF'],
                ['name' => 'Financial Report', 'desc' => 'Payment transactions and revenue', 'icon' => 'dollar-sign', 'color' => '#ec4899', 'format' => 'Excel'],
                ['name' => 'Reviews Report', 'desc' => 'Customer feedback and ratings analysis', 'icon' => 'star', 'color' => '#8b5cf6', 'format' => 'CSV'],
            ];
        @endphp

        @foreach($reports as $report)
        <div class="card" style="padding: 32px; display: flex; flex-direction: column;">
            <div class="report-icon-circle" style="background-color: {{ $report['color'] }}15;">
                <i data-lucide="{{ $report['icon'] }}" style="color: {{ $report['color'] }}; width: 22px;"></i>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--text-dark);">{{ $report['name'] }}</h3>
            <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 24px; line-height: 1.5; flex: 1;">
                {{ $report['desc'] }}
            </p>
            <button class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;" onclick="handleDownload(this, '{{ $report['format'] }}')">
                <i data-lucide="download" style="width: 16px;"></i>
                Export {{ $report['format'] }}
            </button>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleDownload(btn, format) {
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner" style="border: 2px solid rgba(0,0,0,0.1); border-top: 2px solid #3b82f6; border-radius: 50%; width: 14px; height: 14px; display: inline-block; animation: spin 0.8s linear infinite; margin-right: 8px;"></span> Downloading...`;
        
        // Add spinner animation style if not present
        if (!document.getElementById('spinner-style')) {
            const style = document.createElement('style');
            style.id = 'spinner-style';
            style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }

        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            showToast(`Report downloaded successfully in ${format} format`);
        }, 1500);
    }
</script>
@endsection

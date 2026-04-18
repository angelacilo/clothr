@extends('layouts.portal')

@section('title', 'Rider Dashboard')
@section('portal_title', 'Rider Portal')
@section('brand_route', route('rider.dashboard'))
@section('logout_route', route('rider.logout'))
@section('theme_class', 'theme-rider')

@section('nav_extra')
    <a href="{{ route('rider.dashboard') }}" class="tab {{ request()->routeIs('rider.dashboard') ? 'tab-active' : '' }}">My Tasks</a>
    <a href="{{ route('rider.deliveries') }}" class="tab {{ request()->routeIs('rider.deliveries') ? 'tab-active' : '' }}">History</a>
    
    <div class="toggle-container" style="margin-left: 1rem; padding-left: 1rem; border-left: 1px solid var(--card-border);">
        <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-right: 8px;">{{ $rider->is_available ? 'Available' : 'Busy' }}</span>
        <form id="availabilityForm" action="{{ route('rider.toggle-availability') }}" method="POST" style="height: 24px;">
            @csrf
            <label class="switch">
                <input type="checkbox" onchange="document.getElementById('availabilityForm').submit()" {{ $rider->is_available ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
        </form>
    </div>
@endsection

@section('badge')
    <div class="nav-info">
        <span style="color: #fff; font-weight: 600;">{{ auth()->user()->name }}</span>
        <span style="opacity: 0.3; margin: 0 4px;">|</span>
        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $rider->courier->name }}</span>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-3">
    <div class="card stat-card">
        <span class="stat-label">Assigned to me</span>
        <span class="stat-value">{{ $stats['assigned'] }}</span>
        <span class="stat-meta" style="color: var(--accent-orange);">Awaiting pickup</span>
    </div>
    <div class="card stat-card">
        <span class="stat-label">In transit</span>
        <span class="stat-value">{{ $stats['in_transit'] }}</span>
        <span class="stat-meta" style="color: var(--accent-blue);">Out for delivery</span>
    </div>
    <div class="card stat-card">
        <span class="stat-label">Delivered today</span>
        <span class="stat-value">{{ $stats['delivered'] }}</span>
        <span class="stat-meta" style="color: var(--accent-green);">Completed</span>
    </div>
</div>

<div style="margin-top: 2.5rem;">
    <div class="section-header">
        <span class="section-title">My active deliveries</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
        @forelse($activeDeliveries as $delivery)
        <div class="delivery-card" style="border-color: {{ $delivery->status == 'out_for_delivery' ? 'var(--accent-blue)' : '#333' }};">
            <div class="delivery-header">
                <div class="delivery-title">
                    Order #{{ $delivery->order->id }} • {{ $delivery->order->user->name ?? 'Guest' }}
                </div>
                @php
                    $badgeClass = 'badge-orange';
                    switch($delivery->status) {
                        case 'assigned': $badgeClass = 'badge-orange'; break;
                        case 'picked_up': case 'out_for_delivery': $badgeClass = 'badge-blue'; break;
                        case 'delivered': $badgeClass = 'badge-green'; break;
                        case 'failed': $badgeClass = 'badge-red'; break;
                    }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($delivery->status)) }}</span>
            </div>
            
            <div class="delivery-meta">
                Tracking: {{ $delivery->order->tracking_number ?? 'JT' . str_pad($delivery->order->id, 7, '0', STR_PAD_LEFT) }} • ₱{{ number_format($delivery->order->total, 0) }}<br>
                Address: {{ $delivery->order->customer_info['address'] ?? 'No address provided' }}
            </div>

            <div class="delivery-footer">
                @if($delivery->status === 'assigned')
                    <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="picked_up">
                        <button type="submit" class="btn btn-dark">Mark Picked Up ↗</button>
                    </form>
                @elseif($delivery->status === 'picked_up')
                    <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="out_for_delivery">
                        <button type="submit" class="btn btn-dark">Mark Out for Delivery ↗</button>
                    </form>
                @elseif($delivery->status === 'out_for_delivery')
                    <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-status-green">Mark Delivered</button>
                    </form>
                    <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="btn btn-red">Mark Failed</button>
                    </form>
                @endif
                <a href="{{ route('rider.deliveries.show', $delivery->id) }}" class="btn btn-outline">View details</a>
            </div>
        </div>
        @empty
        <div class="card" style="text-align: center; color: var(--text-muted); padding: 3rem;">
            No active deliveries assigned to you.
        </div>
        @endforelse
    </div>
</div>
@endsection

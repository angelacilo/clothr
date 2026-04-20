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
                Address: @php $ci = $delivery->order->customer_info; @endphp
                {{ $ci['address_line_1'] ?? '' }}, {{ $ci['city'] ?? '' }}, {{ $ci['region'] ?? '' }} {{ $ci['zip_code'] ?? '' }}
            </div>

            <div class="delivery-footer">
                @if($delivery->status === 'assigned')
                    @if($delivery->released_at)
                        <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="picked_up">
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--accent-orange); color: #000;">Confirm Pickup</button>
                        </form>
                    @else
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--text-muted); font-size: 0.85rem; height: 40px;">
                            Waiting for Courier to release...
                        </div>
                    @endif
                @elseif($delivery->status === 'picked_up')
                    <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="status" value="out_for_delivery">
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Go Out for Delivery</button>
                    </form>
                @elseif($delivery->status === 'out_for_delivery')
                    <div style="flex: 1;" id="delivery-form-{{ $delivery->id }}">
                        <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px;">
                            @csrf
                            <input type="hidden" name="status" value="delivered">
                            
                            <div class="proof-upload-wrapper" style="position: relative; width: 100%;">
                                <input type="file" name="proof_of_delivery" id="file-{{ $delivery->id }}" accept="image/*" required 
                                    onchange="handleFileSelect(this, {{ $delivery->id }})"
                                    style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; top: 0; left: 0; z-index: 2;">
                                <div id="label-{{ $delivery->id }}" style="background: rgba(255,255,255,0.05); border: 1px dashed var(--card-border); padding: 10px; border-radius: 8px; text-align: center; font-size: 0.8rem; color: var(--text-muted); transition: all 0.2s;">
                                    📸 <span id="text-{{ $delivery->id }}">Click to upload proof of delivery</span>
                                </div>
                            </div>

                            <div id="actions-{{ $delivery->id }}" style="display: none; gap: 8px; width: 100%;">
                                <button type="button" class="btn btn-outline" style="flex: 1; border-color: var(--accent-red); color: var(--accent-red);" onclick="cancelUpload({{ $delivery->id }})">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="flex: 2; background: var(--accent-green); color: white; border: none;">Save & Deliver</button>
                            </div>
                        </form>
                    </div>
                @endif
                <a href="{{ route('rider.deliveries.show', $delivery->id) }}" class="btn btn-outline" style="flex: 0 0 auto; height: {{ $delivery->status == 'out_for_delivery' ? 'auto' : '40px' }}; align-self: flex-end;">View details</a>
            </div>
        </div>
        @empty
        <div class="card" style="text-align: center; color: var(--text-muted); padding: 3rem;">
            No active deliveries assigned to you.
        </div>
        @endforelse
    </div>
</div>

<script>
    function handleFileSelect(input, id) {
        const label = document.getElementById('label-' + id);
        const text = document.getElementById('text-' + id);
        const actions = document.getElementById('actions-' + id);
        
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            text.innerText = "Selected: " + fileName;
            label.style.background = "rgba(34, 197, 94, 0.1)";
            label.style.borderColor = "var(--accent-green)";
            label.style.color = "var(--accent-green)";
            actions.style.display = "flex";
        }
    }

    function cancelUpload(id) {
        const input = document.getElementById('file-' + id);
        const label = document.getElementById('label-' + id);
        const text = document.getElementById('text-' + id);
        const actions = document.getElementById('actions-' + id);
        
        input.value = "";
        text.innerText = "Click to upload proof of delivery";
        label.style.background = "rgba(255,255,255,0.05)";
        label.style.borderColor = "var(--card-border)";
        label.style.color = "var(--text-muted)";
        actions.style.display = "none";
    }
</script>
@endsection

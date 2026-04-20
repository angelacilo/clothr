@extends('layouts.portal')

@section('title', 'Delivery details #' . $delivery->id)
@section('portal_title', 'Rider Portal')
@section('brand_route', route('rider.dashboard'))
@section('logout_route', route('rider.logout'))
@section('theme_class', 'theme-rider')

@section('badge')
    <div class="nav-info">
        <span style="color: #fff; font-weight: 600;">{{ auth()->user()->name }}</span>
        <span style="opacity: 0.3; margin: 0 4px;">|</span>
        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $rider->courier->name }}</span>
    </div>
@endsection

@section('nav_extra')
    <a href="{{ route('rider.dashboard') }}" class="tab {{ request()->routeIs('rider.dashboard') ? 'tab-active' : '' }}">My Tasks</a>
    <a href="{{ route('rider.deliveries') }}" class="tab {{ request()->routeIs('rider.deliveries') ? 'tab-active' : '' }}">History</a>
@endsection

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('rider.dashboard') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">← Back to My Tasks</a>
</div>

<div class="grid grid-main">
    <div class="card">
        <div class="section-header">
            <span class="section-title">Delivery for Order #{{ $delivery->order->id }}</span>
            @php
                switch($delivery->status) {
                    case 'assigned':
                        $badgeClass = 'badge-orange';
                        break;
                    case 'picked_up':
                    case 'out_for_delivery':
                        $badgeClass = 'badge-blue';
                        break;
                    case 'delivered':
                        $badgeClass = 'badge-green';
                        break;
                    case 'failed':
                        $badgeClass = 'badge-red';
                        break;
                    default:
                        $badgeClass = 'badge-orange';
                }
            @endphp
            <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($delivery->status)) }}</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
            <div>
                <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Customer Information</h4>
                <div class="info-card">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $delivery->order->user->name ?? 'Guest' }}</div>
                    <div class="info-label" style="margin-top: 10px;">Phone</div>
                    <div class="info-value">{{ $delivery->order->customer_info['phone'] ?? 'N/A' }}</div>
                    <div class="info-label" style="margin-top: 10px;">Address</div>
                    <div class="info-value" style="color: var(--accent-primary); font-weight: 600;">
                        @php 
                            $ci = $delivery->order->customer_info; 
                            $address = $ci['address'] ?? ($ci['address_line_1'] ?? 'No address provided');
                            if (isset($ci['barangay']) && $ci['barangay']) $address .= ', ' . $ci['barangay'];
                            if ((isset($ci['city_name']) || isset($ci['city'])) && ($ci['city_name'] ?? $ci['city'])) $address .= ', ' . ($ci['city_name'] ?? $ci['city']);
                            if ((isset($ci['region_name']) || isset($ci['region'])) && ($ci['region_name'] ?? $ci['region'])) $address .= ', ' . ($ci['region_name'] ?? $ci['region']);
                            if (isset($ci['zip']) || isset($ci['zip_code'])) $address .= ' ' . ($ci['zip'] ?? $ci['zip_code']);
                        @endphp
                        {{ $address }}
                    </div>
                </div>
            </div>
            <div>
                <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Order Items</h4>
                <div class="info-card">
                    @foreach($delivery->order->items as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                            <span>{{ $item['quantity'] ?? 1 }}x {{ $item['name'] ?? 'Product' }}</span>
                            <span>₱{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                        </div>
                    @endforeach
                    <div style="border-top: 1px solid var(--card-border); margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; font-weight: 700;">
                        <span>Collect Payment</span>
                        <span style="color: var(--accent-green);">₱{{ number_format($delivery->order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--card-border); padding-top: 2rem;">
            <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Delivery Journey</h4>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Order Created</div>
                        <div class="timeline-time">{{ $delivery->order->created_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Assigned to you</div>
                        <div class="timeline-time">{{ $delivery->assigned_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @if($delivery->released_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Released by Courier</div>
                        <div class="timeline-time">{{ $delivery->released_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
                <div class="timeline-item">
                    <div class="timeline-dot {{ $delivery->picked_up_at ? 'active' : '' }}"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Picked Up</div>
                        <div class="timeline-time">{{ $delivery->picked_up_at ? $delivery->picked_up_at->format('M d, Y · h:i A') : 'Pending' }}</div>
                    </div>
                </div>
                @if($delivery->status == 'out_for_delivery' || $delivery->delivered_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Out for Delivery</div>
                        <div class="timeline-time">{{ $delivery->updated_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
                @if($delivery->delivered_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Delivered successfully</div>
                        <div class="timeline-time">{{ $delivery->delivered_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar: Info -->
    <div class="card">
        <h3 class="section-title">Delivery Info</h3>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Detailed information regarding your current assignment.</p>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div class="info-card" style="padding: 1.25rem;">
                <div class="info-label">Tracking Number</div>
                <div class="info-value" style="font-family: monospace; font-size: 1.1rem; color: var(--accent-primary);">{{ $delivery->order->tracking_number ?? 'N/A' }}</div>
                
                <div class="info-label" style="margin-top: 1rem;">Payment to Collect</div>
                <div class="info-value" style="font-size: 1.25rem; color: var(--accent-green); font-weight: 800;">₱{{ number_format($delivery->order->total, 2) }}</div>
            </div>

            @if($delivery->status === 'delivered' && $delivery->proof_of_delivery)
                <div class="info-card" style="text-align: left; border-color: var(--accent-green); background: rgba(34, 197, 94, 0.05); padding: 1rem;">
                    <div style="color: var(--accent-green); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Delivered
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; font-size: 0.7rem; color: var(--text-muted); margin-bottom: 5px;">Proof of Delivery:</label>
                        <img src="{{ asset('storage/' . $delivery->proof_of_delivery) }}" alt="Proof" style="width: 100%; border-radius: 8px; border: 1px solid var(--card-border); cursor: pointer;" onclick="window.open(this.src)">
                    </div>
                </div>
            @endif
        </div>
        
        <div style="margin-top: 2rem; border-top: 1px solid var(--card-border); padding-top: 1.5rem;">
            <div class="info-label">Courier Service</div>
            <div class="info-value" style="font-size: 1.1rem; color: var(--accent-primary);">{{ $rider->courier->name }}</div>
            
            <div class="info-label" style="margin-top: 1rem;">Rider Support</div>
            <div class="info-value">{{ $rider->courier->user->phone ?? 'Contact Office' }}</div>
        </div>
    </div>
</div>
@endsection

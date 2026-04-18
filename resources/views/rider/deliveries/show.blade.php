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
                        {{ $delivery->order->customer_info['address'] ?? 'N/A' }}
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
                        <div class="timeline-title">Assigned to you</div>
                        <div class="timeline-time">{{ $delivery->assigned_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @if($delivery->picked_up_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Picked Up from Warehouse</div>
                        <div class="timeline-time">{{ $delivery->picked_up_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
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

    <!-- Sidebar: Actions -->
    <div class="card">
        <h3 class="section-title">Actions</h3>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Update the status of this delivery as you progress.</p>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @if($delivery->status === 'assigned')
                <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="picked_up">
                    <button type="submit" class="btn btn-dark" style="width: 100%; justify-content: center; padding: 12px;">Mark Picked Up</button>
                </form>
            @elseif($delivery->status === 'picked_up')
                <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="out_for_delivery">
                    <button type="submit" class="btn btn-dark" style="width: 100%; justify-content: center; padding: 12px;">Mark Out for Delivery</button>
                </form>
            @elseif($delivery->status === 'out_for_delivery')
                <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="btn btn-status-green" style="width: 100%; justify-content: center; padding: 12px;">Mark Delivered</button>
                </form>
                <form action="{{ route('rider.update-status', $delivery->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="failed">
                    <button type="submit" class="btn btn-red" style="width: 100%; justify-content: center; padding: 12px;">Mark Failed</button>
                </form>
            @else
                <div class="info-card" style="text-align: center; color: var(--text-muted);">
                    No further actions available for this status.
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

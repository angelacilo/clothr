@extends('layouts.portal')

@section('title', 'Order details #' . $order->id)
@section('portal_title', 'Courier Portal')
@section('brand_route', route('courier.dashboard'))
@section('logout_route', route('courier.logout'))
@section('theme_class', 'theme-courier')

@section('badge')
    <div class="nav-info">
        <span style="color: #fff; font-weight: 600;">{{ $courier->name }}</span>
        <span style="opacity: 0.3; margin: 0 4px;">|</span>
        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ auth()->user()->email }}</span>
    </div>
@endsection

@section('nav_extra')
    <a href="{{ route('courier.dashboard') }}" class="tab {{ request()->routeIs('courier.dashboard') ? 'tab-active' : '' }}">Dashboard</a>
    <a href="{{ route('courier.orders') }}" class="tab {{ request()->routeIs('courier.orders') ? 'tab-active' : '' }}">Orders</a>
    <a href="{{ route('courier.riders') }}" class="tab {{ request()->routeIs('courier.riders') ? 'tab-active' : '' }}">Riders</a>
@endsection

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('courier.orders') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">← Back to Orders</a>
</div>

<div class="grid grid-main">
    <div class="card">
        <div class="section-header">
            <span class="section-title">Order #{{ $order->id }} details</span>
            @php
                switch($order->status) {
                    case 'pending':
                    case 'processing':
                        $badgeClass = 'badge-orange';
                        break;
                    case 'shipped':
                    case 'out_for_delivery':
                        $badgeClass = 'badge-blue';
                        break;
                    case 'delivered':
                        $badgeClass = 'badge-green';
                        break;
                    default:
                        $badgeClass = 'badge-orange';
                }
            @endphp
            <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($order->status)) }}</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
            <div>
                <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Customer Information</h4>
                <div class="info-card">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $order->user->name ?? 'Guest' }}</div>
                    <div class="info-label" style="margin-top: 10px;">Email</div>
                    <div class="info-value">{{ $order->user->email ?? $order->customer_info['email'] ?? 'N/A' }}</div>
                    <div class="info-label" style="margin-top: 10px;">Address</div>
                    <div class="info-value">{{ $order->customer_info['address'] ?? 'N/A' }}</div>
                </div>
            </div>
            <div>
                <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Order Items</h4>
                <div class="info-card">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                            <span>{{ $item['quantity'] ?? 1 }}x {{ $item['name'] ?? 'Product' }}</span>
                            <span>₱{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                        </div>
                    @endforeach
                    <div style="border-top: 1px solid var(--card-border); margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; font-weight: 700;">
                        <span>Total Amount</span>
                        <span style="color: var(--accent-green);">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Timeline --}}
        @if($order->delivery)
        <div style="margin-top: 2rem; border-top: 1px solid var(--card-border); padding-top: 2rem;">
            <h4 style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Delivery Timeline</h4>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Rider Assigned</div>
                        <div class="timeline-time">{{ $order->delivery->assigned_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @if($order->delivery->picked_up_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Picked Up</div>
                        <div class="timeline-time">{{ $order->delivery->picked_up_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
                @if($order->status == 'out_for_delivery' || $order->delivery->delivered_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Out for Delivery</div>
                        <div class="timeline-time">{{ $order->delivery->updated_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
                @if($order->delivery->delivered_at)
                <div class="timeline-item">
                    <div class="timeline-dot active"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Delivered</div>
                        <div class="timeline-time">{{ $order->delivery->delivered_at->format('M d, Y · h:i A') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar: Assignment / Rider Info -->
    <div class="card">
        @if(!$order->rider)
            <h3 class="section-title">Assign Rider</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Select an available rider to handle this delivery.</p>
            <form method="POST" action="{{ route('courier.assign-rider', $order->id) }}">
                @csrf
                <div class="form-group">
                    <label>Select Rider</label>
                    <select name="rider_id" required>
                        <option value="">-- Choose a rider --</option>
                        @foreach($riders as $rider)
                            <option value="{{ $rider->id }}">
                                {{ $rider->user->name }} — {{ $rider->is_available ? 'Available' : 'Busy' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">Assign Rider Now</button>
            </form>
        @else
            <h3 class="section-title">Assigned Rider</h3>
            <div style="display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; padding: 1rem; background-color: var(--bg-color); border: 1px solid var(--card-border); border-radius: 8px;">
                <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #333; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700; color: var(--accent-green);">
                    {{ substr($order->rider->user->name, 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 600;">{{ $order->rider->user->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $order->rider->phone }}</div>
                </div>
            </div>
            <div class="info-card" style="font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: var(--text-muted);">Availability</span>
                    <span style="color: {{ $order->rider->is_available ? 'var(--accent-green)' : 'var(--accent-orange)' }}">
                        {{ $order->rider->is_available ? 'Available' : 'Busy / On Delivery' }}
                    </span>
                </div>
            </div>
            
            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                <button onclick="openAssignModal({{ $order->id }})" class="btn btn-dark" style="width: 100%; justify-content: center; margin-top: 1rem;">Update Rider</button>
            @endif
        @endif
    </div>
</div>
@endsection

@section('modals')
<div id="assignModal" class="modal-overlay">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('assignModal')">&times;</button>
        <h3 class="modal-title">Reassign Rider</h3>
        <form method="POST" action="{{ route('courier.assign-rider', $order->id) }}">
            @csrf
            <div class="form-group">
                <label>Select New Rider</label>
                <select name="rider_id" required>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}" {{ optional($order->rider)->id == $rider->id ? 'selected' : '' }}>
                            {{ $rider->user->name }} — {{ $rider->is_available ? 'Available' : 'Busy' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update Assignment</button>
                <button type="button" class="btn btn-dark" style="flex: 1;" onclick="closeModal('assignModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

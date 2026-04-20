@extends('layouts.portal')

@section('title', 'Courier Dashboard')
@section('portal_title', 'Courier Portal')
@section('brand_route', route('courier.dashboard'))
@section('logout_route', route('courier.logout'))
@section('theme_class', 'theme-courier')

@section('badge')
    <div class="nav-info">
        <span>{{ $courier->name }}</span>
        <span style="opacity: 0.3;">|</span>
        <span style="font-size: 0.8rem;">{{ auth()->user()->email }}</span>
    </div>
@endsection

@section('nav_extra')
    <a href="{{ route('courier.dashboard') }}" class="tab {{ request()->routeIs('courier.dashboard') ? 'tab-active' : '' }}">Dashboard</a>
    <a href="{{ route('courier.orders') }}" class="tab {{ request()->routeIs('courier.orders') ? 'tab-active' : '' }}">Orders</a>
    <a href="{{ route('courier.riders') }}" class="tab {{ request()->routeIs('courier.riders') ? 'tab-active' : '' }}">Riders</a>
@endsection

@section('content')
<div class="grid grid-cols-4" style="margin-bottom: 2rem;">
    <div class="card stat-card">
        <span class="stat-label">Unassigned Orders</span>
        <span class="stat-value">{{ $stats['unassigned'] }}</span>
        <div class="stat-meta" style="color: var(--accent-orange); display: flex; align-items: center; gap: 6px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-orange);"></div>
            Needs rider
        </div>
    </div>
    <div class="card stat-card">
        <span class="stat-label">In Transit</span>
        <span class="stat-value">{{ $stats['in_transit'] }}</span>
        <div class="stat-meta" style="color: var(--accent-blue); display: flex; align-items: center; gap: 6px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-blue);"></div>
            Out for delivery
        </div>
    </div>
    <div class="card stat-card">
        <span class="stat-label">Completed Today</span>
        <span class="stat-value">{{ $stats['delivered'] }}</span>
        <div class="stat-meta" style="color: var(--accent-green); display: flex; align-items: center; gap: 6px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green);"></div>
            Delivered
        </div>
    </div>
    <div class="card stat-card">
        <span class="stat-label">Active Riders</span>
        <span class="stat-value">{{ $stats['active_riders'] }}</span>
        <div class="stat-meta" style="color: var(--accent-green); display: flex; align-items: center; gap: 6px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green);"></div>
            Available now
        </div>
    </div>
</div>

<div class="grid grid-main">
    <!-- Left Column: Pending Orders -->
    <div class="card">
        <div class="section-header">
            <div>
                <span class="section-title">Orders needing a rider</span>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0 0;">Assign these orders to your active riders.</p>
            </div>
        </div>
        <div style="margin-top: 1rem;">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Total</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingOrders as $order)
                    <tr>
                        <td><a href="{{ route('courier.orders.show', $order->id) }}" class="order-id">#{{ $order->id }}</a></td>
                        <td style="font-weight: 500;">{{ $order->user->name ?? 'Guest' }}</td>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                @php $ci = $order->customer_info; @endphp
                                <span style="font-weight: 600;">{{ $ci['city'] ?? 'Davao City' }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $ci['address_line_1'] ?? '' }}, {{ $ci['city'] ?? '' }}
                                </span>
                            </div>
                        </td>
                        <td style="font-weight: 700; color: var(--accent-green);">₱{{ number_format($order->total, 2) }}</td>
                        <td style="text-align: right;">
                            @if($order->status === 'processing')
                                <form action="{{ route('courier.update-status', $order->id) }}" method="POST" style="margin: 0; display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="shipped">
                                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--accent-orange); color: #000; padding: 8px 16px;">
                                        Mark Shipped
                                    </button>
                                </form>
                            @else
                                <button onclick="openAssignModal({{ $order->id }})" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
                                    Assign Rider
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 4rem 2rem;">
                            <div style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.2;">🚚</div>
                            <div>No orders awaiting assignment.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Riders -->
    <div class="card">
        <div class="section-header">
            <span class="section-title">My Riders</span>
            <button onclick="openModal('addRiderModal')" class="btn btn-outline btn-sm" style="border-radius: 8px; font-size: 0.7rem;">+ Add</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
            @forelse($riders as $rider)
            <div class="rider-item" style="padding: 12px; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid var(--card-border);">
                <div class="rider-info">
                    @php
                        $initials = collect(explode(' ', $rider->user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
                        $colors = ['#22c55e', '#f59e0b', '#3b82f6', '#14b8a6', '#ef4444'];
                        $bgColor = $colors[$rider->id % count($colors)];
                    @endphp
                    <div class="rider-avatar" style="width: 38px; height: 38px; background-color: {{ $bgColor }}22; color: {{ $bgColor }}; border: 1px solid {{ $bgColor }}44; font-size: 0.8rem;">
                        {{ $initials }}
                    </div>
                    <div class="rider-main">
                        <div class="rider-name" style="font-size: 0.9rem;">{{ $rider->user->name }}</div>
                        <div class="rider-meta" style="font-size: 0.75rem;">{{ $rider->activeDeliveries->count() }} active task</div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                    <span class="badge {{ $rider->is_available ? 'badge-green' : 'badge-orange' }}" style="font-size: 0.65rem; padding: 2px 8px;">
                        {{ $rider->is_available ? 'Available' : 'Busy' }}
                    </span>
                </div>
            </div>
            @empty
            <div style="text-align: center; color: var(--text-muted); padding: 2rem 1rem; font-size: 0.85rem;">
                No riders registered yet.
            </div>
            @endforelse
        </div>
        @if($riders->count() > 0)
            <a href="{{ route('courier.riders') }}" style="display: block; text-align: center; margin-top: 1.5rem; color: var(--accent-green); text-decoration: none; font-size: 0.85rem; font-weight: 600;">View all riders →</a>
        @endif
    </div>
</div>
@endsection

@section('modals')
<!-- Assign Rider Modal -->
<div id="assignModal" class="modal-overlay">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('assignModal')">&times;</button>
        <h3 class="modal-title" id="assignModalTitle">Assign rider modal</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Select rider</p>
        <form id="assignForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <select name="rider_id" required>
                    <option value="">-- Choose a rider --</option>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}">
                            {{ $rider->user->name }} — {{ $rider->is_available ? 'Available' : 'Busy' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 2rem; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Assign</button>
                <button type="button" class="btn btn-dark" style="padding: 10px 24px;" onclick="closeModal('assignModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Rider Modal -->
<div id="addRiderModal" class="modal-overlay">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('addRiderModal')">&times;</button>
        <h3 class="modal-title">Add New Rider</h3>
        <form method="POST" action="{{ route('courier.riders.store') }}">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Add Rider</button>
                <button type="button" class="btn btn-dark" style="flex: 1;" onclick="closeModal('addRiderModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignModal(orderId) {
        const modal = document.getElementById('assignModal');
        const title = document.getElementById('assignModalTitle');
        const form = document.getElementById('assignForm');
        
        title.innerText = `Assign rider modal (for order #${orderId})`;
        form.action = `/courier/orders/${orderId}/assign-rider`;
        modal.style.display = 'flex';
    }
</script>
@endsection

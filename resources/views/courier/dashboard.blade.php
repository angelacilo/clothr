@extends('layouts.portal')

@section('title', 'Courier Dashboard')
@section('portal_title', 'Courier Portal')
@section('brand_route', route('courier.dashboard'))
@section('logout_route', route('courier.logout'))

@section('badge')
    <div class="nav-info">
        <span>{{ $courier->name }}</span>
        <span>•</span>
        <span>{{ auth()->user()->email }}</span>
    </div>
@endsection

@section('nav_extra')
    <div style="display: flex; gap: 1rem; margin-right: 1.5rem;">
        <a href="{{ route('courier.dashboard') }}" class="tab {{ request()->routeIs('courier.dashboard') ? 'tab-active' : '' }}">Dashboard</a>
        <a href="{{ route('courier.orders') }}" class="tab {{ request()->routeIs('courier.orders') ? 'tab-active' : '' }}">Orders</a>
        <a href="{{ route('courier.riders') }}" class="tab {{ request()->routeIs('courier.riders') ? 'tab-active' : '' }}">Riders</a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-4">
    <div class="card stat-card">
        <span class="stat-label">Unassigned orders</span>
        <span class="stat-value">{{ $stats['unassigned'] }}</span>
        <span class="stat-meta" style="color: var(--accent-orange);">Needs rider</span>
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
    <div class="card stat-card">
        <span class="stat-label">Active riders</span>
        <span class="stat-value">{{ $stats['active_riders'] }}</span>
        <span class="stat-meta" style="color: var(--accent-green);">Available</span>
    </div>
</div>

<div class="grid grid-main" style="margin-top: 1.5rem;">
    <!-- Left Column: Pending Orders -->
    <div class="card">
        <div class="section-header">
            <span class="section-title">Orders needing a rider</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingOrders as $order)
                <tr>
                    <td><a href="{{ route('courier.orders.show', $order->id) }}" class="order-id">#{{ $order->id }}</a></td>
                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                    <td><strong>{{ $order->customer_info['city'] ?? $order->customer_info['address'] ?? 'Davao City' }}</strong></td>
                    <td>₱{{ number_format($order->total, 0) }}</td>
                    <td>
                        <button onclick="openAssignModal({{ $order->id }})" class="btn btn-dark">
                            Assign rider ↗
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        No orders awaiting assignment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Right Column: Riders -->
    <div class="card">
        <div class="section-header">
            <span class="section-title">My riders</span>
            <button onclick="openModal('addRiderModal')" class="btn btn-outline btn-sm">+ Add</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
            @forelse($riders as $rider)
            <div class="rider-item">
                <div class="rider-info">
                    @php
                        $initials = collect(explode(' ', $rider->user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
                        $colors = ['#22c55e', '#f59e0b', '#3b82f6', '#14b8a6', '#ef4444'];
                        $bgColor = $colors[$rider->id % count($colors)];
                    @endphp
                    <div class="rider-avatar" style="background-color: rgba({{ hexdec(substr($bgColor, 1, 2)) }}, {{ hexdec(substr($bgColor, 3, 2)) }}, {{ hexdec(substr($bgColor, 5, 2)) }}, 0.1); color: {{ $bgColor }}; border: 1.5px solid {{ $bgColor }};">
                        {{ $initials }}
                    </div>
                    <div class="rider-main">
                        <div class="rider-name">{{ $rider->user->name }}</div>
                        <div class="rider-meta">{{ $rider->activeDeliveries->count() }} active</div>
                    </div>
                </div>
                <span class="badge {{ $rider->is_available ? 'badge-green' : 'badge-orange' }}">
                    {{ $rider->is_available ? 'Available' : 'Busy' }}
                </span>
            </div>
            @empty
            <div style="text-align: center; color: var(--text-muted); padding: 1rem;">No riders registered yet.</div>
            @endforelse
        </div>
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
                <button type="submit" class="btn btn-green" style="padding: 10px 24px;">Assign</button>
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
                <button type="submit" class="btn btn-green" style="flex: 1;">Add Rider</button>
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

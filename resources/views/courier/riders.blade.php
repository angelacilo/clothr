@extends('layouts.portal')

@section('title', 'Manage Riders')
@section('portal_title', 'Courier Portal')
@section('brand_route', route('courier.dashboard'))
@section('logout_route', route('courier.logout'))

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
<div class="card">
    <div class="section-header">
        <span class="section-title">Registered Riders</span>
        <button onclick="openModal('addRiderModal')" class="btn btn-green">+ Add New Rider</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rider Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Active Deliveries</th>
                <th>Total Completed</th>
                <th>Status</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riders as $rider)
            <tr>
                <td>{{ $rider->user->name }}</td>
                <td>{{ $rider->user->email }}</td>
                <td>{{ $rider->phone }}</td>
                <td>
                    <span style="font-weight: 600; color: {{ $rider->activeDeliveries->count() > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }}">
                        {{ $rider->activeDeliveries->count() }}
                    </span>
                </td>
                <td>{{ $rider->deliveries->where('status', 'delivered')->count() }}</td>
                <td>
                    @if($rider->is_available)
                        <span class="badge badge-green">Available</span>
                    @else
                        <span class="badge badge-orange" style="color: var(--accent-orange); border-color: var(--accent-orange); background-color: rgba(245, 158, 11, 0.1);">Busy</span>
                    @endif
                </td>
                <td>{{ $rider->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No riders registered under your courier service.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modals')
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
                <input type="text" name="phone" required placeholder="09XXXXXXXXX">
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
                <button type="submit" class="btn btn-green" style="flex: 1;">Create Account</button>
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeModal('addRiderModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.portal')

@section('title', 'All Orders')
@section('portal_title', 'Courier Portal')
@section('brand_route', route('courier.dashboard'))
@section('logout_route', route('courier.logout'))

@section('badge')
    <span class="badge badge-green">{{ $courier->code }}</span>
@endsection

@section('nav_extra')
    <div style="display: flex; gap: 1rem; margin-right: 1.5rem;">
        <a href="{{ route('courier.dashboard') }}" class="tab {{ request()->routeIs('courier.dashboard') ? 'tab-active' : '' }}">Dashboard</a>
        <a href="{{ route('courier.orders') }}" class="tab {{ request()->routeIs('courier.orders') ? 'tab-active' : '' }}">Orders</a>
        <a href="{{ route('courier.riders') }}" class="tab {{ request()->routeIs('courier.riders') ? 'tab-active' : '' }}">Riders</a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="section-header">
        <span class="section-title">All Orders for {{ $courier->name }}</span>
    </div>

    {{-- Filter Tabs --}}
    <div class="tabs">
        <a href="{{ route('courier.orders') }}" class="tab {{ !request('status') ? 'tab-active' : '' }}">All</a>
        <a href="{{ route('courier.orders', ['status' => 'unassigned']) }}" class="tab {{ request('status') == 'unassigned' ? 'tab-active' : '' }}">Unassigned</a>
        <a href="{{ route('courier.orders', ['status' => 'in_transit']) }}" class="tab {{ request('status') == 'in_transit' ? 'tab-active' : '' }}">In Transit</a>
        <a href="{{ route('courier.orders', ['status' => 'delivered']) }}" class="tab {{ request('status') == 'delivered' ? 'tab-active' : '' }}">Delivered</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Rider Assigned</th>
                <th>Total</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'Guest' }}</td>
                <td>
                    @php
                        $badgeClass = match($order->status) {
                            'pending', 'processing' => 'badge-orange',
                            'shipped', 'out_for_delivery' => 'badge-blue',
                            'delivered' => 'badge-green',
                            default => 'badge-orange'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        {{ str_replace('_', ' ', strtoupper($order->status)) }}
                    </span>
                </td>
                <td>
                    @if($order->rider)
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #333; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                {{ substr($order->rider->user->name, 0, 1) }}
                            </div>
                            {{ $order->rider->user->name }}
                        </span>
                    @else
                        <span style="color: var(--accent-red); font-weight: 500;">Unassigned</span>
                    @endif
                </td>
                <td>₱{{ number_format($order->total, 2) }}</td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('courier.orders.show', $order->id) }}" class="btn btn-outline" style="padding: 4px 12px; font-size: 0.75rem;">Details</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1.5rem;">
        {{ $orders->links() }}
    </div>
</div>
@endsection

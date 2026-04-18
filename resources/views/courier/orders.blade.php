@extends('layouts.portal')

@section('title', 'All Orders')
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
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--card-border); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="section-title" style="margin: 0;">Courier Operations</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">Manage and track all shipments for {{ $courier->name }}.</p>
        </div>
        
        {{-- Filter Tabs --}}
        <div style="display: flex; gap: 8px; background: #000; padding: 4px; border-radius: 10px; border: 1px solid var(--card-border);">
            <a href="{{ route('courier.orders') }}" class="tab {{ !request('status') ? 'tab-active' : '' }}" style="font-size: 0.8rem; padding: 6px 12px;">All</a>
            <a href="{{ route('courier.orders', ['status' => 'unassigned']) }}" class="tab {{ request('status') == 'unassigned' ? 'tab-active' : '' }}" style="font-size: 0.8rem; padding: 6px 12px;">Unassigned</a>
            <a href="{{ route('courier.orders', ['status' => 'in_transit']) }}" class="tab {{ request('status') == 'in_transit' ? 'tab-active' : '' }}" style="font-size: 0.8rem; padding: 6px 12px;">In Transit</a>
            <a href="{{ route('courier.orders', ['status' => 'delivered']) }}" class="tab {{ request('status') == 'delivered' ? 'tab-active' : '' }}" style="font-size: 0.8rem; padding: 6px 12px;">Delivered</a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="padding: 1.25rem 2rem; background: #181818;">Order</th>
                    <th style="padding: 1.25rem 1rem; background: #181818;">Customer</th>
                    <th style="padding: 1.25rem 1rem; background: #181818;">Status</th>
                    <th style="padding: 1.25rem 1rem; background: #181818;">Rider</th>
                    <th style="padding: 1.25rem 1rem; background: #181818;">Total</th>
                    <th style="padding: 1.25rem 1rem; background: #181818;">Date</th>
                    <th style="padding: 1.25rem 2rem; background: #181818; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr onclick="window.location='{{ route('courier.orders.show', $order->id) }}'" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.25rem 2rem;">
                        <span style="font-weight: 700; color: var(--accent-primary);">#{{ $order->id }}</span>
                    </td>
                    <td style="padding: 1.25rem 1rem;">
                        <div style="font-weight: 600;">{{ $order->user->name ?? 'Guest User' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $order->customer_info['city'] ?? 'Davao City' }}</div>
                    </td>
                    <td style="padding: 1.25rem 1rem;">
                        @php
                            $badgeClass = 'badge-orange';
                            switch($order->status) {
                                case 'shipped': $badgeClass = 'badge-blue'; break;
                                case 'out_for_delivery': $badgeClass = 'badge-blue'; break;
                                case 'delivered': $badgeClass = 'badge-green'; break;
                                case 'lost': $badgeClass = 'badge-red'; break;
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}" style="font-size: 0.65rem; padding: 4px 10px;">
                            {{ str_replace('_', ' ', strtoupper($order->status)) }}
                        </span>
                    </td>
                    <td style="padding: 1.25rem 1rem;">
                        @if($order->rider)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background-color: rgba(var(--accent-primary-rgb), 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(var(--accent-primary-rgb), 0.2);">
                                    {{ substr($order->rider->user->name, 0, 1) }}
                                </div>
                                <span style="font-size: 0.9rem; font-weight: 500;">{{ $order->rider->user->name }}</span>
                            </div>
                        @else
                            <span style="color: var(--accent-red); font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 4px;">
                                <div style="width: 6px; height: 6px; border-radius: 50%; background: var(--accent-red);"></div>
                                Unassigned
                            </span>
                        @endif
                    </td>
                    <td style="padding: 1.25rem 1rem; font-weight: 700; color: var(--accent-green);">
                        ₱{{ number_format($order->total, 2) }}
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 1.25rem 2rem; text-align: right; display: flex; gap: 8px; justify-content: flex-end;" onclick="event.stopPropagation()">
                        @if($order->status === 'processing')
                            <form action="{{ route('courier.update-status', $order->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="btn btn-primary btn-sm" style="background: var(--accent-orange); color: #000;">Mark Shipped</button>
                            </form>
                        @elseif($order->status === 'shipped' && $order->rider)
                            <form action="{{ route('courier.update-status', $order->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="status" value="out_for_delivery">
                                <button type="submit" class="btn btn-primary btn-sm">Release to Rider</button>
                            </form>
                        @elseif($order->status === 'shipped' && !$order->rider)
                            <button onclick="window.location='{{ route('courier.orders.show', $order->id) }}'" class="btn btn-outline btn-sm" style="border-color: var(--accent-red); color: var(--accent-red);">Assign Rider</button>
                        @endif
                        <a href="{{ route('courier.orders.show', $order->id) }}" class="btn btn-outline btn-sm" style="border-radius: 8px; border-color: #333; font-size: 0.7rem; padding: 5px 12px;">Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 5rem 2rem;">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.2;">📦</div>
                        <div style="font-weight: 600;">No orders found in this category.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($orders->hasPages())
    <div style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $orders->links() }}
    </div>
@endif
@endsection

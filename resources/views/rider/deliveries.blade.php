@extends('layouts.portal')

@section('title', 'My Deliveries')
@section('portal_title', 'Rider Portal')
@section('brand_route', route('rider.dashboard'))
@section('logout_route', route('rider.logout'))

@section('nav_extra')
    <div style="display: flex; gap: 1rem; margin-right: 1.5rem;">
        <a href="{{ route('rider.dashboard') }}" class="tab {{ request()->routeIs('rider.dashboard') ? 'tab-active' : '' }}">My Tasks</a>
        <a href="{{ route('rider.deliveries') }}" class="tab {{ request()->routeIs('rider.deliveries') ? 'tab-active' : '' }}">History</a>
    </div>
@endsection

@section('badge')
    <span class="badge badge-green" style="margin-left: 5px;">{{ $rider->courier->code }}</span>
@endsection

@section('content')
<div class="card">
    <div class="section-header">
        <span class="section-title">Delivery History</span>
    </div>

    {{-- Filter Tabs --}}
    <div class="tabs">
        <a href="{{ route('rider.deliveries') }}" class="tab {{ !request('status') ? 'tab-active' : '' }}">All</a>
        <a href="{{ route('rider.deliveries', ['status' => 'assigned']) }}" class="tab {{ request('status') == 'assigned' ? 'tab-active' : '' }}">Assigned</a>
        <a href="{{ route('rider.deliveries', ['status' => 'in_transit']) }}" class="tab {{ request('status') == 'in_transit' ? 'tab-active' : '' }}">In Transit</a>
        <a href="{{ route('rider.deliveries', ['status' => 'delivered']) }}" class="tab {{ request('status') == 'delivered' ? 'tab-active' : '' }}">Delivered</a>
        <a href="{{ route('rider.deliveries', ['status' => 'failed']) }}" class="tab {{ request('status') == 'failed' ? 'tab-active' : '' }}">Failed</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Assigned At</th>
                <th>Picked Up At</th>
                <th>Delivered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveries as $delivery)
            <tr>
                <td>#{{ $delivery->order->id }}</td>
                <td>{{ $delivery->order->user->name ?? 'Guest' }}</td>
                <td>
                    @php
                        $badgeClass = match($delivery->status) {
                            'assigned' => 'badge-orange',
                            'picked_up', 'out_for_delivery' => 'badge-blue',
                            'delivered' => 'badge-green',
                            'failed' => 'badge-red',
                            default => 'badge-orange'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($delivery->status)) }}</span>
                </td>
                <td>{{ $delivery->assigned_at ? $delivery->assigned_at->format('M d, H:i') : '—' }}</td>
                <td>{{ $delivery->picked_up_at ? $delivery->picked_up_at->format('M d, H:i') : '—' }}</td>
                <td>{{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, H:i') : '—' }}</td>
                <td>
                    <a href="{{ route('rider.deliveries.show', $delivery->id) }}" class="btn btn-outline" style="padding: 4px 12px; font-size: 0.75rem;">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No delivery records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1.5rem;">
        {{ $deliveries->links() }}
    </div>
</div>
@endsection

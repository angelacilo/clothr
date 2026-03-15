@extends('profile.layout')

@section('profile_content')
<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 25px;">My Orders</h2>

<div class="order-tabs">
    <a href="{{ route('profile.orders', ['status' => 'all']) }}" class="order-tab {{ $status == 'all' ? 'active' : '' }}">All Orders</a>
    <a href="{{ route('profile.orders', ['status' => 'Pending']) }}" class="order-tab {{ $status == 'Pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('profile.orders', ['status' => 'Processing']) }}" class="order-tab {{ $status == 'Processing' ? 'active' : '' }}">Processing</a>
    <a href="{{ route('profile.orders', ['status' => 'Shipped']) }}" class="order-tab {{ $status == 'Shipped' ? 'active' : '' }}">Shipped</a>
    <a href="{{ route('profile.orders', ['status' => 'Delivered']) }}" class="order-tab {{ $status == 'Delivered' ? 'active' : '' }}">Delivered</a>
    <a href="{{ route('profile.orders', ['status' => 'Cancelled']) }}" class="order-tab {{ $status == 'Cancelled' ? 'active' : '' }}">Cancelled</a>
</div>

@if($orders->count() > 0)
    @foreach($orders as $order)
        <div class="order-card" style="transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow=''">
            <div class="order-card-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-weight: 700; color: #000;">#{{ $order->id }}</span>
                    <span style="color: var(--border-color);">|</span>
                    <span style="color: var(--text-muted);">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    @if($order->courier_name)
                        <span style="font-size: 12px; color: #7c3aed; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="truck" style="width: 13px;"></i>
                            {{ $order->courier_name }}
                        </span>
                    @endif
                    @if($order->tracking_number)
                        <span style="font-size: 11px; font-family: monospace; background: #f3f4f6; padding: 3px 8px; border-radius: 4px; color: #555;">
                            {{ $order->tracking_number }}
                        </span>
                    @endif
                    <div class="order-status {{ 'status-'.strtolower($order->status) }}">
                        {{ $order->status }}
                    </div>
                </div>
            </div>
            <div class="order-card-body">
                @foreach($order->items as $item)
                    <div class="order-item">
                        <img src="{{ $item['image'] ?? '/placeholder.png' }}" alt="{{ $item['name'] }}">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 15px;">{{ $item['name'] }}</div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Sz: {{ $item['size'] ?? 'M' }} | Qty: {{ $item['quantity'] ?? 1 }}</div>
                        </div>
                        <div style="font-weight: 700;">₱{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</div>
                    </div>
                @endforeach
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px dashed var(--border-color); margin-top: 15px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <a href="{{ route('profile.order', $order->id) }}" style="font-size: 13px; font-weight: 700; color: #111; text-decoration: none; display: flex; align-items: center; gap: 4px; transition: color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#111'">
                            <i data-lucide="eye" style="width: 14px;"></i> View Details
                        </a>
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" target="_blank" style="font-size: 12px; font-weight: 700; color: #7c3aed; text-decoration: none; display: flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f3e8ff; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#e9d5ff'" onmouseout="this.style.background='#f3e8ff'">
                                <i data-lucide="external-link" style="width: 12px;"></i> Track Package
                            </a>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span style="font-size: 14px; color: var(--text-muted);">Total Price:</span>
                        <span style="font-weight: 800; font-size: 18px;">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div style="text-align: center; padding: 60px 0;">
        <i data-lucide="shopping-bag" size="48" style="color: var(--border-color); margin-bottom: 20px;"></i>
        <p style="color: var(--text-muted); font-size: 16px;">No orders found in this category.</p>
        <a href="{{ route('shop') }}" class="btn-black" style="display: inline-block; margin-top: 20px; padding: 12px 30px; border-radius: 8px;">Start Shopping</a>
    </div>
@endif
@endsection

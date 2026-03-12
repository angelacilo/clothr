@extends('profile.layout')

@section('profile_content')
<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 25px;">My Orders</h2>

<div class="order-tabs">
    <a href="{{ route('profile.orders', ['status' => 'all']) }}" class="order-tab {{ $status == 'all' ? 'active' : '' }}">All Orders</a>
    <a href="{{ route('profile.orders', ['status' => 'Unpaid']) }}" class="order-tab {{ $status == 'Unpaid' ? 'active' : '' }}">Unpaid</a>
    <a href="{{ route('profile.orders', ['status' => 'Processing']) }}" class="order-tab {{ $status == 'Processing' ? 'active' : '' }}">Processing</a>
    <a href="{{ route('profile.orders', ['status' => 'Shipped']) }}" class="order-tab {{ $status == 'Shipped' ? 'active' : '' }}">Shipped</a>
    <a href="{{ route('profile.orders', ['status' => 'Delivered']) }}" class="order-tab {{ $status == 'Delivered' ? 'active' : '' }}">Review</a>
    <a href="{{ route('profile.orders', ['status' => 'Returned']) }}" class="order-tab {{ $status == 'Returned' ? 'active' : '' }}">Return</a>
</div>

@if($orders->count() > 0)
    @foreach($orders as $order)
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <span style="font-weight: 700; color: #000;">#{{ $order->id }}</span>
                    <span style="margin: 0 10px; color: var(--border-color);">|</span>
                    <span style="color: var(--text-muted);">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                <div class="order-status {{ 'status-'.strtolower($order->status) }}">
                    {{ $order->status }}
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
                <div style="display: flex; justify-content: flex-end; padding-top: 15px; border-top: 1px dashed var(--border-color); margin-top: 15px;">
                    <span style="font-size: 14px; color: var(--text-muted); margin-right: 15px;">Total Price:</span>
                    <span style="font-weight: 800; font-size: 18px;">₱{{ number_format($order->total, 2) }}</span>
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

@extends('admin.layouts.app')

@section('page-title', 'Order Details')
@section('page-subtitle', 'Order #' . $order->order_id)

@section('content')
    <div class="detail-header">
        <div>
            <h2>Order #{{ $order->order_id }}</h2>
            <p class="text-muted">{{ $order->created_at->format('F d, Y H:i') }}</p>
        </div>
        <div class="detail-actions">
            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="btn btn-primary">Edit Order</a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <h3 class="card-title">Customer Information</h3>
            <div class="detail-item">
                <span class="label">Name</span>
                <span class="value">{{ $order->user->name ?? 'Guest' }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Email</span>
                <span class="value">{{ $order->user->email ?? $order->email }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Phone</span>
                <span class="value">{{ $order->user->phone_num ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="detail-card">
            <h3 class="card-title">Order Status</h3>
            <div class="detail-item">
                <span class="label">Current Status</span>
                <span class="badge badge-{{ str_replace('_', '-', $order->order_status) }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
            <div class="detail-item">
                <span class="label">Payment Status</span>
                <span class="badge badge-success">
                    {{ $order->payment ? ucfirst($order->payment->payment_status ?? 'Pending') : 'Pending' }}
                </span>
            </div>
            <div class="detail-item">
                <span class="label">Tracking Number</span>
                <span class="value">{{ $order->tracking_num ?? 'Not assigned' }}</span>
            </div>
        </div>
    </div>

    <div class="table-card">
        <h3 class="card-title">Order Items</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">Total Amount</td>
                    <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <h3 class="card-title">Shipping Address</h3>
            <p>{{ $order->shipping_address ?? 'N/A' }}</p>
        </div>

        @if ($order->delivery)
            <div class="detail-card">
                <h3 class="card-title">Delivery Information</h3>
                <div class="detail-item">
                    <span class="label">Delivery Status</span>
                    <span class="badge badge-{{ str_replace('_', '-', $order->delivery->delivery_status ?? '') }}">
                        {{ ucfirst($order->delivery->delivery_status ?? 'Pending') }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="label">Delivered Date</span>
                    <span class="value">{{ $order->delivery->delivered_date ? $order->delivery->delivered_date->format('M d, Y') : 'Not delivered' }}</span>
                </div>
            </div>
        @endif
    </div>
@endsection

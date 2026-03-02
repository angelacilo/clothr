@extends('admin.layouts.app')

@section('page-title', 'Edit Order')
@section('page-subtitle', 'Order #' . $order->order_id)

@section('content')
    <div class="form-card">
        <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">Order Status</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="order_status" class="form-label">Order Status *</label>
                        <select id="order_status" name="order_status" class="form-control" required>
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tracking_num" class="form-label">Tracking Number</label>
                        <input type="text" id="tracking_num" name="tracking_num" class="form-control" 
                               placeholder="Enter tracking number" value="{{ old('tracking_num', $order->tracking_num) }}">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Order</button>
                <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

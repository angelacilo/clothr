@extends('admin.layouts.app')

@section('page-title', 'Orders')
@section('page-subtitle', 'Manage all orders')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by customer name..." value="{{ request('search') }}" class="search-input">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Items Count</th>
                    <th>Total Amount</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->order_id }}</strong></td>
                        <td>{{ $order->user->name ?? 'Guest' }}</td>
                        <td>{{ $order->items->count() }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if ($order->payment)
                                <span class="badge badge-success">
                                    {{ ucfirst($order->payment->payment_status ?? 'Pending') }}
                                </span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="status-dropdown" data-order-id="{{ $order->order_id }}">
                                <select class="order-status-select" onchange="updateOrderStatus({{ $order->order_id }}, this.value)">
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="action-btn view-btn" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $orders->links() }}
    </div>

    @push('scripts')
        <script>
            function updateOrderStatus(orderId, status) {
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Order status updated successfully!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to update order status', 'error');
                });
            }

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type}`;
                notification.textContent = message;
                document.querySelector('.content').prepend(notification);
                setTimeout(() => notification.remove(), 3000);
            }
        </script>
    @endpush
@endsection

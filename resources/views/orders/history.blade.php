<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - CLOTHR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; background: #f4f6f9; margin: 0; padding: 0;">
    {{-- Header --}}
    <header style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 0;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('home') }}" style="font-size: 24px; font-weight: 700; color: #000; text-decoration: none;">CLOTHR</a>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="{{ route('home') }}" style="color: #666; text-decoration: none; font-size: 14px;">Shop</a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px;">Logout</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Container --}}
    <div style="max-width: 1400px; margin: 30px auto; padding: 0 20px;">
        <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">Order History</h1>

        @if($orders->isEmpty())
            <div style="background: white; border-radius: 8px; padding: 60px 20px; text-align: center; color: #999;">
                <i class="fas fa-inbox" style="font-size: 60px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                <h2 style="color: #333; margin-bottom: 12px;">No Orders Yet</h2>
                <p style="margin-bottom: 30px;">You haven't placed any orders yet</p>
                <a href="{{ route('products.index') }}" style="background: #2563eb; color: white; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-block;">
                    Start Shopping
                </a>
            </div>
        @else
            <div style="background: white; border-radius: 8px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f4f6f9; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Order ID</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Date</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Items</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Total</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Status</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 14px; color: #333;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 16px; color: #2563eb;"><strong>#{{ str_pad($order->order_id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td style="padding: 16px; color: #666; font-size: 14px;">{{ $order->created_at->format('M d, Y') }}</td>
                                <td style="padding: 16px; color: #666; font-size: 14px;">{{ $order->items->count() }} item(s)</td>
                                <td style="padding: 16px; color: #333; font-weight: 600;">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td style="padding: 16px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                        @switch($order->order_status)
                                            @case('pending')
                                                background: #fffbeb; color: #92400e;
                                            @break
                                            @case('processing')
                                                background: #eff6ff; color: #0c4a6e;
                                            @break
                                            @case('shipped')
                                                background: #dcfce7; color: #166534;
                                            @break
                                            @case('delivered')
                                                background: #ecfdf5; color: #065f46;
                                            @break
                                            @case('cancelled')
                                                background: #fef2f2; color: #7f1d1d;
                                            @break
                                        @endswitch
                                    ">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td style="padding: 16px;">
                                    <a href="{{ route('orders.show', $order->order_id) }}" style="color: #2563eb; text-decoration: none; font-weight: 600; font-size: 14px;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</body>
</html>

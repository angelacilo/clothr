<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ str_pad($order->order_id, 6, '0', STR_PAD_LEFT) }} - CLOTHR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; background: #f4f6f9; margin: 0; padding: 0;">
    {{-- Header --}}
    <header style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 0;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('home') }}" style="font-size: 24px; font-weight: 700; color: #000; text-decoration: none;">CLOTHR</a>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="{{ route('orders.history') }}" style="color: #2563eb; text-decoration: none; font-size: 14px;">Back to Orders</a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px;">Logout</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Container --}}
    <div style="max-width: 1000px; margin: 30px auto; padding: 0 20px;">
        {{-- Order Header --}}
        <div style="background: white; border-radius: 8px; padding: 30px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 24px; color: #333; margin: 0 0 8px 0;">Order #{{ str_pad($order->order_id, 6, '0', STR_PAD_LEFT) }}</h1>
                    <p style="color: #999; margin: 0;">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <span style="display: inline-block; padding: 8px 16px; border-radius: 4px; font-weight: 600; font-size: 14px;
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
            </div>

            {{-- Order Info Grid --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div>
                    <p style="color: #999; font-size: 12px; text-transform: uppercase; margin: 0 0 8px 0;">Shipping To</p>
                    <p style="color: #333; font-weight: 600; margin: 0;">{{ $order->first_name }} {{ $order->last_name }}</p>
                    <p style="color: #666; font-size: 13px; margin: 4px 0 0 0;">{{ $order->shipping_address }}</p>
                </div>
                <div>
                    <p style="color: #999; font-size: 12px; text-transform: uppercase; margin: 0 0 8px 0;">Contact</p>
                    <p style="color: #333; font-weight: 600; margin: 0;">{{ $order->email }}</p>
                    <p style="color: #666; font-size: 13px; margin: 4px 0 0 0;">{{ $order->phone_number }}</p>
                </div>
                <div>
                    <p style="color: #999; font-size: 12px; text-transform: uppercase; margin: 0 0 8px 0;">Payment Method</p>
                    <p style="color: #333; font-weight: 600; margin: 0;">
                        @if($order->payment)
                            {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div style="background: white; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
            <div style="background: #f4f6f9; padding: 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #333;">
                Order Items
            </div>
            <div>
                @foreach($order->items as $item)
                    <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 16px;">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                     alt="{{ $item->product->name }}"
                                     style="width: 80px; height: 100px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='https://via.placeholder.com/80x100?text=No+Image'">
                            @else
                                <img src="https://via.placeholder.com/80x100?text=No+Image" 
                                     style="width: 80px; height: 100px; object-fit: cover; border-radius: 4px;">
                            @endif
                            <div>
                                <a href="{{ route('products.show', $item->product->product_id) }}" style="color: #2563eb; text-decoration: none; font-weight: 600; display: block; margin-bottom: 8px;">
                                    {{ $item->product->name }}
                                </a>
                                <p style="color: #999; font-size: 13px; margin: 0;">SKU: {{ $item->product->product_id }}</p>
                                <p style="color: #999; font-size: 13px; margin: 4px 0 0 0;">Quantity: <strong style="color: #333;">{{ $item->quantity }}</strong></p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <p style="color: #666; font-size: 13px; margin: 0 0 8px 0;">${{ number_format($item->price, 2) }} each</p>
                            <p style="color: #333; font-weight: 700; font-size: 16px; margin: 0;">${{ number_format($item->price * $item->quantity, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order Summary --}}
        <div style="background: white; border-radius: 8px; padding: 20px; display: grid; grid-template-columns: 1fr 300px; gap: 30px; align-items: start;">
            <div>
                {{-- Delivery Info --}}
                @if($order->delivery)
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px;">
                        <h3 style="color: #166534; margin: 0 0 12px 0;">Delivery Status</h3>
                        <p style="color: #166534; margin: 0;">
                            <i class="fas fa-truck" style="margin-right: 8px;"></i>
                            {{ $order->delivery->delivery_status ?? 'Pending' }}
                        </p>
                        @if($order->delivery->tracking_num)
                            <p style="color: #666; margin: 8px 0 0 0; font-size: 13px;">
                                Tracking: <strong>{{ $order->delivery->tracking_num }}</strong>
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Total Summary --}}
            <div>
                <div style="background: #f4f6f9; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #666;">Subtotal</span>
                        <span style="color: #333; font-weight: 600;">${{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #666;">Shipping</span>
                        <span style="color: #333; font-weight: 600;">FREE</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #666;">Tax</span>
                        <span style="color: #333; font-weight: 600;">${{ number_format($order->total_amount * 0.1, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 12px;">
                        <span style="color: #333; font-weight: 700; font-size: 16px;">Total</span>
                        <span style="color: #2563eb; font-weight: 700; font-size: 18px;">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back Link --}}
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('orders.history') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                ← Back to Order History
            </a>
        </div>
    </div>
</body>
</html>

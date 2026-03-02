<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - CLOTHR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; background: #f4f6f9; margin: 0; padding: 0;">
    {{-- Header --}}
    <header style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 0;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
            <a href="{{ route('home') }}" style="font-size: 24px; font-weight: 700; color: #000; text-decoration: none;">CLOTHR</a>
        </div>
    </header>

    {{-- Confirmation Container --}}
    <div style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        {{-- Success Message --}}
        <div style="background: white; border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 30px;">
            <div style="font-size: 60px; color: #22c55e; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 style="font-size: 32px; color: #333; margin-bottom: 12px;">Order Confirmed!</h1>
            <p style="color: #666; font-size: 16px; margin-bottom: 30px;">
                Thank you for your order. We'll send you an email confirmation shortly.
            </p>

            {{-- Order Number --}}
            <div style="background: #f4f6f9; padding: 20px; border-radius: 6px; margin-bottom: 30px;">
                <div style="font-size: 12px; color: #999; margin-bottom: 4px;">ORDER NUMBER</div>
                <div style="font-size: 24px; font-weight: 700; color: #333;">#{{ str_pad($order->order_id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>

            {{-- Order Details --}}
            <div style="background: #f9fafb; padding: 24px; border-radius: 6px; text-align: left; margin-bottom: 30px;">
                <h3 style="margin-bottom: 16px; color: #333;">Order Details</h3>
                
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #666;">Order Date</span>
                    <span style="font-weight: 600; color: #333;">{{ $order->created_at->format('M d, Y') }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #666;">Shipping To</span>
                    <span style="font-weight: 600; color: #333;">{{ $order->first_name }} {{ $order->last_name }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #666;">Shipping Address</span>
                    <span style="font-weight: 600; color: #333; text-align: right;">{{ $order->shipping_address }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #666;">Payment Method</span>
                    <span style="font-weight: 600; color: #333;">
                        @if($order->payment)
                            {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}
                        @else
                            Not specified
                        @endif
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                    <span style="color: #666;">Total Amount</span>
                    <span style="font-weight: 700; color: #2563eb; font-size: 18px;">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Items Summary --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; margin-bottom: 30px;">
                <div style="background: #f4f6f9; padding: 16px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #333;">
                    Items Ordered
                </div>
                <div style="padding: 0;">
                    @foreach($order->items as $item)
                        <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; color: #333; margin-bottom: 4px;">{{ $item->product->name }}</div>
                                <div style="font-size: 13px; color: #999;">Qty: {{ $item->quantity }}</div>
                            </div>
                            <div style="text-align: right; font-weight: 600; color: #333;">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- What's Next --}}
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 20px; text-align: left; margin-bottom: 30px;">
                <h3 style="color: #2563eb; margin-bottom: 12px;">What's Next?</h3>
                <ol style="color: #666; padding-left: 20px;">
                    <li style="margin-bottom: 8px;">We'll prepare your order and send you tracking information</li>
                    <li style="margin-bottom: 8px;">Your order will be shipped within 2-3 business days</li>
                    <li>You'll receive an email notification when your package ships</li>
                </ol>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="{{ route('orders.show', $order->order_id) }}" style="background: #2563eb; color: white; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-block;">
                    View Order Details
                </a>
                <a href="{{ route('products.index') }}" style="background: white; color: #2563eb; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-block; border: 1px solid #2563eb;">
                    Continue Shopping
                </a>
            </div>
        </div>

        {{-- Help Section --}}
        <div style="background: white; border-radius: 8px; padding: 30px; text-align: center;">
            <h3 style="margin-bottom: 12px; color: #333;">Need Help?</h3>
            <p style="color: #666; margin-bottom: 12px;">
                For order inquiries, contact us at <a href="mailto:support@clothr.com" style="color: #2563eb; text-decoration: none;">support@clothr.com</a>
            </p>
            <p style="color: #999; font-size: 13px;">We typically respond within 24 hours</p>
        </div>
    </div>
</body>
</html>

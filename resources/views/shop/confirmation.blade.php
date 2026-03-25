@extends('layouts.shop')

@section('title', 'Order Confirmed')

@section('content')
<div class="container section" style="text-align: center; max-width: 600px;">
    <div style="margin-bottom: 40px;">
        <i data-lucide="check-circle" size="80" style="color: #388e3c; margin: 0 auto;"></i>
    </div>
    <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 20px;">Thank you for your order!</h1>
    <p style="color: var(--text-secondary); font-size: 18px; margin-bottom: 20px;">Your order #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }} has been placed and is being processed.</p>
    
    <div style="background: rgba(28,25,23,0.04); border-radius: var(--radius-sm); padding: 16px 20px; display: inline-flex; align-items: center; gap: 12px; margin-bottom: 40px; text-align: left; max-width: 100%;">
        <i data-lucide="bell" style="width: 24px; height: 24px; color: var(--accent-warm); flex-shrink: 0;"></i>
        <p style="font-size: 13px; color: var(--ink-soft); margin: 0; line-height: 1.5;">We will notify you when your order status is updated. Check your notifications bell at the top of the page.</p>
    </div>

    <div style="background: #f8f9fa; border-radius: var(--radius-md); padding: 30px; text-align: left; margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase;">Order Details</h3>
        <div style="display: grid; gap: 10px; font-size: 14px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Status</span>
                <span style="font-weight: 700; color: #f59e0b;">{{ $order->status }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Total Amount</span>
                <span style="font-weight: 700;">₱{{ number_format($order->total, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Shipping To</span>
                <span>{{ $order->customer_info['first_name'] }} {{ $order->customer_info['last_name'] }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Address</span>
                <span>{{ $order->customer_info['address_line_1'] ?? $order->customer_info['address'] ?? '' }}, {{ $order->customer_info['city'] }}</span>
            </div>
        </div>
    </div>

    <a href="{{ route('home') }}" class="hero__btn" style="background: #000; color: #fff; text-decoration: none; display: inline-block;">Back to Home</a>
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    {{-- Header --}}
    <header class="checkout-header">
        <div class="checkout-header-inner">
            <a href="{{ route('home') }}" class="checkout-logo">CLOTHR</a>
        </div>
    </header>

    {{-- Checkout Container --}}
    <div class="checkout-container">
        <div class="checkout-wrapper">
            {{-- Checkout Form --}}
            <div class="checkout-form-section">
                <h1>Checkout</h1>

                <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form" id="checkoutForm">
                    @csrf

                    {{-- Shipping Information --}}
                    <fieldset class="form-section">
                        <legend>Shipping Address</legend>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required 
                                       value="{{ old('first_name', auth()->user()->name) }}">
                                @error('first_name')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required 
                                       value="{{ old('last_name') }}">
                                @error('last_name')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required 
                                   value="{{ old('email', auth()->user()->email) }}">
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number *</label>
                            <input type="tel" id="phone_number" name="phone_number" required 
                                   value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Shipping Address *</label>
                            <input type="text" id="shipping_address" name="shipping_address" required 
                                   placeholder="Street address, apartment, suite, etc."
                                   value="{{ old('shipping_address') }}">
                            @error('shipping_address')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- Payment Method --}}
                    <fieldset class="form-section">
                        <legend>Payment Method</legend>

                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="credit_card" checked>
                                <div class="payment-label">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Credit Card</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <div class="payment-label">
                                    <i class="fab fa-paypal"></i>
                                    <span>PayPal</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer">
                                <div class="payment-label">
                                    <i class="fas fa-university"></i>
                                    <span>Bank Transfer</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash_on_delivery">
                                <div class="payment-label">
                                    <i class="fas fa-money-bill"></i>
                                    <span>Cash on Delivery</span>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    {{-- Terms & Conditions --}}
                    <div class="terms-section">
                        <label class="checkbox">
                            <input type="checkbox" name="agree_terms" required>
                            <span>I agree to the terms and conditions and privacy policy</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="checkout-summary">
                <h2>Order Summary</h2>

                <div class="summary-items">
                    @foreach($cart->items as $item)
                        @php
                            $itemTotal = $item->quantity * ($item->product->sale_price ?? $item->product->price);
                        @endphp
                        <div class="summary-item">
                            <div class="item-info">
                                <img src="{{ $item->product->images->first() ? asset('storage/' . $item->product->images->first()->image_path) : 'https://via.placeholder.com/60x80?text=No+Image' }}" 
                                     alt="{{ $item->product->name }}"
                                     onerror="this.src='https://via.placeholder.com/60x80?text=No+Image'">
                                <div class="item-details">
                                    <div class="item-name">{{ $item->product->name }}</div>
                                    <div class="item-qty">Qty: {{ $item->quantity }}</div>
                                </div>
                            </div>
                            <div class="item-price">₱{{ number_format($itemTotal, 2) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="summary-divider"></div>

                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="total-row">
                        <span>Shipping</span>
                        <span>
                            @if($subtotal >= 50)
                                <span class="free-label">FREE</span>
                            @else
                                ₱{{ number_format($shippingCost, 2) }}
                            @endif
                        </span>
                    </div>

                    <div class="total-row">
                        <span>Tax (10%)</span>
                        <span>₱{{ number_format($tax, 2) }}</span>
                    </div>

                    <div class="total-row grand-total">
                        <span>Total</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="security-info">
                    <i class="fas fa-lock"></i>
                    <span>Your payment information is secure and encrypted.</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const terms = document.querySelector('input[name="agree_terms"]');
            if (!terms.checked) {
                e.preventDefault();
                alert('Please agree to terms and conditions');
                return false;
            }
        });
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }

        .checkout-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 0;
        }

        .checkout-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .checkout-logo {
            font-size: 24px;
            font-weight: 700;
            color: #000;
            text-decoration: none;
        }

        .checkout-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .checkout-form-section {
            background: white;
            border-radius: 8px;
            padding: 30px;
        }

        .checkout-form-section h1 {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .form-section {
            margin-bottom: 30px;
            border: none;
            padding: 0;
        }

        .form-section legend {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            display: block;
            padding: 0;
            width: 100%;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .error {
            color: #ef4444;
            font-size: 12px;
        }

        .payment-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .payment-option {
            position: relative;
        }

        .payment-option input {
            position: absolute;
            opacity: 0;
        }

        .payment-option .payment-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-option input:checked + .payment-label {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .payment-label i {
            font-size: 20px;
            color: #2563eb;
        }

        .payment-label span {
            font-weight: 600;
            color: #333;
        }

        .terms-section {
            margin: 30px 0;
        }

        .checkbox {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }

        .checkbox input {
            margin-top: 2px;
            cursor: pointer;
        }

        .place-order-btn {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .place-order-btn:hover {
            background: #1d4ed8;
        }

        .checkout-summary {
            background: white;
            border-radius: 8px;
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .checkout-summary h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .summary-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .item-info {
            display: flex;
            gap: 12px;
            flex: 1;
        }

        .item-info img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .item-name {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .item-qty {
            font-size: 12px;
            color: #999;
        }

        .item-price {
            font-weight: 600;
            color: #2563eb;
            white-space: nowrap;
        }

        .summary-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        .totals {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
        }

        .total-row.grand-total {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .free-label {
            color: #22c55e;
            font-weight: 600;
        }

        .security-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            font-size: 12px;
            color: #166534;
        }

        .security-info i {
            color: #22c55e;
        }

        @media (max-width: 1024px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }

            .checkout-summary {
                position: static;
            }

            .payment-options {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .checkout-form-section {
                padding: 20px;
            }

            .checkout-form-section h1 {
                font-size: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .payment-options {
                grid-template-columns: repeat(2, 1fr);
            }

            .payment-label {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</body>
</html>

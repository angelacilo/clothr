<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopping Cart - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    {{-- Header --}}
    <header class="cart-header">
        <div class="cart-header-inner">
            <a href="{{ route('home') }}" class="cart-logo">CLOTHR</a>
            <nav class="cart-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}">Shop</a>
            </nav>
            <div class="cart-nav-right">
                @auth
                    <a href="{{ route('account') }}" class="cart-account-btn">Account</a>
                @else
                    <a href="{{ route('login') }}" class="cart-login-btn">Login</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Cart Container --}}
    <div class="cart-container">
        <h1>Shopping Cart</h1>

        @if($cartItems->isEmpty())
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Add some items to get started</p>
                <a href="{{ route('products.index') }}" class="cart-btn">Continue Shopping</a>
            </div>
        @else
            <div class="cart-wrapper">
                {{-- Cart Items --}}
                <div class="cart-items-section">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                @php
                                    $itemTotal = $item->quantity * ($item->product->sale_price ?? $item->product->price);
                                @endphp
                                <tr class="cart-item" data-item-id="{{ $item->cart_item_id }}">
                                    <td>
                                        <div class="item-product">
                                            @if($item->product->images->first())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     onerror="this.src='https://via.placeholder.com/80x100?text=No+Image'">
                                            @else
                                                <img src="https://via.placeholder.com/80x100?text=No+Image">
                                            @endif
                                            <div class="item-info">
                                                <a href="{{ route('products.show', $item->product->product_id) }}" class="item-name">
                                                    {{ $item->product->name }}
                                                </a>
                                                <span class="item-sku">SKU: {{ $item->product->product_id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                            <span class="item-price">${{ number_format($item->product->sale_price, 2) }}</span>
                                        @else
                                            <span class="item-price">${{ number_format($item->product->price, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="quantity-control">
                                            <button onclick="updateQuantity({{ $item->cart_item_id }}, -1)" class="qty-btn">−</button>
                                            <span class="item-quantity">{{ $item->quantity }}</span>
                                            <button onclick="updateQuantity({{ $item->cart_item_id }}, 1)" class="qty-btn">+</button>
                                        </div>
                                    </td>
                                    <td class="item-total">${{ number_format($itemTotal, 2) }}</td>
                                    <td>
                                        <button onclick="removeItem({{ $item->cart_item_id }})" class="remove-btn" title="Remove item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Cart Summary --}}
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span class="summary-value">${{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="summary-item">
                        <span>Shipping</span>
                        <span class="summary-value">
                            @if($subtotal >= 50)
                                <span class="free-shipping">FREE</span>
                            @else
                                ${{ number_format($shippingCost, 2) }}
                            @endif
                        </span>
                    </div>

                    <div class="summary-item">
                        <span>Tax (10%)</span>
                        <span class="summary-value">${{ number_format($tax, 2) }}</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-item total">
                        <span>Total</span>
                        <span class="summary-value">${{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="checkout-btn">Proceed to Checkout</a>

                    <a href="{{ route('products.index') }}" class="continue-shopping">Continue Shopping</a>

                    <div class="summary-notice">
                        @if($subtotal < 50)
                            <p><i class="fas fa-info-circle"></i> Free shipping on orders over $50</p>
                        @else
                            <p><i class="fas fa-check-circle"></i> You qualify for free shipping!</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="cart-actions">
                <button onclick="clearCart()" class="clear-btn">Clear Cart</button>
            </div>
        @endif
    </div>

    {{-- Notification Toast --}}
    <div id="notificationToast" class="notification-toast"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateQuantity(itemId, change) {
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            const currentQty = parseInt(row.querySelector('.item-quantity').textContent);
            const newQty = currentQty + change;

            if (newQty < 1) {
                removeItem(itemId);
                return;
            }

            fetch(`/cart/update/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: newQty })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification('Failed to update cart', 'error');
                }
            })
            .catch(error => {
                showNotification('Failed to update cart', 'error');
            });
        }

        function removeItem(itemId) {
            if (!confirm('Remove this item from cart?')) return;

            fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification('Failed to remove item', 'error');
                }
            })
            .catch(error => {
                showNotification('Failed to remove item', 'error');
            });
        }

        function clearCart() {
            if (!confirm('Clear entire cart?')) return;

            fetch('{{ route("cart.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(() => location.reload())
            .catch(error => {
                showNotification('Failed to clear cart', 'error');
            });
        }

        function showNotification(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            toast.textContent = message;
            toast.className = `notification-toast ${type} show`;
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
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

        .cart-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 0;
        }

        .cart-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            gap: 30px;
        }

        .cart-logo {
            font-size: 24px;
            font-weight: 700;
            color: #000;
            text-decoration: none;
        }

        .cart-nav {
            display: flex;
            gap: 30px;
            flex: 1;
        }

        .cart-nav a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .cart-nav-right {
            display: flex;
            gap: 20px;
        }

        .cart-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .cart-container h1 {
            font-size: 32px;
            margin-bottom: 30px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }

        .empty-cart i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart h2 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #333;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 30px;
        }

        .cart-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
        }

        .cart-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .cart-items-section {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table thead {
            background: #f4f6f9;
            border-bottom: 2px solid #e5e7eb;
        }

        .cart-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .cart-table td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-item:hover {
            background: #f9fafb;
        }

        .item-product {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .item-product img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .item-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .item-name {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .item-sku {
            color: #999;
            font-size: 12px;
        }

        .item-price {
            font-weight: 600;
            color: #333;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            width: fit-content;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: white;
            cursor: pointer;
            color: #666;
            font-size: 16px;
        }

        .qty-btn:hover {
            background: #f4f6f9;
        }

        .item-quantity {
            width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .item-total {
            font-weight: 600;
            color: #2563eb;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            transition: color 0.2s;
        }

        .remove-btn:hover {
            color: #dc2626;
        }

        .cart-summary {
            background: white;
            border-radius: 8px;
            padding: 24px;
            height: fit-content;
        }

        .cart-summary h3 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
            color: #666;
        }

        .summary-item.total {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .summary-value {
            text-align: right;
        }

        .free-shipping {
            color: #22c55e;
            font-weight: 600;
        }

        .summary-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 12px;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.2s;
        }

        .checkout-btn:hover {
            background: #1d4ed8;
        }

        .continue-shopping {
            display: block;
            width: 100%;
            background: white;
            color: #2563eb;
            border: 1px solid #2563eb;
            padding: 12px;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            margin-top: 12px;
            transition: all 0.2s;
        }

        .continue-shopping:hover {
            background: #eff6ff;
        }

        .summary-notice {
            margin-top: 20px;
            padding: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            font-size: 13px;
            color: #166534;
        }

        .summary-notice i {
            margin-right: 6px;
        }

        .cart-actions {
            text-align: center;
        }

        .clear-btn {
            background: white;
            color: #ef4444;
            border: 1px solid #ef4444;
            padding: 12px 30px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .clear-btn:hover {
            background: #fef2f2;
        }

        .notification-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .notification-toast.show {
            opacity: 1;
        }

        .notification-toast.success {
            background: #22c55e;
        }

        .notification-toast.error {
            background: #ef4444;
        }

        @media (max-width: 1024px) {
            .cart-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .cart-table {
                font-size: 12px;
            }

            .cart-table th,
            .cart-table td {
                padding: 12px 8px;
            }

            .item-product img {
                width: 60px;
                height: 80px;
            }

            .item-name {
                font-size: 12px;
            }
        }
    </style>
</body>
</html>

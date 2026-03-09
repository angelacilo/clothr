@extends('layouts.shop')

@section('title', 'Your Cart')

@section('extra_css')
    .cart-layout { display: grid; grid-template-columns: 1fr 380px; gap: 40px; }
    
    .cart-items { border-top: 1px solid var(--border-color); }
    .cart-item { display: grid; grid-template-columns: 120px 1fr 140px; gap: 20px; padding: 30px 0; border-bottom: 1px solid var(--border-color); }
    .cart-item__img { aspect-ratio: 1/1; background: #f4f4f4; border-radius: var(--radius-sm); overflow: hidden; }
    .cart-item__img img { width: 100%; height: 100%; object-fit: cover; }
    
    .cart-item__info h3 { font-size: 16px; font-weight: 700; margin-bottom: 5px; }
    .cart-item__info .details { color: var(--text-muted); font-size: 13px; margin-bottom: 15px; }
    .cart-item__actions { display: flex; gap: 20px; align-items: center; }
    .cart-item__btn { color: var(--text-muted); font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .cart-item__btn:hover { color: #000; }
    
    .cart-item__right { text-align: right; display: flex; flex-direction: column; justify-content: space-between; }
    .cart-item__price { font-weight: 700; font-size: 16px; }
    
    .summary-card { background: #f8f9fa; padding: 30px; border-radius: var(--radius-md); position: sticky; top: 120px; }
    .summary-title { font-size: 18px; font-weight: 700; margin-bottom: 25px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; }
    .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 18px; font-weight: 800; }
    
    .checkout-btn { background: #000; color: #fff; width: 100%; padding: 18px; border-radius: var(--radius-sm); font-weight: 700; margin-top: 30px; }
    .continue-btn { display: block; text-align: center; margin-top: 15px; font-size: 13px; font-weight: 600; text-decoration: underline; }

    .qty-stepper { display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 4px; width: fit-content; background: #fff; }
    .qty-stepper button { padding: 4px 10px; font-weight: 700; }
    .qty-stepper span { width: 30px; text-align: center; font-size: 13px; font-weight: 700; }

    .empty-state { text-align: center; padding: 100px 0; }
    .empty-state h2 { font-size: 24px; margin-bottom: 20px; }
@endsection

@section('content')
<div class="container section">
    <h1 style="font-size: 40px; font-weight: 800; margin-bottom: 40px;">Shopping Cart</h1>

    <div id="cart-content" class="cart-layout" style="display: none;">
        <div class="cart-items" id="cart-items-list">
            <!-- Items injected here -->
        </div>

        <aside>
            <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">₱0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="shipping">₱250.00</span>
                </div>
                <div class="summary-row" id="free-shipping-note" style="color: #388e3c; font-weight: 500; display: none;">
                    <span>Free Shipping Applied</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="total">₱0.00</span>
                </div>

                <a href="{{ route('checkout') }}" class="checkout-btn" style="display: block; text-align: center;">Checkout Now</a>
                <a href="{{ route('shop') }}" class="continue-btn">Continue Shopping</a>
            </div>
        </aside>
    </div>

    <div id="cart-empty" class="empty-state" style="display: none;">
        <h2>Your cart is empty.</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('shop') }}" class="hero__btn" style="background: #000; color: #fff; text-decoration: none;">Start Shopping</a>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function renderCart() {
        const itemsList = document.getElementById('cart-items-list');
        const content = document.getElementById('cart-content');
        const empty = document.getElementById('cart-empty');

        if (cart.length === 0) {
            content.style.display = 'none';
            empty.style.display = 'block';
            return;
        }

        content.style.display = 'grid';
        empty.style.display = 'none';
        itemsList.innerHTML = '';

        let subtotal = 0;

        cart.forEach((item, index) => {
            subtotal += item.price * item.quantity;
            itemsList.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item__img">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item__info">
                        <h3>${item.name}</h3>
                        <div class="details">Size: ${item.size}</div>
                        <div class="cart-item__actions">
                            <div class="qty-stepper">
                                <button onclick="updateItemQty(${index}, -1)">−</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateItemQty(${index}, 1)">+</button>
                            </div>
                            <button class="cart-item__btn" onclick="removeItem(${index})"><i data-lucide="trash-2" size="16"></i> Remove</button>
                            <button class="cart-item__btn"><i data-lucide="heart" size="16"></i> Wishlist</button>
                        </div>
                    </div>
                    <div class="cart-item__right">
                        <div class="cart-item__price">₱${(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                </div>
            `;
        });

        lucide.createIcons();

        const shipping = subtotal >= 2500 ? 0 : 250;
        document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
        document.getElementById('shipping').innerText = shipping === 0 ? '₱0.00' : '₱' + shipping.toFixed(2);
        document.getElementById('free-shipping-note').style.display = shipping === 0 ? 'flex' : 'none';
        document.getElementById('total').innerText = '₱' + (subtotal + shipping).toFixed(2);
    }

    window.updateItemQty = function(index, delta) {
        cart[index].quantity = Math.max(1, cart[index].quantity + delta);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    }

    window.removeItem = function(index) {
        cart.splice(index, 1);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    }

    window.addEventListener('load', renderCart);
</script>
@endsection

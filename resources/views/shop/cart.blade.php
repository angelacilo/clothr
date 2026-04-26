@extends('layouts.shop')

@section('title', 'Your Cart')

@section('extra_css')
    .cart-progress { display: flex; justify-content: center; gap: 20px; margin-bottom: 40px; font-size: 14px; font-weight: 600; color: #999; }
    .cart-progress span.active { color: #000; border-bottom: 2px solid #000; }
    
    .cart-alert { background: #e8f5e9; color: #2e7d32; padding: 12px 20px; border-radius: 4px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 14px; }
    
    .cart-layout { display: grid; grid-template-columns: 1fr 380px; gap: 40px; }
    
    .cart-box { background: #fff; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
    .cart-box-header { padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 15px; font-weight: 800; }
    
    .cart-item { display: grid; grid-template-columns: 40px 100px 1fr 100px; gap: 20px; padding: 20px; border-bottom: 1px solid #f0f0f0; }
    .cart-item:last-child { border-bottom: none; }
    .cart-item__img { aspect-ratio: 3/4; background: #f8f8f8; border-radius: 4px; overflow: hidden; }
    .cart-item__img img { width: 100%; height: 100%; object-fit: cover; }
    
    .cart-item__info h3 { font-size: 14px; font-weight: 600; line-height: 1.4; margin-bottom: 4px; }
    .cart-item__meta { font-size: 12px; color: #666; display: flex; gap: 10px; margin-bottom: 15px; }
    .cart-item__actions { display: flex; align-items: center; gap: 15px; }
    
    .qty-ctrl { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; height: 32px; }
    .qty-ctrl button { width: 32px; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .qty-ctrl span { width: 36px; text-align: center; font-weight: 700; font-size: 13px; }
    
    .cart-item__price { font-weight: 800; font-size: 15px; text-align: right; }
    
    .summary-box { background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; position: sticky; top: 120px; }
    .summary-title { font-size: 18px; font-weight: 800; margin-bottom: 25px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; }
    .summary-total { display: flex; justify-content: space-between; padding-top: 15px; border-top: 1px solid #eee; margin-top: 15px; font-size: 18px; font-weight: 800; }
    
    .checkout-btn { background: #000; color: #fff; width: 100%; padding: 16px; border-radius: 4px; font-weight: 800; margin-top: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .we-accept { margin-top: 30px; }
    .accept-title { font-size: 13px; font-weight: 700; margin-bottom: 15px; color: #333; }
    .accept-icons { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
    .accept-icons img { width: 100%; border: 1px solid #eee; border-radius: 3px; padding: 4px; opacity: 0.8; }
    
    .recommendations { margin-top: 80px; }
    .rec-title { font-size: 24px; font-weight: 800; text-align: center; margin-bottom: 40px; }
    .rec-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
    
    .empty-cart { text-align: center; padding: 100px 20px; }
@endsection

@section('content')
<div class="container section">
    <div class="cart-progress">
        <span class="active">Cart</span>
        <span>></span>
        <span>Place Order</span>
        <span>></span>
        <span>Pay</span>
        <span>></span>
        <span>Order Complete</span>
    </div>

    <div id="cart-content" style="display: none;">
        <div class="cart-layout">
            <div class="cart-left">


                <div class="cart-box">
                    <div class="cart-box-header">
                        <input type="checkbox" id="select-all" style="width: 20px; height: 20px; cursor: pointer;" onchange="toggleSelectAll(this.checked)">
                        <label for="select-all" style="cursor: pointer;">ALL ITEMS (<span id="item-count-badge">0</span>)</label>
                    </div>
                    
                    <div id="cart-items-list">
                        <!-- items -->
                    </div>
                </div>
            </div>

            <div class="cart-right">
                <div class="summary-box">
                    <h3 class="summary-title">Order Summary</h3>
                    <div class="summary-row">
                        <span>Original Price:</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="summary-row" style="color: #d32f2f;">
                        <span>Shipping Fee:</span>
                        <span id="shipping">₱0.00</span>
                    </div>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span id="total">₱0.00</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="checkout-btn" style="display: block; text-align: center;">Checkout Now (<span class="selected-count">0</span>)</a>
                    

                </div>
            </div>
        </div>

        <div class="recommendations">
            <h2 class="rec-title">You Might Like to Fill it With</h2>
            <div class="rec-grid">
                @foreach($recommendations as $product)
                    <div class="product-card" style="box-shadow: none; border: none;">
                        <a href="{{ route('product', $product->id) }}">
                            <div class="product-card__img-box">
                                <img src="{{ $product->images[0] ?? '/placeholder.png' }}" class="product-card__img" alt="{{ $product->name }}">
                            </div>
                        </a>
                        <h3 style="font-size: 13px; margin: 10px 0 5px;">{{ $product->name }}</h3>
                        <p style="font-weight: 800; font-size: 14px;">₱{{ number_format($product->price, 2) }}</p>
                        <button class="btn-sso-black" style="width: 100%; padding: 8px; font-size: 12px; margin-top: 10px;" onclick="addToCartGlobal({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->images[0] ?? '' }}')">Add to Cart</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="cart-empty" class="empty-cart" style="display: none;">
        <i data-lucide="shopping-bag" size="64" style="margin-bottom: 20px; color: #ddd;"></i>
        <h2>Your Bag is Empty</h2>
        <p style="color: #666; margin: 15px 0 30px;">Let's go find something you'll love!</p>
        <a href="{{ route('shop') }}" class="btn-sso-black" style="padding: 16px 40px;">Shop New Arrivals</a>
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

        content.style.display = 'block';
        empty.style.display = 'none';
        itemsList.innerHTML = '';

        let subtotal = 0;
        let selectedCount = 0;
        let allSelected = true;

        cart.forEach((item, index) => {
            if (item.is_selected === undefined) item.is_selected = true;
            if (item.is_selected) {
                subtotal += item.price * item.quantity;
                selectedCount += item.quantity;
            } else {
                allSelected = false;
            }

            itemsList.innerHTML += `
                <div class="cart-item">
                    <div style="padding-top: 5px;">
                        <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;" ${item.is_selected ? 'checked' : ''} onchange="toggleSelectItem(${index}, this.checked)">
                    </div>
                    <div class="cart-item__img">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item__info">
                        <h3>${item.name}</h3>
                        <div class="cart-item__meta">
                            <span>Size: ${item.size}</span>
                            ${item.color ? `<span style="margin-left:8px; display:flex; align-items:center; gap:4px;">Color: <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:${item.color}; border:1px solid #ccc;"></span></span>` : ''}
                        </div>
                        <div class="cart-item__actions">
                            <div class="qty-ctrl">
                                <button onclick="updateItemQty(${index}, -1)">−</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateItemQty(${index}, 1)">+</button>
                            </div>
                            <button onclick="removeItem(${index})" style="color: #999;"><i data-lucide="trash-2" size="18"></i></button>
                            <button onclick="toggleWishlistGlobal(${item.id}, this)" style="color: #999;"><i data-lucide="heart" size="18"></i></button>
                        </div>
                    </div>
                    <div class="cart-item__price">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
            `;
        });

        document.getElementById('select-all').checked = allSelected && cart.length > 0;
        document.getElementById('item-count-badge').innerText = cart.length;
        document.querySelectorAll('.selected-count').forEach(el => el.innerText = selectedCount);
        
        lucide.createIcons();

        const shipping = subtotal >= 2500 ? 0 : (subtotal > 0 ? 250 : 0);
        document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
        document.getElementById('shipping').innerText = shipping === 0 ? 'FREE' : '₱' + shipping.toFixed(2);
        document.getElementById('total').innerText = '₱' + (subtotal + shipping).toFixed(2);
    }

    window.toggleSelectAll = function(checked) {
        cart.forEach(item => item.is_selected = checked);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
    }

    window.toggleSelectItem = function(index, checked) {
        cart[index].is_selected = checked;
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
    }

    window.updateItemQty = function(index, delta) {
        cart[index].quantity = Math.max(1, cart[index].quantity + delta);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
        
        if (isLoggedIn) {
            fetch('/api/cart/update', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify(cart[index])
            });
        }
    }

    window.removeItem = function(index) {
        const item = cart[index];
        if (isLoggedIn) {
            fetch('/api/cart/remove', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: item.id, size: item.size, color: item.color })
            });
        }
        cart.splice(index, 1);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
        showToast('Item removed', 'info');
    }

    window.addEventListener('load', renderCart);
</script>
@endsection

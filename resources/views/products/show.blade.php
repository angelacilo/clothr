<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    {{-- Top bar --}}
    <div class="detail-topbar">
        <span>
            <i class="fas fa-shipping-fast"></i>
            FREE SHIPPING ON ORDERS OVER $50
        </span>
    </div>

    {{-- Header --}}
    <header class="detail-header">
        <div class="detail-header-inner">
            <a href="{{ route('home') }}" class="detail-logo">CLOTHR</a>
            <nav class="detail-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}">Shop</a>
            </nav>
            <div class="detail-nav-right">
                @auth
                    <a href="{{ route('account') }}" class="detail-account-btn">Account</a>
                @else
                    <a href="{{ route('login') }}" class="detail-login-btn">Login</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="detail-cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <div class="breadcrumb-inner">
            <a href="{{ route('home') }}">Home</a>
            <span class="separator">/</span>
            <a href="{{ route('products.index') }}">Shop</a>
            <span class="separator">/</span>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->category_name }}</a>
            <span class="separator">/</span>
            <span>{{ $product->name }}</span>
        </div>
    </div>

    {{-- Product Details --}}
    <div class="product-detail-container">
        <div class="detail-wrapper">
            {{-- Image Gallery --}}
            <div class="product-gallery">
                <div class="main-image">
                    @if($product->images->first())
                        <img id="mainImage" 
                             src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                             alt="{{ $product->name }}"
                             onerror="this.src='https://via.placeholder.com/500x600?text=No+Image'">
                    @else
                        <img id="mainImage" 
                             src="https://via.placeholder.com/500x600?text=No+Image" 
                             alt="{{ $product->name }}">
                    @endif
                </div>
                @if($product->images->count() > 1)
                    <div class="thumbnail-list">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $product->name }}"
                                 class="thumbnail"
                                 onclick="changeImage(this)"
                                 onerror="this.src='https://via.placeholder.com/100x120?text=No+Image'">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="product-details">
                <div class="detail-category">
                    <a href="{{ route('products.index', ['category' => $product->category_id]) }}">
                        {{ $product->category->category_name }}
                    </a>
                </div>

                <h1 class="detail-name">{{ $product->name }}</h1>

                <div class="detail-rating">
                    @php
                        $avgRating = $product->reviews->avg('rating') ?? 0;
                        $ratingCount = $product->reviews->count();
                    @endphp
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star @if($i <= $avgRating) filled @endif"></i>
                        @endfor
                    </div>
                    <span class="rating-count">{{ number_format($avgRating, 1) }} ({{ $ratingCount }} reviews)</span>
                </div>

                <div class="detail-price">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="sale-price">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="original-price">${{ number_format($product->price, 2) }}</span>
                        @php
                            $discount = round(((($product->price - $product->sale_price) / $product->price) * 100), 0);
                        @endphp
                        <span class="discount-badge">Save {{ $discount }}%</span>
                    @else
                        <span class="price">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <div class="detail-description">
                    <p>{{ $product->description }}</p>
                </div>

                <div class="detail-stock">
                    @php
                        $stock = $product->inventory ? $product->inventory->available_qty : 0;
                    @endphp
                    @if($stock > 0)
                        <span class="in-stock"><i class="fas fa-check"></i> In Stock ({{ $stock }} available)</span>
                    @else
                        <span class="out-of-stock"><i class="fas fa-times"></i> Out of Stock</span>
                    @endif
                </div>

                @auth
                    @if($stock > 0)
                        <div class="detail-actions">
                            <div class="quantity-selector">
                                <button onclick="decreaseQuantity()" class="qty-btn">−</button>
                                <input type="number" id="quantity" value="1" min="1" max="{{ $stock }}">
                                <button onclick="increaseQuantity()" class="qty-btn">+</button>
                            </div>
                            <button class="add-to-cart-btn" onclick="addToCart({{ $product->product_id }})">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                        <button class="wishlist-btn" onclick="addToWishlist({{ $product->product_id }})">
                            <i class="far fa-heart"></i> Add to Wishlist
                        </button>
                    @endif
                @else
                    <div class="login-prompt">
                        <p>Please <a href="{{ route('login') }}">log in</a> to purchase this product</p>
                    </div>
                @endauth

                <div class="detail-features">
                    <h3>Features</h3>
                    <ul>
                        <li>High-quality material</li>
                        <li>Comfortable fit</li>
                        <li>Stylish design</li>
                        <li>Easy care</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="reviews-section">
            <h2>Customer Reviews</h2>
            
            @if($product->reviews->count() > 0)
                <div class="reviews-list">
                    @foreach($product->reviews->sortByDesc('created_at') as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-name">{{ $review->user->name }}</div>
                                    <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star @if($i <= $review->rating) filled @endif"></i>
                                    @endfor
                                    <span>({{ $review->rating }}/5)</span>
                                </div>
                            </div>
                            <div class="review-content">
                                <p>{{ $review->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-reviews">
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            @endif
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->count() > 0)
            <div class="related-section">
                <h2>Related Products</h2>
                <div class="related-grid">
                    @foreach($relatedProducts as $related)
                        <div class="related-card">
                            <a href="{{ route('products.show', $related->product_id) }}" class="related-link">
                                <div class="related-image">
                                    @if($related->images->first())
                                        <img src="{{ asset('storage/' . $related->images->first()->image_path) }}" 
                                             alt="{{ $related->name }}"
                                             onerror="this.src='https://via.placeholder.com/250x300?text=No+Image'">
                                    @else
                                        <img src="https://via.placeholder.com/250x300?text=No+Image">
                                    @endif
                                </div>
                                <div class="related-info">
                                    <div class="related-name">{{ $related->name }}</div>
                                    <div class="related-price">
                                        @if($related->sale_price && $related->sale_price < $related->price)
                                            <span class="price">${{ number_format($related->sale_price, 2) }}</span>
                                        @else
                                            <span class="price">${{ number_format($related->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Notification Toast --}}
    <div id="notificationToast" class="notification-toast"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const maxStock = {{ $stock ?? 0 }};

        function changeImage(img) {
            document.getElementById('mainImage').src = img.src;
        }

        function increaseQuantity() {
            const qty = document.getElementById('quantity');
            if (parseInt(qty.value) < maxStock) {
                qty.value = parseInt(qty.value) + 1;
            }
        }

        function decreaseQuantity() {
            const qty = document.getElementById('quantity');
            if (parseInt(qty.value) > 1) {
                qty.value = parseInt(qty.value) - 1;
            }
        }

        function addToCart(productId) {
            const quantity = parseInt(document.getElementById('quantity').value);
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount();
                    document.getElementById('quantity').value = 1;
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Failed to add to cart', 'error');
            });
        }

        function addToWishlist(productId) {
            showNotification('Added to wishlist!', 'success');
            // TODO: Implement wishlist functionality
        }

        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cartCount').textContent = data.count;
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

        // Update cart count on page load
        updateCartCount();
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

        .detail-topbar {
            background-color: #f4f6f9;
            padding: 8px 0;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .detail-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 0;
        }

        .detail-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            gap: 30px;
        }

        .detail-logo {
            font-size: 24px;
            font-weight: 700;
            color: #000;
            text-decoration: none;
            min-width: 80px;
        }

        .detail-nav {
            display: flex;
            gap: 30px;
            flex: 1;
        }

        .detail-nav a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .detail-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .detail-account-btn, .detail-login-btn {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        .detail-cart-btn {
            position: relative;
            color: #666;
            cursor: pointer;
            text-decoration: none;
            font-size: 18px;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            font-size: 11px;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .breadcrumb {
            background: white;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .breadcrumb-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            font-size: 13px;
            color: #999;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .separator {
            margin: 0 8px;
        }

        .product-detail-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .detail-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .product-gallery {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .main-image {
            width: 100%;
            height: 500px;
            background: #f4f6f9;
            border-radius: 8px;
            overflow: hidden;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-list {
            display: flex;
            gap: 10px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            background: #f4f6f9;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            transition: border-color 0.2s;
        }

        .thumbnail:hover {
            border-color: #2563eb;
        }

        .product-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .detail-category {
            font-size: 13px;
        }

        .detail-category a {
            color: #2563eb;
            text-decoration: none;
        }

        .detail-name {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
        }

        .detail-rating {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stars i {
            color: #fbbf24;
            font-size: 16px;
        }

        .rating-count {
            color: #999;
            font-size: 14px;
        }

        .detail-price {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 28px;
        }

        .sale-price {
            color: #ef4444;
            font-weight: 700;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            font-size: 20px;
        }

        .price {
            color: #333;
            font-weight: 700;
        }

        .discount-badge {
            background: #fef3c7;
            color: #d97706;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .detail-description {
            color: #666;
            line-height: 1.6;
        }

        .detail-stock {
            font-size: 14px;
        }

        .in-stock {
            color: #22c55e;
        }

        .out-of-stock {
            color: #ef4444;
        }

        .detail-actions {
            display: flex;
            gap: 12px;
        }

        .quantity-selector {
            display: flex;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .qty-btn {
            width: 40px;
            padding: 10px;
            border: none;
            background: white;
            cursor: pointer;
            font-size: 16px;
            color: #666;
        }

        .qty-btn:hover {
            background: #f4f6f9;
        }

        #quantity {
            flex: 1;
            border: none;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            width: 50px;
        }

        #quantity::-webkit-outer-spin-button,
        #quantity::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .add-to-cart-btn {
            flex: 1;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .add-to-cart-btn:hover {
            background: #1d4ed8;
        }

        .wishlist-btn {
            width: 100%;
            background: white;
            color: #ef4444;
            border: 1px solid #ef4444;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .wishlist-btn:hover {
            background: #fef2f2;
        }

        .login-prompt {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 16px;
            border-radius: 4px;
            color: #2563eb;
        }

        .login-prompt a {
            font-weight: 600;
        }

        .detail-features {
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .detail-features h3 {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .detail-features ul {
            list-style: none;
            padding-left: 16px;
        }

        .detail-features li {
            color: #666;
            padding: 6px 0;
            position: relative;
        }

        .detail-features li:before {
            content: "✓";
            position: absolute;
            left: -16px;
            color: #22c55e;
        }

        .reviews-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .reviews-section h2 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .review-item {
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 4px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
        }

        .review-date {
            font-size: 12px;
            color: #999;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .review-rating i {
            color: #fbbf24;
        }

        .review-content {
            color: #666;
            line-height: 1.6;
        }

        .no-reviews {
            background: #f4f6f9;
            padding: 30px;
            text-align: center;
            color: #999;
            border-radius: 4px;
        }

        .related-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
        }

        .related-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .related-link {
            text-decoration: none;
            color: inherit;
        }

        .related-image {
            width: 100%;
            height: 250px;
            background: #f4f6f9;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .related-name {
            font-weight: 600;
            color: #333;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .related-price .price {
            color: #2563eb;
            font-weight: 700;
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

        @media (max-width: 768px) {
            .detail-wrapper {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .main-image {
                height: 400px;
            }

            .detail-name {
                font-size: 24px;
            }

            .detail-price {
                font-size: 24px;
            }

            .related-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html>

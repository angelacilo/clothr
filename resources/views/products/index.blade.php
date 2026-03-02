<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shop - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    {{-- Top bar --}}
    <div class="shop-topbar">
        <span>
            <i class="fas fa-shipping-fast"></i>
            FREE SHIPPING ON ORDERS OVER $50
        </span>
    </div>

    {{-- Header --}}
    <header class="shop-header">
        <div class="shop-header-inner">
            <a href="{{ route('home') }}" class="shop-logo">CLOTHR</a>
            <nav class="shop-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}" class="active">Shop</a>
            </nav>
            <div class="shop-nav-right">
                <div class="shop-search-bar">
                    <form action="{{ route('products.index') }}" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                @auth
                    <a href="{{ route('account') }}" class="shop-account-btn">Account</a>
                @else
                    <a href="{{ route('login') }}" class="shop-login-btn">Login</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="shop-cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Shop Container --}}
    <div class="shop-container">
        {{-- Sidebar --}}
        <aside class="shop-sidebar">
            <div class="sidebar-section">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li>
                        <a href="{{ route('products.index') }}" @if(!request('category')) class="active" @endif>
                            All Products
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('products.index', ['category' => $category->category_id]) }}" 
                               @if(request('category') == $category->category_id) class="active" @endif>
                                {{ $category->category_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-section">
                <h3>Sort By</h3>
                <div class="sort-options">
                    <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" 
                       class="sort-link @if(request('sort', 'newest') == 'newest') active @endif">
                        Newest
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price_low'])) }}" 
                       class="sort-link @if(request('sort') == 'price_low') active @endif">
                        Price: Low to High
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price_high'])) }}" 
                       class="sort-link @if(request('sort') == 'price_high') active @endif">
                        Price: High to Low
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'popular'])) }}" 
                       class="sort-link @if(request('sort') == 'popular') active @endif">
                        Most Popular
                    </a>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="shop-main">
            <div class="shop-header-section">
                <h1>{{ request('search') ? 'Search Results: ' . request('search') : 'Shop All Products' }}</h1>
                <p class="result-count">{{ $products->total() }} products found</p>
            </div>

            @if($products->isEmpty())
                <div class="no-products">
                    <i class="fas fa-inbox"></i>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your filters or search terms</p>
                    <a href="{{ route('products.index') }}" class="shop-btn">View All Products</a>
                </div>
            @else
                <div class="products-grid">
                    @foreach($products as $product)
                        <div class="product-card">
                            <div class="product-image">
                                @if($product->images->first())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                         alt="{{ $product->name }}"
                                         onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                                @else
                                    <img src="https://via.placeholder.com/300x400?text=No+Image" 
                                         alt="{{ $product->name }}">
                                @endif
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <span class="product-badge sale">Sale</span>
                                @endif
                                @if($product->is_new)
                                    <span class="product-badge new">New</span>
                                @endif
                            </div>
                            <div class="product-info">
                                <a href="{{ route('products.show', $product->product_id) }}" class="product-name">
                                    {{ $product->name }}
                                </a>
                                <div class="product-rating">
                                    @php
                                        $avgRating = $product->reviews->avg('rating') ?? 0;
                                        $ratingCount = $product->reviews->count();
                                    @endphp
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star @if($i <= $avgRating) filled @endif"></i>
                                        @endfor
                                    </div>
                                    <span class="rating-count">({{ $ratingCount }})</span>
                                </div>
                                <div class="product-price">
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="sale-price">${{ number_format($product->sale_price, 2) }}</span>
                                        <span class="original-price">${{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="price">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->product_id) }}" class="shop-btn-secondary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button class="shop-btn add-to-cart-btn" onclick="addToCart({{ $product->product_id }})">
                                        <i class="fas fa-shopping-cart"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="pagination-container">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>

    {{-- Notification Toast --}}
    <div id="notificationToast" class="notification-toast"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function addToCart(productId) {
            const quantity = prompt('Enter quantity:', '1');
            if (quantity === null || isNaN(quantity) || quantity < 1) return;

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Failed to add to cart', 'error');
            });
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
        @auth
            updateCartCount();
        @endauth
    </script>

    <style>
        .shop-topbar {
            background-color: #f4f6f9;
            padding: 8px 0;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .shop-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 0;
            sticky top: 0;
            z-index: 100;
        }

        .shop-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            gap: 30px;
        }

        .shop-logo {
            font-size: 24px;
            font-weight: 700;
            color: #000;
            text-decoration: none;
            min-width: 80px;
        }

        .shop-nav {
            display: flex;
            gap: 30px;
            flex: 1;
        }

        .shop-nav a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .shop-nav a.active {
            color: #2563eb;
            font-weight: 600;
        }

        .shop-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .shop-search-bar {
            flex: 1;
            min-width: 250px;
        }

        .search-form {
            display: flex;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .search-form input {
            flex: 1;
            border: none;
            padding: 8px 12px;
            font-size: 14px;
        }

        .search-form button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
        }

        .shop-account-btn, .shop-login-btn {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        .shop-cart-btn {
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
            min-width: 18px;
            text-align: center;
        }

        .shop-container {
            max-width: 1400px;
            margin: 30px auto;
            display: flex;
            gap: 30px;
            padding: 0 20px;
        }

        .shop-sidebar {
            width: 220px;
            flex-shrink: 0;
        }

        .sidebar-section {
            margin-bottom: 30px;
        }

        .sidebar-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .category-list {
            list-style: none;
            padding: 0;
        }

        .category-list li {
            margin-bottom: 10px;
        }

        .category-list a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: block;
            padding: 8px 0;
            border-left: 3px solid transparent;
            padding-left: 12px;
            transition: all 0.2s;
        }

        .category-list a.active {
            color: #2563eb;
            border-left-color: #2563eb;
            font-weight: 600;
        }

        .sort-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sort-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 0;
            border-left: 3px solid transparent;
            padding-left: 12px;
            transition: all 0.2s;
            display: block;
        }

        .sort-link.active {
            color: #2563eb;
            border-left-color: #2563eb;
            font-weight: 600;
        }

        .shop-main {
            flex: 1;
        }

        .shop-header-section {
            margin-bottom: 30px;
        }

        .shop-header-section h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .result-count {
            color: #999;
            font-size: 14px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            position: relative;
            width: 100%;
            height: 320px;
            overflow: hidden;
            background: #f4f6f9;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .product-badge.sale {
            background: #ef4444;
            color: white;
        }

        .product-badge.new {
            background: #22c55e;
            color: white;
        }

        .product-info {
            padding: 16px;
        }

        .product-name {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .stars i {
            color: #fbbf24;
            font-size: 12px;
        }

        .rating-count {
            color: #999;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .sale-price {
            color: #ef4444;
            font-weight: 700;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            font-size: 14px;
        }

        .price {
            color: #333;
            font-weight: 700;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .shop-btn, .shop-btn-secondary {
            flex: 1;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .shop-btn {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .shop-btn:hover {
            background: #1d4ed8;
        }

        .shop-btn-secondary {
            background: white;
            color: #2563eb;
            border-color: #2563eb;
        }

        .shop-btn-secondary:hover {
            background: #eff6ff;
        }

        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-products i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            padding: 30px 0;
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
            .shop-container {
                flex-direction: column;
            }

            .shop-sidebar {
                width: 100%;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .shop-header-inner {
                flex-direction: column;
                gap: 15px;
            }

            .shop-nav {
                flex-direction: column;
                gap: 10px;
            }

            .shop-search-bar {
                min-width: auto;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</body>
</html>

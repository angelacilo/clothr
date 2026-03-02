<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLOTHR - Modern Women's Fashion</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    @if (session('success'))
        <div class="home-success-banner">{{ session('success') }}</div>
    @endif
    {{-- Top bar --}}
    <div class="home-topbar">
        <span>
            <svg class="bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            FREE SHIPPING ON ORDERS OVER $50
        </span>
    </div>

    {{-- Header --}}
    <header class="home-header">
        <div class="home-header-inner">
            <a href="{{ route('home') }}" class="home-logo">CLOTHR</a>
            <nav class="home-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}">Shop All</a>
                <a href="{{ route('products.index', ['category' => 1]) }}">Dresses</a>
                <a href="{{ route('products.index', ['category' => 2]) }}">Tops & Blouses</a>
                <a href="{{ route('products.index', ['category' => 3]) }}">Bottoms</a>
            </nav>
            <div class="home-nav-right">
                <button type="button" class="icon-btn" aria-label="Search">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
                @auth
                    <a href="{{ route('account') }}">Account</a>
                    <form action="{{ route('logout') }}" method="POST" class="home-logout-form">
                        @csrf
                        <button type="submit" class="home-logout-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="icon-btn" aria-label="Cart" style="position: relative; text-decoration: none; color: inherit;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span id="cartBadge" style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; display: none;">0</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="home-hero">
        <div class="home-hero-bg"></div>
        <div class="home-hero-overlay"></div>
        <div class="home-hero-content">
            <h1>New Season Styles</h1>
            <p>Discover the latest trends in fashion. Shop our curated collection of must-have pieces.</p>
            <a href="{{ route('products.index') }}" class="home-btn">Shop Now <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        </div>
    </section>

    {{-- Shop by Category --}}
    <section class="home-categories">
        <h2 class="home-section-title">Shop by Category</h2>
        <div class="home-category-grid">
            <div class="home-category-card">Dresses</div>
            <div class="home-category-card">Tops & Blouses</div>
            <div class="home-category-card">Bottoms</div>
            <div class="home-category-card">Outerwear</div>
            <div class="home-category-card">Accessories</div>
        </div>
    </section>

    {{-- New Arrivals --}}
    <section class="home-arrivals">
        <div class="home-arrivals-header">
            <h2>New Arrivals</h2>
            <a href="{{ route('products.index') }}">View All</a>
        </div>
        <div class="home-product-grid">
            <div class="home-product-card">
                <div class="home-product-image">
                    <img src="https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400&q=80" alt="High-Waist Denim Jeans">
                </div>
                <div class="home-product-name">High-Waist Denim Jeans</div>
                <div class="home-product-price">$64.99</div>
            </div>
            <div class="home-product-card">
                <div class="home-product-image">
                    <img src="https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&q=80" alt="Tailored Trousers">
                </div>
                <div class="home-product-name">Tailored Trousers</div>
                <div class="home-product-price">$54.99</div>
            </div>
            <div class="home-product-card">
                <div class="home-product-image">
                    <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=400&q=80" alt="Cozy Knit Sweater">
                </div>
                <div class="home-product-name">Cozy Knit Sweater</div>
                <div class="home-product-price">$69.99</div>
            </div>
            <div class="home-product-card">
                <div class="home-product-image">
                    <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&q=80" alt="Designer Handbag">
                </div>
                <div class="home-product-name">Designer Handbag</div>
                <div class="home-product-price">$129.90</div>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="home-newsletter">
        <div class="home-newsletter-inner">
            <h2>Stay in the Loop</h2>
            <p>Get the latest updates on new arrivals, exclusive offers, and fashion tips.</p>
            <form id="home-newsletter-form" class="home-newsletter-form">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
            <div id="home-newsletter-message" class="home-newsletter-message"></div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="home-footer">
        <div class="home-footer-inner">
            <div class="home-footer-brand">
                <h3>CLOTHR</h3>
                <p>Your destination for modern women's fashion. Curated collections that celebrate style and individuality.</p>
                <div class="home-footer-social">
                    <span aria-label="Facebook">f</span>
                    <span aria-label="Instagram">📷</span>
                    <span aria-label="Twitter">🐦</span>
                </div>
            </div>
            <div class="home-footer-column">
                <h4>Shop</h4>
                <ul>
                    <li><a href="{{ route('products.index') }}" style="color: inherit; text-decoration: none;">All Products</a></li>
                    <li><a href="{{ route('products.index', ['category' => 1]) }}" style="color: inherit; text-decoration: none;">Dresses</a></li>
                    <li><a href="{{ route('products.index', ['category' => 2]) }}" style="color: inherit; text-decoration: none;">Tops</a></li>
                    <li><a href="{{ route('products.index', ['category' => 3]) }}" style="color: inherit; text-decoration: none;">Bottoms</a></li>
                </ul>
            </div>
            <div class="home-footer-column">
                <h4>Customer Service</h4>
                <ul>
                    <li><span>Contact Us</span></li>
                    <li><span>Shipping Info</span></li>
                    <li><span>Returns</span></li>
                    <li><span>FAQ</span></li>
                </ul>
            </div>
            <div class="home-footer-column">
                <h4>About</h4>
                <ul>
                    <li><span>About Us</span></li>
                    <li><span>Privacy Policy</span></li>
                    <li><span>Terms of Service</span></li>
                </ul>
            </div>
        </div>
        <p class="home-footer-copy">© 2026 CLOTHR. All rights reserved.</p>
    </footer>

    <script src="{{ asset('js/home.js') }}"></script>
    <script>
        // Update cart badge
        @auth
        function updateCartBadge() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cartBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }
        
        // Update on page load
        updateCartBadge();
        
        // Update every 5 seconds
        setInterval(updateCartBadge, 5000);
        @endauth
    </script>
</body>
</html>

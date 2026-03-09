<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTHR | @yield('title', "Modern Women's Fashion")</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #1a1a1a;
            --text-secondary: #555555;
            --text-muted: #888888;
            --accent-color: #000000;
            --accent-hover: #333333;
            --border-color: #e5e5e5;
            --error: #d32f2f;
            --success: #388e3c;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.12);
            --radius-sm: 4px;
            --radius-md: 8px;
            --container-width: 1280px;
            --php-blue: #3b82f6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: var(--text-primary); background: var(--bg-primary); line-height: 1.5; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; transition: 0.2s; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; transition: 0.2s; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }
        .container { max-width: var(--container-width); margin: 0 auto; padding: 0 24px; }

        /* Announcement Bar */
        .announcement-bar { background: #000; color: #fff; text-align: center; padding: 8px; font-size: 13px; font-weight: 500; letter-spacing: 0.05em; }

        /* Navbar */
        .navbar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid var(--border-color); padding: 18px 0; }
        .navbar__inner { display: flex; align-items: center; justify-content: space-between; }
        .navbar__logo { font-size: 24px; font-weight: 800; letter-spacing: -0.05em; }
        .navbar__links { display: flex; gap: 32px; font-size: 14px; font-weight: 500; }
        .navbar__actions { display: flex; align-items: center; gap: 20px; }
        .navbar__icon-btn { display: flex; position: relative; }
        .navbar__cart-badge { position: absolute; top: -8px; right: -10px; background: #000; color: #fff; font-size: 10px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .navbar__login-link { font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 6px; }

        /* Generic Layout Helpers */
        .section { padding: 80px 0; }
        .section-title { font-size: 32px; font-weight: 700; margin-bottom: 40px; }

        /* Footer */
        .footer { background: #fff; border-top: 1px solid var(--border-color); padding: 60px 0 30px; margin-top: 80px; }
        .footer__grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
        .footer__col h4 { margin-bottom: 20px; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; }
        .footer__col p { font-size: 14px; color: var(--text-secondary); margin-bottom: 20px; }
        .footer__links li { margin-bottom: 10px; font-size: 14px; color: var(--text-secondary); }
        .footer__socials { display: flex; gap: 15px; }
        .footer__bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center; font-size: 13px; color: var(--text-muted); }

        /* Product Cards */
        .products__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
        .product-card { transition: 0.3s; position: relative; }
        .product-card:hover { transform: translateY(-5px); }
        .product-card__img-box { position: relative; aspect-ratio: 1/1; background: #f4f4f4; border-radius: var(--radius-md); overflow: hidden; margin-bottom: 15px; }
        .product-card__img { width: 100%; height: 100%; object-fit: cover; }
        .product-card__add { position: absolute; bottom: -50px; left: 0; right: 0; background: #000; color: #fff; padding: 12px; font-size: 13px; font-weight: 600; text-align: center; transition: 0.3s; }
        .product-card:hover .product-card__add { bottom: 0; }
        .product-card h3 { font-size: 15px; font-weight: 600; margin-bottom: 5px; }
        .product-card .price { font-weight: 700; color: var(--text-primary); }
        .product-card .old-price { font-weight: 400; color: var(--text-muted); text-decoration: line-through; margin-left: 8px; font-size: 14px; }
        .product-card .sale-price { color: #2563eb; }
        .product-badge { position: absolute; top: 12px; left: 12px; background: #000; color: #fff; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 40px; text-transform: uppercase; z-index: 2; }

        @media (max-width: 1024px) {
            .products__grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            .products__grid { grid-template-columns: repeat(2, 1fr); }
            .footer__grid { grid-template-columns: 1fr; }
        }

        @yield('extra_css')

        /* ── MODAL OVERLAY ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show {
            display: flex;
        }

        /* ── LOGIN MODAL BOX ── */
        .login-modal {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            width: 400px;
            max-width: 90vw;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlide 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalSlide {
            from { transform: translateY(-20px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .modal-title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #9ca3af;
            line-height: 1;
            padding: 4px;
        }
        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* ── SSO BUTTONS ── */
        .btn-sso-outline {
            display: block;
            width: 100%;
            padding: 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #1a1a1a;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-sso-outline:hover { background: #f9fafb; border-color: #d1d5db; }

        .btn-sso-black {
            width: 100%;
            padding: 16px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-sso-black:hover { background: #1a1a1a; }

        .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #374151; }
        .form-input { width: 100%; padding: 12px 16px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .form-input:focus { border-color: var(--php-blue); }
        .btn-blue { background: var(--php-blue); color: #fff; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; margin-top: 10px; }
        .btn-blue:hover { background: #2563eb; }
        .form-row-between { display: flex; justify-content: space-between; align-items: center; margin: 15px 0; font-size: 13px; }
    </style>
</head>
<body>
    <div class="announcement-bar">
        🚚 FREE SHIPPING ON ORDERS OVER ₱2,500
    </div>

    <nav class="navbar">
        <div class="container navbar__inner">
            <a href="{{ route('home') }}" class="navbar__logo">CLOTHR</a>
            <ul class="navbar__links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop') }}">Shop All</a></li>
                <li><a href="{{ route('category', 'dresses') }}">Dresses</a></li>
                <li><a href="{{ route('category', 'tops-blouses') }}">Tops & Blouses</a></li>
                <li><a href="{{ route('category', 'bottoms') }}">Bottoms</a></li>
            </ul>
            <div class="navbar__actions">
                <button class="navbar__icon-btn"><i data-lucide="search" size="22"></i></button>
                <div id="auth-nav">
                    @auth
                        <div style="display: flex; align-items: center; gap: 15px;">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" style="font-size: 13px; font-weight: 700; color: #f59e0b; border: 1px solid #f59e0b; padding: 4px 10px; border-radius: 4px;">Admin Dash</a>
                            @else
                                <span style="font-size: 14px; font-weight: 500;">Hello, {{ explode(' ', Auth::user()->name)[0] }}</span>
                            @endif
                            <a href="{{ route('logout') }}" class="navbar__login-link" style="color: var(--text-muted); font-size: 13px;">Logout</a>
                        </div>
                    @else
                        <button id="openLoginModal" class="navbar__login-link"><i data-lucide="user" size="22"></i> Login</button>
                    @endauth
                </div>
                <a href="{{ route('cart') }}" class="navbar__icon-btn">
                    <i data-lucide="shopping-bag" size="22"></i>
                    <span class="navbar__cart-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- LOGIN MODAL OVERLAY -->
    <div class="modal-overlay" id="loginModalOverlay">
        <!-- STEP 1: SSO CHOOSER -->
        <div class="login-modal" id="ssoModal">
            <div class="modal-header">
                <span class="modal-title">LOG IN</span>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <a href="{{ route('login') }}" class="btn-sso-outline">
                    Sign In / Register
                </a>
                <button class="btn-sso-black" id="showAdminLogin">
                    Admin log in
                </button>
            </div>
        </div>

        <!-- STEP 2: ADMIN LOGIN FORM -->
        <div class="login-modal" id="adminModal" style="display:none;">
            <div class="modal-header">
                <span class="modal-title">ADMIN LOG IN</span>
                <button class="modal-close" id="closeAdminModal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="admin">
                    <div style="margin-bottom: 16px;">
                        <label class="form-label">User ID</label>
                        <input type="email" name="email" class="form-input" placeholder="admin@clothr.com" required>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <div class="form-row-between">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="{{ route('password.request') }}" style="color: var(--php-blue);">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn-blue">Log in</button>
                    <button type="button" class="btn-sso-black" style="margin-top: 10px;" id="backToSSO">Back to SSO</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container footer__grid">
            <div class="footer__col">
                <h3 style="font-size: 24px; font-weight: 800; margin-bottom: 15px;">CLOTHR</h3>
                <p>Your destination for modern women's fashion. Curated collections that celebrate style and individuality.</p>
                <div class="footer__socials">
                    <i data-lucide="facebook"></i>
                    <i data-lucide="instagram"></i>
                    <i data-lucide="twitter"></i>
                </div>
            </div>
            <div class="footer__col">
                <h4>Shop</h4>
                <ul class="footer__links">
                    <li><a href="{{ route('shop') }}">All Products</a></li>
                    <li><a href="{{ route('category', 'dresses') }}">Dresses</a></li>
                    <li><a href="{{ route('category', 'tops-blouses') }}">Tops</a></li>
                    <li><a href="{{ route('category', 'bottoms') }}">Bottoms</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>Customer Service</h4>
                <ul class="footer__links">
                    <li>Contact Us</li>
                    <li>Shipping Info</li>
                    <li>Returns</li>
                    <li>FAQ</li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>About</h4>
                <ul class="footer__links">
                    <li>About Us</li>
                    <li>Privacy Policy</li>
                    <li>Terms of Service</li>
                </ul>
            </div>
        </div>
        <div class="footer__bottom">
            <p>© 2026 CLOTHR. All rights reserved.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // Cart Logic
        let cart = JSON.parse(localStorage.getItem('clothr_cart') || '[]');
        updateCartCount();

        function updateCartCount() {
            const count = cart.reduce((acc, item) => acc + item.quantity, 0);
            document.getElementById('cart-count').innerText = count;
        }

        function addToCart(product) {
            const existing = cart.find(item => item.id === product.id && item.size === product.size);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({...product, quantity: 1});
            }
            localStorage.setItem('clothr_cart', JSON.stringify(cart));
            updateCartCount();
            alert(product.name + ' added to cart!');
        }

        window.addToCartGlobal = function(id, name, price, image, size = 'M') {
            addToCart({id, name, price, image, size});
        }

        // Modal Logic
        const overlay = document.getElementById('loginModalOverlay');
        const ssoModal = document.getElementById('ssoModal');
        const adminModal = document.getElementById('adminModal');
        const openBtn = document.getElementById('openLoginModal');
        const closeBtn = document.getElementById('closeModal');
        const closeAdmin = document.getElementById('closeAdminModal');
        const showAdmin = document.getElementById('showAdminLogin');
        const backToSSO = document.getElementById('backToSSO');

        openBtn?.addEventListener('click', () => {
            overlay.classList.add('show');
            ssoModal.style.display = 'block';
            adminModal.style.display = 'none';
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        };

        closeBtn?.addEventListener('click', closeModal);
        closeAdmin?.addEventListener('click', closeModal);
        
        overlay?.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        showAdmin?.addEventListener('click', () => {
            ssoModal.style.display = 'none';
            adminModal.style.display = 'block';
        });

        backToSSO?.addEventListener('click', () => {
            adminModal.style.display = 'none';
            ssoModal.style.display = 'block';
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
    @yield('extra_js')
</body>
</html>

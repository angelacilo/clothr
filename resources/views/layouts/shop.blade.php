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
        .section { padding: 100px 0; }
        .section-title { font-size: 36px; font-weight: 800; margin-bottom: 60px; letter-spacing: -0.02em; text-align: center; }

        /* Footer */
        .footer { background: #fff; border-top: 1px solid var(--border-color); padding: 80px 0 40px; margin-top: 100px; }
        .footer__grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 60px; }
        .footer__col h4 { margin-bottom: 25px; font-size: 15px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; }
        .footer__col p { font-size: 15px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6; }
        .footer__links li { margin-bottom: 12px; font-size: 15px; color: var(--text-secondary); }
        .footer__socials { display: flex; gap: 20px; }
        .footer__bottom { margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--border-color); text-align: center; font-size: 14px; color: var(--text-muted); }

        /* Product Cards */
        .products__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; row-gap: 60px; }
        .product-card { transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative; }
        .product-card:hover { transform: translateY(-8px); }
        .product-card__img-box { position: relative; aspect-ratio: 4/5; background: #f8f9fa; border-radius: 12px; overflow: hidden; margin-bottom: 20px; box-shadow: var(--shadow-sm); }
        .product-card__img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); }
        .product-card:hover .product-card__img { transform: scale(1.05); }
        .product-card__add { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(5px); color: #fff; padding: 16px; font-size: 14px; font-weight: 700; text-align: center; transition: 0.3s; z-index: 3; }
        .product-card:hover .product-card__add { background: #000; }
        .product-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #111; }
        .product-card .price { font-weight: 800; color: #000; font-size: 15px; }
        .product-card .old-price { font-weight: 400; color: var(--text-muted); text-decoration: line-through; margin-left: 10px; font-size: 14px; }
        .product-card .sale-price { color: #2563eb; }
        .product-badge { position: absolute; top: 12px; left: 12px; background: #000; color: #fff; padding: 6px 12px; font-size: 10px; font-weight: 800; border-radius: 4px; text-transform: uppercase; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .product-card__wishlist { position: absolute; top: 12px; right: 12px; background: #fff; color: #000; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 2; transition: 0.3s; }
        .product-card__wishlist:hover { transform: scale(1.1); color: #ef4444; background: #fff; }
        .product-card__wishlist.active { color: #ef4444; }

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

        /* TOAST NOTIFICATIONS */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 10000; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: #000; color: #fff; padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px; animation: toastIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; }
        @keyframes toastIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast.removing { animation: toastOut 0.3s forwards; }
        @keyframes toastOut { to { transform: translateX(100%); opacity: 0; } }
        .toast-success { border-left: 4px solid #10b981; }
        .toast-error { border-left: 4px solid #ef4444; }
        .toast-info { border-left: 4px solid var(--php-blue); }
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
                                <a href="{{ route('profile') }}" style="font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                                    <i data-lucide="user" size="18"></i>
                                    Hello, {{ explode(' ', Auth::user()->name)[0] }}
                                </a>
                            @endif
                            <a href="{{ route('logout') }}" class="navbar__login-link logout-link" style="color: var(--text-muted); font-size: 13px;">Logout</a>
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

    <div class="toast-container" id="toastContainer"></div>

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
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        lucide.createIcons();

        // Cart Logic
        let cart = JSON.parse(localStorage.getItem('clothr_cart') || '[]');
        updateCartCount();

        function syncCartToDB() {
            if (!isLoggedIn) return;
            fetch('/api/cart/sync', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: cart })
            });
        }

        async function fetchCartFromDB() {
            if (!isLoggedIn) return;
            const res = await fetch('/api/cart');
            const dbItems = await res.json();
            
            if (dbItems.length > 0) {
                cart = dbItems.map(item => ({
                    id: item.product_id,
                    name: item.product.name,
                    price: item.product.price,
                    image: item.product.images[0],
                    size: item.size,
                    color: item.color,
                    quantity: item.quantity,
                    is_selected: item.is_selected
                }));
                localStorage.setItem('clothr_cart', JSON.stringify(cart));
                updateCartCount();
                if (typeof renderCart === 'function') renderCart();
                if (typeof renderSummary === 'function') renderSummary();
            } else if (cart.length > 0) {
                syncCartToDB();
            }
        }

        if (isLoggedIn) fetchCartFromDB();

        function updateCartCount() {
            const count = cart.reduce((acc, item) => acc + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            if (badge) badge.innerText = count;
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let icon = 'check-circle';
            if (type === 'error') icon = 'alert-circle';
            if (type === 'info') icon = 'info';

            toast.innerHTML = `<i data-lucide="${icon}" size="18"></i> <span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }, 3000);

            toast.onclick = () => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            };
        }

        function addToCart(product) {
            const existing = cart.find(item => item.id === product.id && item.size === product.size && item.color === product.color);
            if (existing) {
                existing.quantity += product.quantity || 1;
            } else {
                cart.push({...product, quantity: product.quantity || 1, is_selected: true});
            }
            localStorage.setItem('clothr_cart', JSON.stringify(cart));
            updateCartCount();
            showToast(product.name + ' added to cart!');

            if (isLoggedIn) {
                fetch('/api/cart/update', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify(product)
                });
            }
        }

        window.addToCartGlobal = function(id, name, price, image, size = 'M', color = '') {
            addToCart({id, name, price, image, size, color});
        }

        window.toggleWishlistGlobal = function(id, btn) {
            if (!isLoggedIn) {
                showToast('Please login to wishlist items', 'info');
                setTimeout(() => {
                    document.getElementById('openLoginModal').click();
                }, 1000);
                return;
            }

            const isActive = btn.classList.toggle('active');
            
            // Call API to persist wishlist
            fetch(`/wishlist/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.status === 'added') {
                    showToast('Added to wishlist', 'info');
                    btn.innerHTML = `<i data-lucide="heart" size="18" fill="currentColor"></i>`;
                } else {
                    showToast('Removed from wishlist');
                    btn.innerHTML = `<i data-lucide="heart" size="18"></i>`;
                }
                lucide.createIcons();
            });
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
        document.querySelectorAll('.logout-link').forEach(link => {
            link.addEventListener('click', () => {
                localStorage.removeItem('clothr_cart');
            });
        });
    </script>
    @yield('extra_js')
</body>
</html>

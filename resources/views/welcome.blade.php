<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTHR | Modern Women's Fashion</title>
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

        /* Hero */
        .hero { position: relative; height: 85vh; min-height: 600px; display: flex; align-items: center; overflow: hidden; }
        .hero__img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; }
        .hero__content { color: #fff; max-width: 600px; animation: fadeInUp 0.8s ease-out; }
        .hero__title { font-size: 64px; font-weight: 800; line-height: 1.1; margin-bottom: 20px; text-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .hero__btn { background: #fff; color: #000; padding: 16px 32px; font-weight: 600; font-size: 15px; display: inline-block; transition: 0.3s; }
        .hero__btn:hover { background: #000; color: #fff; transform: translateY(-3px); box-shadow: var(--shadow-md); }

        /* Sections */
        .section { padding: 80px 0; }
        .section-title { font-size: 32px; font-weight: 700; text-align: center; margin-bottom: 48px; }

        /* Category Grid */
        .categories__grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
        .category-card { background: var(--bg-secondary); border: 1px solid var(--border-color); padding: 40px 20px; text-align: center; border-radius: var(--radius-md); transition: 0.3s; }
        .category-card:hover { transform: translateY(-5px); border-color: #000; box-shadow: var(--shadow-md); }
        .category-card h3 { font-size: 15px; font-weight: 600; margin-top: 10px; }

        /* Product Grid */
        .products__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
        .product-card { transition: 0.3s; }
        .product-card:hover { transform: translateY(-5px); }
        .product-card__img-box { position: relative; aspect-ratio: 3/4; background: #f4f4f4; border-radius: var(--radius-md); overflow: hidden; margin-bottom: 15px; }
        .product-card__img { width: 100%; height: 100%; object-fit: cover; }
        .product-card__add { position: absolute; bottom: -50px; left: 0; right: 0; background: #000; color: #fff; padding: 12px; font-size: 13px; font-weight: 600; text-align: center; transition: 0.3s; }
        .product-card:hover .product-card__add { bottom: 0; }
        .product-card h3 { font-size: 15px; font-weight: 500; margin-bottom: 5px; }
        .product-card .price { font-weight: 700; color: var(--text-secondary); }

        /* Newsletter */
        .newsletter { background: #f0f0f0; text-align: center; }
        .newsletter__form { display: flex; max-width: 500px; margin: 30px auto 0; gap: 10px; }
        .newsletter__input { flex: 1; padding: 12px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; }
        .newsletter__btn { background: #000; color: #fff; padding: 12px 24px; border-radius: var(--radius-sm); font-weight: 600; }

        /* Footer */
        .footer { background: #fff; border-top: 1px solid var(--border-color); padding: 60px 0 30px; }
        .footer__grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
        .footer__col h4 { margin-bottom: 20px; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; }
        .footer__col p { font-size: 14px; color: var(--text-secondary); margin-bottom: 20px; }
        .footer__links li { margin-bottom: 10px; font-size: 14px; color: var(--text-secondary); }
        .footer__socials { display: flex; gap: 15px; }
        .footer__bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center; font-size: 13px; color: var(--text-muted); }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-overlay.active { display: flex; }
        .modal { background: #fff; width: 100%; max-width: 400px; border-radius: var(--radius-md); padding: 32px; position: relative; box-shadow: var(--shadow-lg); }
        .modal__close { position: absolute; top: 20px; right: 20px; color: var(--text-muted); }
        .modal__title { font-size: 24px; font-weight: 700; margin-bottom: 24px; text-align: center; }
        .modal__sso { display: flex; flex-direction: column; gap: 12px; }
        .btn-full { width: 100%; padding: 14px; text-align: center; border-radius: var(--radius-sm); font-weight: 600; font-size: 14px; }
        .btn-black { background: #000; color: #fff; }
        .btn-outline { border: 1px solid var(--border-color); }
        .modal__form { display: none; flex-direction: column; gap: 15px; }
        .modal__form.active { display: flex; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 13px; font-weight: 500; }
        .form-group input { padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; }
        .btn-blue { background: #2563eb; color: #fff; border: none; }
        .btn-blue:hover { background: #1d4ed8; transform: translateY(-2px); }
        .modal__back { font-size: 13px; font-weight: 600; color: #fff; background: #000; text-align: center; margin-top: 10px; cursor: pointer; padding: 14px; border-radius: var(--radius-sm); }
        .modal__back:hover { opacity: 0.8; }
        
        .login-tabs { display: flex; background: #f0f0f0; border-radius: 6px; padding: 4px; margin-bottom: 25px; }
        .login-tab { flex: 1; padding: 12px; border-radius: 4px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: 0.2s; }
        .login-tab.active { background: #000; color: #fff; }
        .login-tab:not(.active) { color: #666; background: transparent; }

        /* Page Switcher */
        #page-home, #page-login { display: none; }
        #page-home.active, #page-login.active { display: block; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="announcement-bar">
        🚚 FREE SHIPPING ON ORDERS OVER $50
    </div>

    <nav class="navbar">
        <div class="container navbar__inner">
            <a href="#" class="navbar__logo" onclick="showPage('home')">CLOTHR</a>
            <ul class="navbar__links">
                <li><a href="#" onclick="showPage('home')">Home</a></li>
                <li><a href="#">Shop All</a></li>
                <li><a href="#">Dresses</a></li>
                <li><a href="#">Tops & Blouses</a></li>
                <li><a href="#">Bottoms</a></li>
            </ul>
            <div class="navbar__actions">
                <button class="navbar__icon-btn"><i data-lucide="search" size="22"></i></button>
                <div id="auth-nav">
                    <a href="/login" class="navbar__login-link"><i data-lucide="user" size="22"></i> Login</a>
                </div>
                <button class="navbar__icon-btn">
                    <i data-lucide="shopping-bag" size="22"></i>
                    <span class="navbar__cart-badge" id="cart-count">0</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Home Page -->
    <div id="page-home" class="{{ $page === 'home' ? 'active' : '' }}">
        <section class="hero">
            <img src="/hero.png" class="hero__img" alt="Woman Model">
            <div class="container">
                <div class="hero__content">
                    <h1 class="hero__title">New Season Styles</h1>
                    <p style="font-size: 18px; margin-bottom: 30px;">Discover the latest trends in fashion. Shop our curated collection.</p>
                    <a href="#" class="hero__btn">Shop Now →</a>
                </div>
            </div>
        </section>

        <section class="section container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="categories__grid">
                <div class="category-card"><h3>Dresses</h3></div>
                <div class="category-card"><h3>Tops & Blouses</h3></div>
                <div class="category-card"><h3>Bottoms</h3></div>
                <div class="category-card"><h3>Outerwear</h3></div>
                <div class="category-card"><h3>Accessories</h3></div>
            </div>
        </section>

        <section class="section container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 style="font-size: 32px; font-weight: 700;">New Arrivals</h2>
                <a href="#" style="font-weight: 600; text-decoration: underline;">View All</a>
            </div>
            <div class="products__grid">
                <div class="product-card">
                    <div class="product-card__img-box">
                        <img src="/jeans.png" class="product-card__img">
                        <button class="product-card__add" onclick="addToCart('High-Waist Denim Jeans', 64.99)">Add to Cart</button>
                    </div>
                    <h3>High-Waist Denim Jeans</h3>
                    <p class="price">$64.99</p>
                </div>
                <div class="product-card">
                    <div class="product-card__img-box">
                        <img src="/trousers.png" class="product-card__img">
                        <button class="product-card__add" onclick="addToCart('Tailored Trousers', 54.99)">Add to Cart</button>
                    </div>
                    <h3>Tailored Trousers</h3>
                    <p class="price">$54.99</p>
                </div>
                <div class="product-card">
                    <div class="product-card__img-box">
                        <img src="/sweater.png" class="product-card__img">
                        <button class="product-card__add" onclick="addToCart('Cozy Knit Sweater', 69.99)">Add to Cart</button>
                    </div>
                    <h3>Cozy Knit Sweater</h3>
                    <p class="price">$69.99</p>
                </div>
                <div class="product-card">
                    <div class="product-card__img-box">
                        <img src="/handbag.png" class="product-card__img">
                        <button class="product-card__add" onclick="addToCart('Designer Handbag', 129.99)">Add to Cart</button>
                    </div>
                    <h3>Designer Handbag</h3>
                    <p class="price">$129.99</p>
                </div>
            </div>
        </section>

        <section class="section newsletter">
            <div class="container">
                <h2 class="section-title">Stay in the Loop</h2>
                <p style="color: var(--text-secondary);">Get the latest updates on new arrivals, exclusive offers, and fashion tips.</p>
                <form class="newsletter__form">
                    <input type="email" class="newsletter__input" placeholder="Enter your email">
                    <button class="newsletter__btn">Subscribe</button>
                </form>
            </div>
        </section>
    </div>

    <!-- Standalone Login Page -->
    <div id="page-login" class="{{ $page === 'login' ? 'active' : '' }}">
        <div class="section container" style="max-width: 500px; padding-top: 100px;">
            <h1 class="section-title">Welcome to CLOTHR</h1>
            <div style="background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 40px; box-shadow: var(--shadow-lg);">
                <div class="login-tabs">
                    <button id="standalone-login-tab" class="login-tab active" onclick="toggleStandaloneTab('login')">Login</button>
                    <button id="standalone-reg-tab" class="login-tab" onclick="toggleStandaloneTab('register')">Register</button>
                </div>
                
                <div id="standalone-login-form" class="modal__form active">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Password</label>
                        <input type="password" placeholder="Enter your password">
                    </div>
                    <button class="btn-full btn-black" style="margin-top: 25px;" onclick="doLogin('customer', 'User')">Login</button>
                </div>

                <div id="standalone-reg-form" class="modal__form">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" placeholder="Confirm Password">
                    </div>
                    <button class="btn-full btn-black" style="margin-top: 25px;" onclick="doLogin('customer', 'User')">Create Account</button>
                </div>
            </div>
            <p style="text-align: center; margin-top: 30px;"><a href="#" onclick="showPage('home')" style="color:#666; font-size: 14px;">Continue browsing →</a></p>
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
                    <li>All Products</li>
                    <li>Dresses</li>
                    <li>Tops</li>
                    <li>Bottoms</li>
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

    <!-- Login Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <button class="modal__close" onclick="closeModal()"><i data-lucide="x"></i></button>
            <div class="modal__title">LOGIN</div>
            
            <div id="step-sso" class="modal__sso">
                <button class="btn-full btn-outline" onclick="showStep('customer')">Sign In / Register</button>
                <button class="btn-full btn-black" onclick="showStep('admin')">Admin log in</button>
            </div>

            <div id="step-customer" class="modal__form">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="text" id="cust-id" placeholder="Enter your User ID">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="cust-pass" placeholder="Enter your password">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin: 5px 0 10px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox"> Remember me</label>
                    <a href="#" style="color:#2563eb; font-weight: 500;">Forgot password??</a>
                </div>
                <button class="btn-full btn-blue" onclick="doLogin('customer', 'User')">Log in</button>
                <div class="modal__back" onclick="showStep('sso')">Back to SSO</div>
            </div>

            <div id="step-admin" class="modal__form">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="text" id="admin-id" placeholder="Admin email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="admin-pass" placeholder="Admin password">
                </div>
                <div style="height: 10px;"></div>
                <button class="btn-full btn-blue" onclick="doLogin('admin', 'Admin')">Log in</button>
                <div class="modal__back" onclick="showStep('sso')">Back to SSO</div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let cart = [];
        let authState = 'guest';

        function openModal() {
            document.getElementById('modalOverlay').classList.add('active');
            showStep('sso');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        function showStep(step) {
            document.getElementById('step-sso').style.display = step === 'sso' ? 'flex' : 'none';
            document.getElementById('step-customer').classList.toggle('active', step === 'customer');
            document.getElementById('step-admin').classList.toggle('active', step === 'admin');
        }

        function doLogin(role, name) {
            authState = role;
            const authNav = document.getElementById('auth-nav');
            authNav.innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; font-size:14px; font-weight:500;">
                    <div style="width:30px; height:30px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="user" size="16"></i>
                    </div>
                    <span>${name}</span>
                    <button onclick="logout()" style="color:#666; font-size:12px;">Logout</button>
                </div>
            `;
            lucide.createIcons();
            closeModal();
            if (role === 'admin') {
                alert('Success! Redirecing to Admin Dashboard...');
            }
        }

        function logout() {
            authState = 'guest';
            location.reload();
        }

        function showPage(page) {
            document.getElementById('page-home').classList.toggle('active', page === 'home');
            document.getElementById('page-login').classList.toggle('active', page === 'login');
            window.scrollTo(0,0);
        }

        function toggleStandaloneTab(tab) {
            document.getElementById('standalone-login-tab').classList.toggle('active', tab === 'login');
            document.getElementById('standalone-reg-tab').classList.toggle('active', tab === 'register');
            document.getElementById('standalone-login-form').classList.toggle('active', tab === 'login');
            document.getElementById('standalone-reg-form').classList.toggle('active', tab === 'register');
        }

        function addToCart(name, price) {
            cart.push({name, price});
            document.getElementById('cart-count').innerText = cart.length;
            alert(name + ' added to cart!');
        }

        // Initial setup
        window.addEventListener('load', () => {
            console.log('CLOTHR Loaded');
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTHR | @yield('title', "Modern Women's Fashion")</title>
    <meta name="description" content="CLOTHR — Your destination for modern women's fashion. Curated collections that celebrate style and individuality.">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* ════════════════════════════════════════
           DESIGN TOKENS
        ════════════════════════════════════════ */
        :root {
            --sand:      #f5f0ea;
            --sand-dark: #ede6dc;
            --cream:     #faf8f5;
            --ink:       #1c1917;
            --ink-soft:  #44403c;
            --ink-muted: #78716c;
            --ink-faint: #a8a29e;
            --accent:    #1c1917;
            --accent-warm: #c8a882;
            --border:    #e7e0d8;
            --border-dark:#ccc3b8;
            --ruby:      #c0392b;
            --cobalt:    #1e40af;
            --emerald:   #166534;
            --white:     #ffffff;

            --shadow-xs: 0 1px 3px rgba(28,25,23,.06);
            --shadow-sm: 0 2px 8px rgba(28,25,23,.08);
            --shadow-md: 0 8px 24px rgba(28,25,23,.1);
            --shadow-lg: 0 20px 48px rgba(28,25,23,.14);

            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --container: 1320px;
            --nav-h:     68px;
        }

        /* ════════════════════════════════════════
           RESET & BASE
        ════════════════════════════════════════ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--cream);
            line-height: 1.55;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; transition: color .2s, opacity .2s; }
        button { font-family: inherit; cursor: pointer; border: none; background: none; transition: .2s; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }
        .container { max-width: var(--container); margin: 0 auto; padding: 0 28px; }

        /* ════════════════════════════════════════
           ANNOUNCEMENT BAR
        ════════════════════════════════════════ */
        .announcement-bar {
            background: var(--ink);
            color: var(--accent-warm);
            text-align: center;
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        /* ════════════════════════════════════════
           NAVBAR
        ════════════════════════════════════════ */
        .navbar {
            background: rgba(250,248,245,.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 200;
            border-bottom: 1px solid var(--border);
            height: var(--nav-h);
        }
        .navbar__inner {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }
        .navbar__logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--ink);
            flex-shrink: 0;
        }
        .navbar__links {
            display: flex;
            gap: 36px;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .02em;
        }
        .navbar__links a {
            color: var(--ink-soft);
            padding-bottom: 3px;
            border-bottom: 1.5px solid transparent;
            transition: color .2s, border-color .2s;
        }
        .navbar__links a:hover { color: var(--ink); border-bottom-color: var(--ink); }

        .navbar__actions { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
        .navbar__icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            color: var(--ink-soft);
            transition: background .2s, color .2s;
            position: relative;
        }
        .navbar__icon-btn:hover { background: var(--sand); color: var(--ink); }
        .navbar__cart-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--ink);
            color: var(--white);
            font-size: 9px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            border: 2px solid var(--cream);
        }
        .navbar__login-link {
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--ink-soft);
            padding: 7px 14px;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            transition: background .2s, border-color .2s, color .2s;
        }
        .navbar__login-link:hover { background: var(--ink); color: var(--white); border-color: var(--ink); }

        /* Customer Notification Dropdown */
        .cust-notif-dropdown {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            width: 340px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 300;
            overflow: hidden;
            text-align: left;
        }
        .cust-notif-dropdown.show { display: block; }
        .cust-notif-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cust-notif-header h3 { font-size: 15px; font-weight: 700; }
        .cust-notif-header button { font-size: 12px; color: var(--cobalt); cursor: pointer; }
        .cust-notif-header button:hover { text-decoration: underline; }
        .cust-notif-list { max-height: 420px; overflow-y: auto; }
        .cust-notif-item {
            padding: 16px;
            border-bottom: 1px solid var(--sand);
            display: flex;
            gap: 12px;
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
        }
        .cust-notif-item:hover { background: var(--sand); }
        .cust-notif-item.unread { background: #fdfbf7; border-left: 3px solid var(--ink); }
        .cust-notif-item.unread:hover { background: var(--sand); }
        .cust-notif-icon {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--sand-dark);
            display: flex; align-items: center; justify-content: center;
            color: var(--ink-soft); flex-shrink: 0;
        }
        .cust-notif-content { flex-grow: 1; }
        .cust-notif-title { font-size: 13px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
        .cust-notif-message { font-size: 12px; color: var(--ink-muted); margin-bottom: 4px; line-height: 1.4; }
        .cust-notif-time { font-size: 11px; color: var(--ink-faint); }
        .cust-notif-empty { padding: 40px 20px; text-align: center; color: var(--ink-muted); font-size: 13px; }
        .cust-notif-empty strong { display: block; color: var(--ink); margin: 12px 0 4px; font-size: 14px; }

        /* ════════════════════════════════════════
           LAYOUT HELPERS
        ════════════════════════════════════════ */
        .section { padding: 96px 0; }
        .section-eyebrow {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: var(--accent-warm);
            margin-bottom: 12px;
            text-align: center;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 600;
            margin-bottom: 56px;
            letter-spacing: -.01em;
            text-align: center;
            color: var(--ink);
            line-height: 1.2;
        }

        /* ════════════════════════════════════════
           PRODUCT CARDS
        ════════════════════════════════════════ */
        .products__grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
            row-gap: 52px;
        }
        .product-card { transition: .35s cubic-bezier(.25,.8,.25,1); position: relative; }
        .product-card:hover { transform: translateY(-6px); }

        .product-card__img-box {
            position: relative;
            aspect-ratio: 3/4;
            background: var(--sand);
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: var(--shadow-xs);
            transition: box-shadow .35s;
        }
        .product-card:hover .product-card__img-box { box-shadow: var(--shadow-md); }
        .product-card__img { width: 100%; height: 100%; object-fit: cover; transition: transform .55s cubic-bezier(.25,.8,.25,1); }
        .product-card:hover .product-card__img { transform: scale(1.04); }

        /* Hover CTA overlay */
        .product-card__overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            padding: 18px;
            background: linear-gradient(to top, rgba(28,25,23,.55) 0%, transparent 55%);
            opacity: 0;
            transition: opacity .3s;
        }
        .product-card:hover .product-card__overlay { opacity: 1; }
        .product-card__add {
            background: var(--white);
            color: var(--ink);
            width: 100%;
            padding: 11px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            border-radius: var(--radius-sm);
            letter-spacing: .06em;
            text-transform: uppercase;
            transition: background .2s, color .2s;
        }
        .product-card__add:hover { background: var(--ink); color: var(--white); }

        .product-card__wishlist {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(4px);
            color: var(--ink);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
            z-index: 2;
            transition: background .2s, color .2s, transform .2s;
        }
        .product-card__wishlist:hover { transform: scale(1.1); color: #e55; }
        .product-card__wishlist.active { color: #e55; }

        .product-card h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--ink);
            line-height: 1.3;
        }
        .product-card .price { font-weight: 700; color: var(--ink); font-size: 14px; }
        .product-card .old-price { font-weight: 400; color: var(--ink-faint); text-decoration: line-through; margin-left: 8px; font-size: 13px; }
        .product-card .sale-price { color: var(--cobalt); }

        .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--ink);
            color: var(--white);
            padding: 4px 10px;
            font-size: 9px;
            font-weight: 800;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: .1em;
            z-index: 2;
        }

        /* Responsive */
        @media (max-width: 1100px) { .products__grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 700px)  { .products__grid { grid-template-columns: repeat(2, 1fr); } }

        /* ════════════════════════════════════════
           MODALS
        ════════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(28,25,23,.45);
            backdrop-filter: blur(6px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .login-modal {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 36px;
            width: 420px;
            max-width: 92vw;
            box-shadow: var(--shadow-lg);
            animation: modalPop .3s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes modalPop {
            from { transform: translateY(-18px) scale(.97); opacity: 0; }
            to   { transform: translateY(0) scale(1); opacity: 1; }
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .modal-title { font-size: 13px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--ink); }
        .modal-close { color: var(--ink-faint); font-size: 22px; line-height: 1; padding: 4px; transition: color .2s; }
        .modal-close:hover { color: var(--ink); }
        .modal-body { display: flex; flex-direction: column; gap: 12px; }

        .btn-sso-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--white);
            color: var(--ink);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, border-color .2s;
        }
        .btn-sso-outline:hover { background: var(--sand); border-color: var(--border-dark); }
        .btn-sso-black {
            width: 100%;
            padding: 14px;
            background: var(--ink);
            color: var(--white);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }
        .btn-sso-black:hover { background: var(--ink-soft); }

        .form-label { display: block; font-size: 12px; font-weight: 700; letter-spacing: .04em; margin-bottom: 7px; color: var(--ink-soft); }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            outline: none;
            background: var(--cream);
            transition: border-color .2s, background .2s;
            font-family: inherit;
        }
        .form-input:focus { border-color: var(--ink); background: var(--white); }
        .btn-blue {
            background: var(--ink);
            color: var(--white);
            width: 100%;
            padding: 13px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: .04em;
            margin-top: 8px;
        }
        .btn-blue:hover { background: var(--ink-soft); }
        .form-row-between { display: flex; justify-content: space-between; align-items: center; margin: 12px 0; font-size: 13px; color: var(--ink-muted); }

        /* ════════════════════════════════════════
           TOAST
        ════════════════════════════════════════ */
        .toast-container {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background: var(--ink);
            color: var(--white);
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn .3s cubic-bezier(.34,1.56,.64,1);
            cursor: pointer;
            max-width: 320px;
            border-left: 4px solid transparent;
        }
        @keyframes toastIn { from { transform: translateX(110%); opacity:0; } to { transform: translateX(0); opacity:1; } }
        .toast.removing { animation: toastOut .3s forwards; }
        @keyframes toastOut { to { transform: translateX(110%); opacity:0; } }
        .toast-success { border-left-color: #10b981; }
        .toast-error   { border-left-color: #ef4444; }
        .toast-info    { border-left-color: var(--accent-warm); }

        /* ════════════════════════════════════════
           FOOTER
        ════════════════════════════════════════ */
        .footer {
            background: var(--ink);
            color: var(--white);
            padding: 80px 0 40px;
            margin-top: 100px;
        }
        .footer__grid { display: grid; grid-template-columns: 2.2fr 1fr 1fr 1fr; gap: 60px; }
        .footer__logo {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 16px;
            display: block;
        }
        .footer__col p { font-size: 14px; color: rgba(255,255,255,.5); line-height: 1.75; margin-bottom: 24px; }
        .footer__col h4 {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--accent-warm);
            margin-bottom: 22px;
        }
        .footer__links li { margin-bottom: 12px; }
        .footer__links a { font-size: 14px; color: rgba(255,255,255,.55); transition: color .2s; }
        .footer__links a:hover { color: var(--white); }
        .footer__socials { display: flex; gap: 14px; }
        .footer__socials a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,.5);
            transition: background .2s, color .2s, border-color .2s;
        }
        .footer__socials a:hover { background: rgba(255,255,255,.08); color: var(--white); border-color: rgba(255,255,255,.3); }
        .footer__divider { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 52px 0 28px; }
        .footer__bottom { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: rgba(255,255,255,.35); flex-wrap: wrap; gap: 10px; }

        @media (max-width: 900px) { .footer__grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 580px) { .footer__grid { grid-template-columns: 1fr; } }

        @yield('extra_css')
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        ✦ &nbsp; Free shipping on orders over ₱2,500 &nbsp; ✦ &nbsp; New arrivals every Friday &nbsp; ✦
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar__inner">
            <a href="{{ route('home') }}" class="navbar__logo">CLOTHR</a>

            <ul class="navbar__links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop') }}">Shop All</a></li>
                <li><a href="{{ route('category', 'dresses') }}">Dresses</a></li>
                <li><a href="{{ route('category', 'tops-blouses') }}">Tops &amp; Blouses</a></li>
                <li><a href="{{ route('category', 'bottoms') }}">Bottoms</a></li>
            </ul>

            <div class="navbar__actions">
                <button class="navbar__icon-btn" title="Search">
                    <i data-lucide="search" size="19"></i>
                </button>

                <div id="auth-nav">
                    @auth
                        <div style="display:flex; align-items:center; gap:14px;">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}"
                                   style="font-size:12px; font-weight:700; color:var(--accent-warm); border:1.5px solid var(--accent-warm); padding:5px 12px; border-radius:50px; letter-spacing:.06em;">
                                    Admin
                                </a>
                            @else
                                <a href="{{ route('profile.index') }}"
                                   style="font-size:13px; font-weight:600; display:flex; align-items:center; gap:6px; color:var(--ink-soft);">
                                    <i data-lucide="user" size="17"></i>
                                    {{ explode(' ', Auth::user()->name)[0] }}
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                @csrf
                                <button type="submit"
                                        style="font-size:12px; font-weight:600; color:var(--ink-faint);"
                                        onclick="localStorage.removeItem('clothr_cart');">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <button id="openLoginModal" class="navbar__login-link">
                            <i data-lucide="user" size="16"></i> Sign In
                        </button>
                    @endauth
                </div>

                @auth
                <div style="position: relative;" id="cust-notif-container">
                    <button class="navbar__icon-btn" title="Notifications" onclick="toggleCustNotifications(event)">
                        <i data-lucide="bell" size="19"></i>
                        <span class="navbar__cart-badge" id="cust-notif-badge" style="display:none; background:#ef4444; border-color:#ef4444; color:white;">0</span>
                    </button>
                    <!-- Notification Dropdown -->
                    <div class="cust-notif-dropdown" id="custNotifDropdown">
                        <div class="cust-notif-header">
                            <h3>Notifications</h3>
                            <button id="custMarkAllBtn" onclick="markAllCustAsRead(event)" style="display:none;">Mark all as read</button>
                        </div>
                        <div class="cust-notif-list" id="custNotifList">
                            <div class="cust-notif-empty">Loading...</div>
                        </div>
                    </div>
                </div>
                @endauth

                <a href="{{ route('cart') }}" class="navbar__icon-btn" title="Cart">
                    <i data-lucide="shopping-bag" size="19"></i>
                    <span class="navbar__cart-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <main>@yield('content')</main>

    <!-- LOGIN MODAL -->
    <div class="modal-overlay" id="loginModalOverlay">
        <!-- Step 1: SSO chooser -->
        <div class="login-modal" id="ssoModal">
            <div class="modal-header">
                <span class="modal-title">Welcome Back</span>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <a href="{{ route('login') }}" class="btn-sso-outline">Sign In / Register</a>
                <button class="btn-sso-black" id="showAdminLogin">Admin Log In</button>
            </div>
        </div>

        <!-- Step 2: Admin login form -->
        <div class="login-modal" id="adminModal" style="display:none;">
            <div class="modal-header">
                <span class="modal-title">Admin Access</span>
                <button class="modal-close" id="closeAdminModal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="admin">
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="admin@clothr.com" required>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--ink-muted);">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn-blue">Log In</button>
                    <button type="button" class="btn-sso-outline" style="margin-top:10px;" id="backToSSO">← Back</button>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__col">
                    <span class="footer__logo">CLOTHR</span>
                    <p>Your destination for modern women's fashion. Curated collections that celebrate style and individuality.</p>
                    <div class="footer__socials">
                        <a href="https://www.facebook.com/share/14ViXfujQf3/?mibextid=wwXIfr" target="_blank" aria-label="Facebook">
                            <i data-lucide="facebook" size="15"></i>
                        </a>
                        <a href="https://www.instagram.com/clothr.co_" target="_blank" aria-label="Instagram">
                            <i data-lucide="instagram" size="15"></i>
                        </a>
                        <a href="#" aria-label="X / Twitter">
                            <i data-lucide="twitter" size="15"></i>
                        </a>
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
                    <h4>Help</h4>
                    <ul class="footer__links">
                        <li><a href="{{ route('info', 'contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('info', 'shipping') }}">Shipping Info</a></li>
                        <li><a href="{{ route('info', 'returns') }}">Returns</a></li>
                        <li><a href="{{ route('info', 'faq') }}">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer__col">
                    <h4>Company</h4>
                    <ul class="footer__links">
                        <li><a href="{{ route('info', 'about') }}">About Us</a></li>
                        <li><a href="{{ route('info', 'privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('info', 'terms') }}">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <hr class="footer__divider">
            <div class="footer__bottom">
                <span>© 2026 CLOTHR. All rights reserved.</span>
                <span>Crafted with ♡ in the Philippines</span>
            </div>
        </div>
    </footer>

    <script>
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        lucide.createIcons();

        /* ── Cart ── */
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
            const count = cart.reduce((a, i) => a + i.quantity, 0);
            const badge = document.getElementById('cart-count');
            if (badge) badge.textContent = count;
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            const icons = { success: 'check-circle', error: 'alert-circle', info: 'info' };
            toast.innerHTML = `<i data-lucide="${icons[type]||'check-circle'}" size="16"></i> <span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();
            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }, 3200);
            toast.onclick = () => { toast.classList.add('removing'); setTimeout(() => toast.remove(), 300); };
        }

        function addToCart(product) {
            const existing = cart.find(i => i.id === product.id && i.size === product.size);
            let out;
            if (existing) { existing.quantity += product.quantity || 1; out = existing; }
            else { out = {...product, quantity: product.quantity || 1, is_selected: true}; cart.push(out); }
            localStorage.setItem('clothr_cart', JSON.stringify(cart));
            updateCartCount();
            showToast(product.name + ' added to cart!');
            if (isLoggedIn) {
                fetch('/api/cart/update', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify(out)
                });
            }
        }

        window.addToCartGlobal = (id, name, price, image, size, color) =>
            addToCart({id, name, price, image, size: size||'', color: color||''});

        window.toggleWishlistGlobal = function(id, btn) {
            if (!isLoggedIn) {
                showToast('Please sign in to save items', 'info');
                setTimeout(() => document.getElementById('openLoginModal')?.click(), 900);
                return;
            }
            btn.classList.toggle('active');
            fetch(`/wishlist/toggle/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.status === 'added') {
                    showToast('Saved to wishlist', 'info');
                    btn.innerHTML = `<i data-lucide="heart" size="17" fill="currentColor"></i>`;
                } else {
                    showToast('Removed from wishlist');
                    btn.innerHTML = `<i data-lucide="heart" size="17"></i>`;
                }
                lucide.createIcons();
            });
        };

        /* ── Login Modal ── */
        const overlay   = document.getElementById('loginModalOverlay');
        const ssoModal  = document.getElementById('ssoModal');
        const adminModal= document.getElementById('adminModal');
        const openBtn   = document.getElementById('openLoginModal');

        openBtn?.addEventListener('click', () => {
            overlay.classList.add('show');
            ssoModal.style.display='block';
            adminModal.style.display='none';
            document.body.style.overflow='hidden';
        });

        const closeModal = () => { overlay.classList.remove('show'); document.body.style.overflow=''; };
        document.getElementById('closeModal')?.addEventListener('click', closeModal);
        document.getElementById('closeAdminModal')?.addEventListener('click', closeModal);
        overlay?.addEventListener('click', e => { if(e.target===overlay) closeModal(); });
        document.getElementById('showAdminLogin')?.addEventListener('click', () => { ssoModal.style.display='none'; adminModal.style.display='block'; });
        document.getElementById('backToSSO')?.addEventListener('click', () => { adminModal.style.display='none'; ssoModal.style.display='block'; });
        document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
        document.querySelectorAll('.logout-link').forEach(l => l.addEventListener('click', () => localStorage.removeItem('clothr_cart')));

        /* ── Customer Notifications ── */
        if (isLoggedIn) {
            function toggleCustNotifications(e) {
                if(e) e.stopPropagation();
                const dropdown = document.getElementById('custNotifDropdown');
                dropdown.classList.toggle('show');
                if(dropdown.classList.contains('show')) fetchCustNotifications();
            }

            document.addEventListener('click', e => {
                const dropdown = document.getElementById('custNotifDropdown');
                const btn = document.getElementById('cust-notif-container');
                if (dropdown && dropdown.classList.contains('show') && !dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });

            function fetchCustNotifications() {
                fetch('/notifications')
                    .then(res => res.json())
                    .then(data => renderCustNotifications(data))
                    .catch(() => {});
            }

            function renderCustNotifications(data) {
                const list = document.getElementById('custNotifList');
                const badge = document.getElementById('cust-notif-badge');
                const markAll = document.getElementById('custMarkAllBtn');
                list.innerHTML = '';

                if (data.length === 0) {
                    list.innerHTML = `<div class="cust-notif-empty"><i data-lucide="smile" size="32" style="margin:0 auto; color:var(--ink-faint);"></i><strong>You are all caught up!</strong>We will notify you when your order status changes</div>`;
                    badge.style.display = 'none';
                    markAll.style.display = 'none';
                    lucide.createIcons();
                    return;
                }

                markAll.style.display = 'block';
                let unread = 0;

                data.forEach(item => {
                    if (!item.is_read) unread++;
                    let icon = 'bell';
                    if(item.type === 'order_processing') icon = 'clock';
                    if(item.type === 'order_shipped') icon = 'truck';
                    if(item.type === 'order_delivered') icon = 'check-circle';
                    if(item.type === 'order_cancelled') icon = 'x-circle';

                    const unreadCls = item.is_read ? '' : 'unread';
                    list.insertAdjacentHTML('beforeend', `
                        <div class="cust-notif-item ${unreadCls}" onclick="markCustAsRead(${item.id}, '${item.link}')">
                            <div class="cust-notif-icon"><i data-lucide="${icon}" size="18"></i></div>
                            <div class="cust-notif-content">
                                <div class="cust-notif-title">${item.title}</div>
                                <div class="cust-notif-message">${item.message}</div>
                                <div class="cust-notif-time">${item.created_at}</div>
                            </div>
                        </div>
                    `);
                });
                lucide.createIcons();

                if (unread > 0) {
                    badge.style.display = 'flex';
                    badge.innerText = unread > 99 ? '99+' : unread;
                } else {
                    badge.style.display = 'none';
                    markAll.style.display = 'none';
                }
            }

            window.markCustAsRead = function(id, link) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => { window.location.href = link; });
            };

            window.markAllCustAsRead = function(e) {
                if(e) e.stopPropagation();
                fetch(`/notifications/read-all`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => {
                    document.getElementById('cust-notif-badge').style.display = 'none';
                    document.getElementById('custMarkAllBtn').style.display = 'none';
                    document.querySelectorAll('.cust-notif-item').forEach(el => el.classList.remove('unread'));
                });
            };

            // poll every 60s
            setInterval(() => {
                fetch('/notifications').then(res => res.json()).then(data => {
                    const unreadCount = data.filter(n => !n.is_read).length;
                    const badge = document.getElementById('cust-notif-badge');
                    if(unreadCount > 0) {
                        badge.style.display = 'flex';
                        badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                    } else {
                        badge.style.display = 'none';
                    }
                    if(document.getElementById('custNotifDropdown').classList.contains('show')) {
                        renderCustNotifications(data);
                    }
                });
            }, 60000);
            
            // initial badge fetch
            fetch('/notifications').then(res => res.json()).then(data => {
                const unreadCount = data.filter(n => !n.is_read).length;
                if(unreadCount > 0) {
                    const badge = document.getElementById('cust-notif-badge');
                    badge.style.display = 'flex';
                    badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                }
            });
        }
    </script>
    @yield('extra_js')
</body>
</html>

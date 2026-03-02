<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
@php
    $isRegister = ($activeTab ?? '') === 'register' || $errors->has('name') || $errors->has('password_confirmation');
@endphp
<body data-active-tab="{{ $isRegister ? 'register' : 'login' }}">
    {{-- Top bar --}}
    <div class="auth-topbar">
        <span>
            FREE SHIPPING ON ORDERS OVER $50
            <svg class="truck-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
        </span>
    </div>

    {{-- Main header --}}
    <header class="auth-header">
        <div class="auth-header-inner">
            <span class="auth-logo">CLOTHR</span>
            <nav class="auth-nav">
                <span>Home</span>
                <span>Shop All</span>
                <span>Dresses</span>
                <span>Tops & Blouses</span>
                <span>Bottoms</span>
            </nav>
            <div class="auth-nav-right">
                <button type="button" class="icon-btn" aria-label="Search">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
                <a href="{{ route('login') }}">Login</a>
                <button type="button" class="icon-btn" aria-label="Cart">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="auth-main">
        <h1>Welcome to CLOTHR</h1>

        <div class="auth-card">
            <div class="admin-login-section">
                <p class="admin-login-subtitle">Are you an admin?</p>
                <a href="{{ route('login') }}" class="admin-login-btn">Admin Log in</a>
            </div>

            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab {{ !$isRegister ? 'active' : '' }}" data-tab="login" data-href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="auth-tab {{ $isRegister ? 'active' : '' }}" data-tab="register" data-href="{{ route('register') }}">Register</a>
            </div>

            <div class="auth-form-container">
                {{-- Login form --}}
                <form id="auth-login-form" class="auth-form {{ !$isRegister ? 'active' : '' }}" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="auth-form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus autocomplete="email">
                        @error('email')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        @error('password')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="auth-btn">Login</button>
                </form>

                {{-- Register form --}}
                <form id="auth-register-form" class="auth-form {{ $isRegister ? 'active' : '' }}" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="auth-form-group">
                        <label for="register-name">Full Name</label>
                        <input type="text" id="register-name" name="name" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" placeholder="Enter your password" required autocomplete="new-password">
                        @error('password')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-password-confirm">Confirm Password</label>
                        <input type="password" id="register-password-confirm" name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="auth-btn">Register</button>
                </form>
            </div>
        </div>

        <span class="auth-continue-link">Continue browsing</span>
    </main>

    {{-- Footer --}}
    <footer class="auth-footer">
        <div class="auth-footer-inner">
            <div class="auth-footer-brand">
                <h3>CLOTHR</h3>
                <p>Your destination for modern women's fashion. Curated collections that celebrate style and individuality.</p>
                <div class="auth-footer-social">
                    <span aria-label="Facebook">f</span>
                    <span aria-label="Instagram">📷</span>
                    <span aria-label="Twitter">🐦</span>
                </div>
            </div>
            <div class="auth-footer-column">
                <h4>Shop</h4>
                <ul>
                    <li><span>All Products</span></li>
                    <li><span>Dresses</span></li>
                    <li><span>Tops</span></li>
                    <li><span>Bottoms</span></li>
                </ul>
            </div>
            <div class="auth-footer-column">
                <h4>Customer Service</h4>
                <ul>
                    <li><span>Contact Us</span></li>
                    <li><span>Shipping Info</span></li>
                    <li><span>Returns</span></li>
                    <li><span>FAQ</span></li>
                </ul>
            </div>
            <div class="auth-footer-column">
                <h4>About</h4>
                <ul>
                    <li><span>About Us</span></li>
                    <li><span>Privacy Policy</span></li>
                    <li><span>Terms of Service</span></li>
                </ul>
            </div>
        </div>
        <p class="auth-footer-copy">© 2026 CLOTHR. All rights reserved.</p>
    </footer>

    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>

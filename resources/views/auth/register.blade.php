<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body data-active-tab="register">
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
            <a href="/" class="auth-logo">CLOTHR</a>
            <nav class="auth-nav">
                <a href="/">Home</a>
                <a href="/products">Shop All</a>
            </nav>
            <div class="auth-nav-right">
                <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="auth-main">
        <h1>Create Your Account</h1>

        <div class="auth-card">
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab" data-tab="login">Login</a>
                <a href="{{ route('register') }}" class="auth-tab active" data-tab="register">Register</a>
            </div>

            <div class="auth-form-container">
                <form id="auth-register-form" class="auth-form active" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="auth-form-group">
                        <label for="register-name">Full Name</label>
                        <input type="text" id="register-name" name="name" placeholder="Enter your full name"
                               value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" placeholder="Enter your email"
                               value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password"
                               placeholder="Enter your password" required autocomplete="new-password">
                        @error('password')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="register-password-confirm">Confirm Password</label>
                        <input type="password" id="register-password-confirm" name="password_confirmation"
                               placeholder="Confirm your password" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="auth-btn">Register</button>
                </form>
            </div>
        </div>

        <a href="{{ route('login') }}" class="auth-continue-link">Already have an account? Login</a>
    </main>

    {{-- Footer --}}
    <footer class="auth-footer">
        <div class="auth-footer-inner">
            <div class="auth-footer-brand">
                <h3>CLOTHR</h3>
                <p>Your destination for modern women's fashion.</p>
            </div>
        </div>
        <p class="auth-footer-copy">&copy; {{ date('Y') }} CLOTHR. All rights reserved.</p>
    </footer>

    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>

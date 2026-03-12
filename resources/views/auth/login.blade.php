@extends('layouts.shop')
@section('title', 'Login')
@section('extra_css')
    .auth-page { min-height: 70vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; }
    .auth-heading { font-size: 26px; font-weight: 700; margin-bottom: 28px; }
    .auth-card { background: #fff; border-radius: 16px; padding: 28px 32px; width: 480px; max-width: 95vw; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
    .auth-tabs { display: grid; grid-template-columns: 1fr 1fr; background: #f3f4f6; border-radius: 10px; padding: 4px; margin-bottom: 24px; }
    .auth-tab { padding: 10px; border: none; border-radius: 8px; background: transparent; font-size: 14px; font-weight: 500; cursor: pointer; color: #6b7280; transition: all 0.2s; }
    .auth-tab.active { background: #000; color: #fff; }
    .form-input-auth { width: 100%; padding: 12px 16px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; margin-bottom: 12px; outline: none; background: #f9fafb; transition: border 0.2s; display: block; }
    .form-input-auth:focus { border-color: #000; background: #fff; }
    .btn-full { width: 100%; padding: 13px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 4px; }
    .btn-black { background: #000; color: #fff; border: none; }
    .btn-black:hover { background: #1a1a1a; }
    .hidden { display: none; }
    .continue-browsing { margin-top: 20px; color: #9ca3af; font-size: 14px; text-decoration: none; }
    .continue-browsing:hover { color: #374151; }
    .alert-error { background: #fee2e2; color: #dc2626; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 14px; }
@endsection

@section('content')
<div class="auth-page">
    <h1 class="auth-heading">Welcome to CLOTHR</h1>

    <div class="auth-card">
        <!-- Tab Toggle -->
        <div class="auth-tabs">
            <button class="auth-tab {{ !old('action') || old('action') == 'login' ? 'active' : '' }}" id="loginTab" onclick="switchTab('login')">Login</button>
            <button class="auth-tab {{ old('action') == 'register' ? 'active' : '' }}" id="registerTab" onclick="switchTab('register')">Register</button>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="{{ old('action') == 'register' ? 'hidden' : '' }}">
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="has_cart" id="hasCartInputLogin">
                @if($errors->any() && old('action') != 'register')
                    <div class="alert-error">{{ $errors->first() }}</div>
                @endif
                <input type="email" name="email" class="form-input-auth" placeholder="Enter your email" value="{{ old('email') }}" required>
                <input type="password" name="password" class="form-input-auth" placeholder="Enter your password" required>
                <button type="submit" class="btn-black btn-full">Login</button>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="{{ route('password.request') }}" style="font-size: 13px; color: #6b7280; text-decoration: underline;">Forgot password?</a>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="{{ !old('action') || old('action') == 'login' ? 'hidden' : '' }}">
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="has_cart" id="hasCartInputRegister">
                @if($errors->any() && old('action') == 'register')
                    <div class="alert-error">{{ $errors->first() }}</div>
                @endif
                <input type="text" name="name" class="form-input-auth" placeholder="Full Name" value="{{ old('name') }}" required>
                <input type="email" name="email" class="form-input-auth" placeholder="Email" value="{{ old('email') }}" required>
                <input type="password" name="password" class="form-input-auth" placeholder="Password" required>
                <input type="password" name="password_confirmation" class="form-input-auth" placeholder="Confirm Password" required>
                <button type="submit" class="btn-black btn-full">Create Account</button>
            </form>
        </div>
    </div>

    <a href="{{ route('home') }}" class="continue-browsing">
        Continue browsing →
    </a>
</div>
@endsection

@section('extra_js')
<script>
function switchTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');

    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
    } else {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
        registerTab.classList.add('active');
        loginTab.classList.remove('active');
    }
}

// Check cart on load
window.addEventListener('load', () => {
    const hasCart = JSON.parse(localStorage.getItem('clothr_cart') || '[]').length > 0;
    if (document.getElementById('hasCartInputLogin')) document.getElementById('hasCartInputLogin').value = hasCart ? '1' : '0';
    if (document.getElementById('hasCartInputRegister')) document.getElementById('hasCartInputRegister').value = hasCart ? '1' : '0';
});
</script>
@endsection

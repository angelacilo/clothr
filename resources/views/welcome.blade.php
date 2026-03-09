@extends('layouts.shop')

@section('title', 'Login / Register')

@section('extra_css')
    .auth-container { max-width: 500px; margin: 80px auto; padding: 40px; background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); }
    .login-tabs { display: flex; background: #f0f0f0; border-radius: 6px; padding: 4px; margin-bottom: 25px; }
    .login-tab { flex: 1; padding: 12px; border-radius: 4px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: 0.2s; }
    .login-tab.active { background: #000; color: #fff; }
    .login-tab:not(.active) { color: #666; background: transparent; }
    
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
    .form-group label { font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); }
    .form-group input { padding: 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; font-size: 14px; }
    .form-group input:focus { border-color: #000; }
    
    .auth-btn { width: 100%; padding: 16px; background: #000; color: #fff; font-weight: 700; border-radius: var(--radius-sm); font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 10px; }
    .auth-btn:hover { background: var(--accent-hover); }
    
    .error-msg { color: var(--error); font-size: 12px; margin-top: 4px; }
@endsection

@section('content')
<div class="container">
    <div class="auth-container">
        <h1 style="font-size: 28px; font-weight: 800; text-align: center; margin-bottom: 30px;">Welcome to CLOTHR</h1>
        
        <div class="login-tabs">
            <button id="tab-login" class="login-tab {{ old('action') == 'register' || old('action') == 'admin' ? '' : 'active' }}" onclick="switchTab('login')">Login</button>
            <button id="tab-register" class="login-tab {{ old('action') == 'register' ? 'active' : '' }}" onclick="switchTab('register')">Register</button>
            <button id="tab-admin" class="login-tab {{ old('action') == 'admin' ? 'active' : '' }}" onclick="switchTab('admin')">Admin</button>
        </div>

        <div id="form-login" style="display: {{ old('action') == 'register' || old('action') == 'admin' ? 'none' : 'block' }}">
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="auth-btn">Login</button>
            </form>
        </div>

        <div id="form-register" style="display: {{ old('action') == 'register' ? 'block' : 'none' }}">
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <button type="submit" class="auth-btn">Create Account</button>
            </form>
        </div>

        <div id="form-admin" style="display: {{ old('action') == 'admin' ? 'block' : 'none' }}">
            <div style="margin-bottom: 20px; padding: 15px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 13px; color: #92400e;">
                <strong>Admin Portal</strong>: Access restricted to authorized personnel.
            </div>
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="admin">
                <div class="form-group">
                    <label>Admin ID / Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@clothr.com">
                </div>
                <div class="form-group">
                    <label>Secure Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="auth-btn" style="background: #1e40af;">Enter Dashboard</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function switchTab(type) {
        document.getElementById('tab-login').classList.toggle('active', type === 'login');
        document.getElementById('tab-register').classList.toggle('active', type === 'register');
        document.getElementById('tab-admin').classList.toggle('active', type === 'admin');
        
        document.getElementById('form-login').style.display = type === 'login' ? 'block' : 'none';
        document.getElementById('form-register').style.display = type === 'register' ? 'block' : 'none';
        document.getElementById('form-admin').style.display = type === 'admin' ? 'block' : 'none';
    }
</script>
@endsection

@extends('layouts.shop')
@section('title', 'Forgot Password')
@section('extra_css')
    .auth-page { min-height: 70vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; }
    .auth-heading { font-size: 26px; font-weight: 700; margin-bottom: 28px; }
    .auth-card { background: #fff; border-radius: 16px; padding: 28px 32px; width: 440px; max-width: 95vw; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
    .form-input-auth { width: 100%; padding: 12px 16px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; margin-bottom: 12px; outline: none; background: #f9fafb; transition: border 0.2s; display: block; }
    .form-input-auth:focus { border-color: #000; background: #fff; }
    .btn-full { width: 100%; padding: 13px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 4px; }
    .btn-black { background: #000; color: #fff; border: none; }
    .btn-black:hover { background: #1a1a1a; }
    .alert-success { background: #dcfce7; color: #166534; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 14px; }
@endsection

@section('content')
<div class="auth-page">
    <h1 class="auth-heading">Forgot Password</h1>

    <div class="auth-card">
        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">Enter your email or phone number to receive a verification code.</p>
        
        <form action="{{ route('password.code') }}" method="POST">
            @csrf
            <input type="text" name="identifier" class="form-input-auth" placeholder="Email or Phone Number" required>
            <button type="submit" class="btn-black btn-full">Send Code</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="{{ route('login') }}" style="font-size: 14px; color: #6b7280; text-decoration: underline;">Back to Login</a>
        </div>
    </div>
</div>
@endsection

@extends('profile.layout')

@section('title', 'Account Settings')

@section('profile_content')
<div style="max-width: 600px;">
    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 30px;">Profile Information</h2>

    @if(session('status'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
            @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
            @error('email') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 30px;">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-input" value="{{ $user->phone }}" placeholder="e.g. +63 912 345 6789">
            @error('phone') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-sso-black" style="width: auto; padding: 14px 40px;">Update Profile</button>
    </form>

    <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border-color);">
        <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 20px; color: #ef4444;">Danger Zone</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">Once you delete your account, there is no going back. Please be certain.</p>
        <button class="btn-sso-outline" style="color: #ef4444; border-color: #fecaca; width: auto;">Delete Account</button>
    </div>
</div>
@endsection

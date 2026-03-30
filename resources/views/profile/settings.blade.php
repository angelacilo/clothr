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

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
            <div style="position: relative; width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: #e2e8f0; flex-shrink: 0;">
                @if($user->avatar)
                    <img id="profile-preview" src="{{ asset($user->avatar) }}" alt="Profile photo" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div id="avatar-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="#94a3b8" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                    <img id="profile-preview" src="" alt="Profile photo" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                @endif
            </div>
            <div>
                <label for="avatar-upload" style="display: inline-block; padding: 8px 16px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s;">Change Photo</label>
                <input id="avatar-upload" type="file" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;" onchange="previewProfileImage(this)">
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">JPG, PNG, WebP up to 2MB</div>
                @error('avatar') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
            </div>
        </div>
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
</div>
@endsection

@section('extra_js')
<script>
    function previewProfileImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('profile-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                if (placeholder) placeholder.style.display = 'none';
                img.style.display = 'block';
                img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

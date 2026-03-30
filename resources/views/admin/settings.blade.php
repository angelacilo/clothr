@extends('layouts.admin')

@section('title', 'System Settings')
@section('subtitle', 'Configure store settings')



@section('content')
<div class="settings-container">
    <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('admin-profile')">Admin Profile</button>
        <button class="tab-btn" onclick="switchTab('store-info')">Store Info</button>
        <button class="tab-btn" onclick="switchTab('shipping')">Shipping</button>
        <button class="tab-btn" onclick="switchTab('security')">Security</button>
    </div>

    <!-- Tab 0: Admin Profile -->
    <div id="tab-admin-profile" class="settings-section active">
        <form id="admin-profile-form" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card" style="padding: 32px; max-width: 800px; position: relative;">
                <div id="profile-view-controls" style="position: absolute; top: 32px; right: 32px;">
                    <button type="button" class="btn btn-outline" onclick="enableEditMode()">
                        <i data-lucide="edit-3" style="width: 16px; margin-right: 8px;"></i> Edit Profile
                    </button>
                </div>
                <div id="profile-edit-controls" style="position: absolute; top: 32px; right: 32px; display: none; gap: 10px;">
                    <button type="button" class="btn btn-outline" onclick="disableEditMode()" style="color: #ef4444; border-color: #fca5a5;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-dark" onclick="saveAdminProfile()">
                        Save Profile
                    </button>
                </div>

                <div style="display: flex; gap: 32px; align-items: flex-start; margin-bottom: 32px;">
                    <div style="position: relative;">
                        <div style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid #f3f4f6; overflow: hidden; background: #e2e8f0;">
                            @if($admin->avatar)
                                <img id="profile-preview" src="{{ asset($admin->avatar) }}" alt="Admin" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div id="avatar-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="#94a3b8" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                </div>
                                <img id="profile-preview" src="" alt="Admin" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            @endif
                        </div>
                        <input type="file" name="avatar" id="profile-upload" style="display: none;" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewProfileImage(this)">
                        <button type="button" id="camera-btn" style="position: absolute; bottom: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-sm); display: none;" onclick="document.getElementById('profile-upload').click()">
                            <i data-lucide="camera" style="width: 16px; color: var(--text-medium);"></i>
                        </button>
                    </div>
                    <div style="flex: 1;">
                        <h3 id="display-name" style="font-size: 24px; font-weight: 800; margin-bottom: 4px;">{{ $admin->name ?? 'Admin User' }}</h3>
                        <p style="color: var(--text-medium); margin-bottom: 20px;">System Administrator</p>
                        <div style="display: flex; gap: 12px;">
                            <span style="background: #eff6ff; color: #3b82f6; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">Active Account</span>
                            <span style="background: #f3e8ff; color: #a855f7; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">Full Access</span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" id="input-name" class="form-input profile-input" value="{{ $admin->name ?? 'Admin User' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="input-email" class="form-input profile-input" value="{{ $admin->email ?? 'admin@clothr.com' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="input-phone" class="form-input profile-input" value="{{ $admin->phone ?? '+63 912 345 6789' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Admin ID</label>
                        <input type="text" class="form-input" value="CLT-ADM-{{ str_pad($admin->id, 3, '0', STR_PAD_LEFT) }}" readonly style="background-color: #f9fafb;">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 24px;">
                    <label>Professional Bio</label>
                    <textarea name="bio" id="input-bio" class="form-input profile-input" rows="4" style="resize: none;" readonly>{{ $admin->bio ?? 'Head of Operations for CLOTHR. Responsible for system oversight, inventory management, and strategic store configurations.' }}</textarea>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab 2: Store Info -->
    <div id="tab-store-info" class="settings-section">
        <div class="card" style="padding: 32px; max-width: 600px;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Store Information</h3>
            <div class="form-group">
                <label>Store Name</label>
                <input type="text" class="form-input" value="CLOTHR">
            </div>
            <div class="form-group">
                <label>Store Email</label>
                <input type="email" class="form-input" value="support@clothr.com">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <select class="form-input">
                    <option selected>PHP (₱)</option>
                    <option>USD ($)</option>
                    <option>EUR (€)</option>
                    <option>GBP (£)</option>
                </select>
            </div>
            <button class="btn btn-dark" style="margin-top: 12px; display: flex; align-items: center; gap: 10px;" onclick="showToast('Settings saved successfully')">
                <i data-lucide="save" style="width: 18px;"></i> Save Changes
            </button>
        </div>
    </div>



    <!-- Tab 4: Shipping -->
    <div id="tab-shipping" class="settings-section">
        <div class="card" style="padding: 32px; max-width: 600px;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Shipping Settings</h3>
            <div class="form-group">
                <label>Standard Shipping Rate (₱)</label>
                <input type="number" class="form-input" value="250.00">
            </div>
            <div class="form-group">
                <label>Free Shipping Threshold (₱)</label>
                <input type="number" class="form-input" value="2500.00">
            </div>
            <button class="btn btn-dark" style="margin-top: 12px; display: flex; align-items: center; gap: 10px;" onclick="showToast('Settings saved successfully')">
                <i data-lucide="save" style="width: 18px;"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Tab 5: Security -->
    <div id="tab-security" class="settings-section">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Security Settings</h3>
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 700; display: block;">Two-Factor Authentication</span>
                    <span style="font-size: 13px; color: var(--text-medium);">Add an extra layer of security to your account</span>
                </div>
                <button class="btn btn-outline" onclick="this.innerText = this.innerText === 'Enable' ? 'Disable' : 'Enable'; showToast('2FA updated')">Enable</button>
            </div>
            <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 700; display: block;">Activity Logs</span>
                    <span style="font-size: 13px; color: var(--text-medium);">View recent activity and login history</span>
                </div>
                <button class="btn btn-outline" onclick="openLogs()">View Logs</button>
            </div>
            <div style="padding: 24px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 700; display: block;">Change Password</span>
                    <span style="font-size: 13px; color: var(--text-medium);">Update your administrator password</span>
                </div>
                <button class="btn btn-outline" onclick="openChangePassword()">Update</button>
            </div>
        </div>
    </div>
</div>


@if(session('success'))
<script>
    window.addEventListener('load', function() {
        showToast('{{ session('success') }}');
    });
</script>
@endif

@if(session('error'))
<script>
    window.addEventListener('load', function() {
        const t = document.getElementById('toast');
        if (t) {
            t.style.background = '#ef4444';
            showToast('{{ session('error') }}');
            setTimeout(() => { t.style.background = '#111'; }, 3500);
        }
    });
</script>
@endif

<!-- Logs Modal -->
<div id="logsModal" class="modal-backdrop">
    <div class="modal-content" style="max-width: 600px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Activity Logs</h3>
        <div style="max-height: 400px; overflow-y: auto;">
            <div style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between;">
                <div>
                    <span style="font-weight: 600; display: block; font-size: 13px;">Admin Logged In</span>
                    <span style="font-size: 12px; color: var(--text-light);">IP: 192.168.1.1</span>
                </div>
                <span style="font-size: 12px; color: var(--text-medium);">2 hours ago</span>
            </div>
            <div style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between;">
                <div>
                    <span style="font-weight: 600; display: block; font-size: 13px;">Updated System Settings</span>
                    <span style="font-size: 12px; color: var(--text-light);">Modified Shipping Rates</span>
                </div>
                <span style="font-size: 12px; color: var(--text-medium);">5 hours ago</span>
            </div>
            <div style="padding: 12px 0; display: flex; justify-content: space-between;">
                <div>
                    <span style="font-weight: 600; display: block; font-size: 13px;">Added New Product</span>
                    <span style="font-size: 12px; color: var(--text-light);">Product ID: #PRD-9021</span>
                </div>
                <span style="font-size: 12px; color: var(--text-medium);">Yesterday</span>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <button class="btn btn-dark" onclick="closeLogs()">Close</button>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal-backdrop">
    <div class="modal-content">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Update Password</h3>
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" class="form-input">
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-input">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" class="form-input">
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
            <button class="btn btn-outline" onclick="closePassword()">Cancel</button>
            <button class="btn btn-dark" onclick="showToast('Password updated successfully'); closePassword()">Update Password</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        // Toggle Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.innerText.toLowerCase().replace(' ', '-') === tabId);
        });
        
        // Toggle Sections
        document.querySelectorAll('.settings-section').forEach(sec => {
            sec.classList.remove('active');
        });
        document.getElementById('tab-' + tabId).classList.add('active');
    }


    function enableEditMode() {
        document.getElementById('profile-view-controls').style.display = 'none';
        document.getElementById('profile-edit-controls').style.display = 'flex';
        document.getElementById('camera-btn').style.display = 'flex';
        document.querySelectorAll('.profile-input').forEach(input => {
            input.readOnly = false;
            input.style.backgroundColor = 'white';
            input.style.borderColor = '#3b82f6';
        });
    }

    function disableEditMode() {
        document.getElementById('profile-view-controls').style.display = 'block';
        document.getElementById('profile-edit-controls').style.display = 'none';
        document.getElementById('camera-btn').style.display = 'none';
        document.querySelectorAll('.profile-input').forEach(input => {
            input.readOnly = true;
            input.style.backgroundColor = '#f9fafb';
            input.style.borderColor = 'var(--border-color)';
        });
    }

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

    function saveAdminProfile() {
        // Submit the actual form
        document.getElementById('admin-profile-form').submit();
    }

    function openLogs() { document.getElementById('logsModal').style.display = 'flex'; }
    function closeLogs() { document.getElementById('logsModal').style.display = 'none'; }
    function openChangePassword() { document.getElementById('passwordModal').style.display = 'flex'; }
    function closePassword() { document.getElementById('passwordModal').style.display = 'none'; }
</script>
@endsection

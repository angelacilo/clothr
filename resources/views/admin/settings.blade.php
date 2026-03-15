@extends('layouts.admin')

@section('title', 'System Settings')
@section('subtitle', 'Configure store settings')

@section('header_left_logo')
<div style="width: 32px; height: 32px; background-color: #8b5cf6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; color: white; font-weight: 800;">C</div>
@endsection

@section('bell_badge')
<span style="position: absolute; top: -5px; right: -5px; background-color: #ef4444; color: white; font-size: 10px; padding: 2px 5px; border-radius: 10px; font-weight: 800; border: 2px solid white;">9</span>
@endsection

@section('view_store_icon')
<i data-lucide="globe" style="width: 16px;"></i>
@endsection

@section('view_store_class', 'btn-dark')

@section('floating_help')
<button style="position: fixed; bottom: 32px; right: 32px; width: 48px; height: 48px; background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-md); cursor: pointer; z-index: 100;" onclick="showToast('How can we help you?')">
    <span style="font-size: 20px; font-weight: 700; color: var(--text-dark);">?</span>
</button>
@endsection

@section('custom_sidebar')
<aside class="sidebar">
    <div style="padding: 24px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 28px; height: 28px; background-color: #8b5cf6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px;">C</div>
        <div style="display: flex; flex-direction: column;">
            <span style="font-weight: 800; font-size: 16px; letter-spacing: -0.5px;">CLOTHR</span>
            <span style="font-size: 10px; color: var(--text-medium); font-weight: 600; text-transform: uppercase;">Admin Panel</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="layout-dashboard"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Dashboard</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Sales analytics & insights</span>
            </div>
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="shopping-bag"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Orders</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Manage customers orders</span>
            </div>
        </a>
        <a href="{{ route('admin.products') }}" class="nav-item {{ request()->routeIs('admin.products') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="package"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Products</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Add, edit, and manage products</span>
            </div>
        </a>
        <a href="{{ route('admin.archive') }}" class="nav-item {{ request()->routeIs('admin.archive') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="archive"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Archive</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Archived products & categories</span>
            </div>
        </a>
        <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="bar-chart-2"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Reports</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Sales and inventory reports</span>
            </div>
        </a>
        <a href="{{ route('admin.reviews') }}" class="nav-item {{ request()->routeIs('admin.reviews') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="star"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Reviews</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Customer review & ratings</span>
            </div>
        </a>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="users"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">Users</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Manage customer accounts</span>
            </div>
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}" style="flex-direction: row; align-items: center; padding: 12px 16px;">
            <i data-lucide="settings"></i>
            <div style="display: flex; flex-direction: column; margin-left: 12px;">
                <span style="font-size: 14px; line-height: 1;">System Settings</span>
                <span style="font-size: 10px; color: var(--text-light); margin-top: 4px;">Configure store settings</span>
            </div>
        </a>
    </nav>
    
    <div class="sidebar-footer" style="padding: 24px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <div style="width: 36px; height: 36px; background-color: #a855f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">A</div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 13px; font-weight: 700; color: var(--text-dark);">Admin User</span>
                <span style="font-size: 11px; color: var(--text-medium);">admin@clothr.com</span>
            </div>
        </div>
        <a href="/" class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 8px;">
            <i data-lucide="log-out" style="width: 14px;"></i>
            Logout
        </a>
    </div>
</aside>
@endsection

@section('content')
<div class="settings-container">
    <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('categories')">Categories</button>
        <button class="tab-btn" onclick="switchTab('store-info')">Store Info</button>
        <button class="tab-btn" onclick="switchTab('shipping')">Shipping</button>
        <button class="tab-btn" onclick="switchTab('security')">Security</button>
    </div>

    <!-- Tab 1: Categories -->
    <div id="tab-categories" class="settings-section active">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-dark);">Product Categories</h3>
            <button class="btn btn-dark" onclick="openAddCategoryModal()">+ Add Category</button>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;" id="category-list">
            @php
                $categories = [
                    ['id' => 'cat1', 'name' => 'Dresses'],
                    ['id' => 'cat2', 'name' => 'Tops & Blouses'],
                    ['id' => 'cat3', 'name' => 'Bottoms'],
                    ['id' => 'cat4', 'name' => 'Outerwear'],
                    ['id' => 'cat5', 'name' => 'Accessories'],
                ];
            @endphp

            @foreach($categories as $cat)
            <div class="card" style="padding: 16px 24px; display: flex; align-items: center; gap: 20px;">
                <div style="width: 40px; height: 40px; background-color: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="box" style="color: #3b82f6; width: 20px;"></i>
                </div>
                <div style="flex: 1;">
                    <span style="font-weight: 700; color: var(--text-dark); display: block;">{{ $cat['name'] }}</span>
                    <span style="font-size: 12px; color: var(--text-light);">ID: {{ $cat['id'] }}</span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <i data-lucide="edit-3" style="width: 18px; color: var(--text-medium); cursor: pointer;" onclick="editCategory('{{ $cat['id'] }}', '{{ $cat['name'] }}')"></i>
                    <i data-lucide="trash-2" style="width: 18px; color: #ef4444; cursor: pointer;" onclick="deleteCategory('{{ $cat['name'] }}')"></i>
                </div>
            </div>
            @endforeach
        </div>
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

<!-- Category Modal -->
<div id="categoryModal" class="modal-backdrop">
    <div class="modal-content">
        <h3 id="catModalTitle" style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Add New Category</h3>
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" id="catNameInput" class="form-input" placeholder="e.g. Footwear">
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
            <button class="btn btn-outline" onclick="closeCatModal()">Cancel</button>
            <button class="btn btn-dark" onclick="saveCategory()">Save Category</button>
        </div>
    </div>
</div>

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

    function openAddCategoryModal() {
        document.getElementById('catModalTitle').innerText = 'Add New Category';
        document.getElementById('catNameInput').value = '';
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function editCategory(id, name) {
        document.getElementById('catModalTitle').innerText = 'Edit Category: ' + id;
        document.getElementById('catNameInput').value = name;
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeCatModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    function saveCategory() {
        const name = document.getElementById('catNameInput').value;
        if (!name) return alert('Please enter a category name');
        showToast('Category saved successfully');
        closeCatModal();
    }

    function deleteCategory(name) {
        if (confirm(`Are you sure you want to delete the category "${name}"?`)) {
            showToast('Category deleted');
        }
    }

    function openLogs() { document.getElementById('logsModal').style.display = 'flex'; }
    function closeLogs() { document.getElementById('logsModal').style.display = 'none'; }
    function openChangePassword() { document.getElementById('passwordModal').style.display = 'flex'; }
    function closePassword() { document.getElementById('passwordModal').style.display = 'none'; }
</script>
@endsection

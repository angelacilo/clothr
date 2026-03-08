@extends('layouts.admin')

@section('title', 'Users')
@section('subtitle', 'Manage customer accounts')

@section('content')
<div class="users-container">
    <!-- Search Bar -->
    <div class="card" style="margin-bottom: 32px; padding: 16px 24px;">
        <div style="position: relative; width: 100%;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" placeholder="Search customers..." 
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background-color: white;">
        </div>
    </div>

    <!-- User Grid -->
    <div class="user-grid">
        @php
            $sampleUsers = [
                ['name' => 'Demo User', 'email' => 'demo@clothr.com', 'spent' => '0.00', 'orders' => 0],
                ['name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com', 'spent' => '1,247.00', 'orders' => 8],
                ['name' => 'Michael Chen', 'email' => 'm.chen@example.com', 'spent' => '2,890.50', 'orders' => 15],
                ['name' => 'Emily Rodriguez', 'email' => 'emily.r@example.com', 'spent' => '567.25', 'orders' => 4],
                ['name' => 'James Wilson', 'email' => 'james.w@example.com', 'spent' => '3,456.80', 'orders' => 22],
                ['name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com', 'spent' => '892.40', 'orders' => 6],
                ['name' => 'Robert Taylor', 'email' => 'rob.taylor@example.com', 'spent' => '1,678.90', 'orders' => 11],
                ['name' => 'Amanda Lee', 'email' => 'amanda.lee@example.com', 'spent' => '4,123.60', 'orders' => 28],
                ['name' => 'David Martinez', 'email' => 'd.martinez@example.com', 'spent' => '734.20', 'orders' => 5],
            ];
        @endphp

        @foreach($sampleUsers as $user)
        <div class="card" style="padding: 24px;">
            <div class="user-card-top">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 16px; font-weight: 700; color: var(--text-dark);">{{ $user['name'] }}</span>
                    <span style="font-size: 13px; color: var(--text-medium); margin-top: 2px;">{{ $user['email'] }}</span>
                </div>
                <div style="position: relative;">
                    <i data-lucide="more-vertical" style="width: 20px; color: var(--text-light); cursor: pointer;" onclick="toggleMenu(this)"></i>
                    <div class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 24px; background: white; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: var(--shadow-md); z-index: 10; width: 140px; padding: 4px;">
                        <a href="#" style="display: block; padding: 8px 12px; font-size: 13px; text-decoration: none; color: var(--text-dark); border-radius: 4px;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">View Profile</a>
                        <a href="#" style="display: block; padding: 8px 12px; font-size: 13px; text-decoration: none; color: var(--text-dark); border-radius: 4px;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">Edit</a>
                        <a href="#" style="display: block; padding: 8px 12px; font-size: 13px; text-decoration: none; color: #ef4444; border-radius: 4px;" onmouseover="this.style.backgroundColor='#fef2f2'" onmouseout="this.style.backgroundColor='transparent'" onclick="confirmDelete('{{ $user['name'] }}')">Delete</a>
                    </div>
                </div>
            </div>
            
            <div class="user-card-bottom">
                <div class="user-stat">
                    <span style="font-size: 11px; font-weight: 500; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Total Spent</span>
                    <span style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-top: 4px;">${{ $user['spent'] }}</span>
                </div>
                <div class="user-stat">
                    <span style="font-size: 11px; font-weight: 500; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Orders</span>
                    <span style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $user['orders'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-backdrop">
    <div class="modal-content">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 12px;">Delete User</h3>
        <p style="font-size: 14px; color: var(--text-medium); margin-bottom: 24px;">Are you sure you want to delete <span id="deleteUserName" style="font-weight: 700; color: var(--text-dark);"></span>? This action cannot be undone.</p>
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn" style="background-color: #ef4444; color: white;" onclick="performDelete()">Delete User</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleMenu(el) {
        const menu = el.nextElementSibling;
        const allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(m => {
            if (m !== menu) m.style.display = 'none';
        });
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    // Close menus on click outside
    window.onclick = function(event) {
        if (!event.target.matches('[data-lucide="more-vertical"]')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
        }
    }

    function confirmDelete(name) {
        document.getElementById('deleteUserName').innerText = name;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function performDelete() {
        showToast('User deleted successfully');
        closeDeleteModal();
    }
</script>
@endsection

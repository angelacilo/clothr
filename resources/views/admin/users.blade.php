@extends('layouts.admin')

@section('title', 'Users')
@section('subtitle', 'Manage customer accounts')

@section('content')
<div class="users-container">
    <!-- Search Bar -->
    <div class="card" style="margin-bottom: 32px; padding: 16px 24px;">
        <div style="position: relative; width: 100%;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" id="userSearch" placeholder="Search customers..." oninput="filterUsers()"
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background-color: white;">
        </div>
    </div>

    <!-- User Grid -->
    <div class="user-grid" id="userGrid">
        @foreach($users as $user)
        <div class="card user-card-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" style="padding: 24px;">
            <div class="user-card-top">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #a855f7; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-size: 16px; font-weight: 700; color: var(--text-dark);">{{ $user->name }}</span>
                        <span style="font-size: 13px; color: var(--text-medium); margin-top: 2px;">{{ $user->email }}</span>
                    </div>
                </div>
                <div style="position: relative;">
                    <i data-lucide="more-vertical" style="width: 20px; color: var(--text-light); cursor: pointer;" onclick="toggleMenu(this)"></i>
                    <div class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 24px; background: white; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: var(--shadow-md); z-index: 10; width: 140px; padding: 4px;">
                        <a href="#" style="display: block; padding: 8px 12px; font-size: 13px; text-decoration: none; color: var(--text-dark); border-radius: 4px;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">View Profile</a>
                        <a href="#" style="display: block; padding: 8px 12px; font-size: 13px; text-decoration: none; color: #ef4444; border-radius: 4px;" onmouseover="this.style.backgroundColor='#fef2f2'" onmouseout="this.style.backgroundColor='transparent'" onclick="confirmDelete('{{ $user->name }}', {{ $user->id }})">Delete</a>
                    </div>
                </div>
            </div>
            
            <div class="user-card-bottom">
                <div class="user-stat">
                    <span style="font-size: 11px; font-weight: 500; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Total Spent</span>
                    <span style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-top: 4px;">₱{{ number_format($user->orders ? $user->orders->sum('total') : 0, 2) }}</span>
                </div>
                <div class="user-stat">
                    <span style="font-size: 11px; font-weight: 500; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Orders</span>
                    <span style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $user->orders ? $user->orders->count() : 0 }}</span>
                </div>
                <div class="user-stat">
                    <span style="font-size: 11px; font-weight: 500; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Joined</span>
                    <span style="font-size: 14px; font-weight: 600; color: var(--text-dark); margin-top: 4px;">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($users->isEmpty())
        <div style="text-align: center; padding: 60px 0; color: var(--text-medium);">
            <i data-lucide="users" style="width: 48px; height: 48px; color: var(--text-light); margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">No users found</h3>
            <p style="font-size: 14px;">Customer accounts will appear here when they register.</p>
        </div>
    @endif

    <div style="margin-top: 24px;">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-backdrop">
    <div class="modal-content">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 12px;">Delete User</h3>
        <p style="font-size: 14px; color: var(--text-medium); margin-bottom: 24px;">Are you sure you want to delete <span id="deleteUserName" style="font-weight: 700; color: var(--text-dark);"></span>? This action cannot be undone.</p>
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteUserForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="background-color: #ef4444; color: white;">Delete User</button>
            </form>
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

    window.onclick = function(event) {
        if (!event.target.matches('[data-lucide="more-vertical"]')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
        }
    }

    function confirmDelete(name, id) {
        document.getElementById('deleteUserName').innerText = name;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function filterUsers() {
        const q = document.getElementById('userSearch').value.toLowerCase();
        document.querySelectorAll('.user-card-item').forEach(card => {
            const name = card.getAttribute('data-name');
            const email = card.getAttribute('data-email');
            card.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
        });
    }
</script>
@endsection

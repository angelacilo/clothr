@extends('layouts.shop')

@section('extra_css')
    <style>
        .profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 40px; }
        .profile-sidebar { background: #fff; border-right: 1px solid var(--border-color); padding-right: 40px; }
        .profile-nav { display: flex; flex-direction: column; gap: 10px; }
        .profile-nav-link { padding: 12px 18px; border-radius: 8px; font-size: 15px; font-weight: 500; color: var(--text-secondary); transition: 0.2s; display: flex; align-items: center; gap: 12px; }
        .profile-nav-link:hover { background: #f9fafb; color: #000; }
        .profile-nav-link.active { background: #000; color: #fff; }
        
        .profile-header { margin-bottom: 40px; }
        .profile-title { font-size: 32px; font-weight: 800; margin-bottom: 10px; }
        
        .profile-content { background: #fff; }
        
        .order-tabs { display: flex; gap: 20px; border-bottom: 1px solid var(--border-color); margin-bottom: 30px; overflow-x: auto; padding-bottom: 2px; }
        .order-tab { padding: 12px 10px; font-size: 14px; font-weight: 600; color: var(--text-muted); cursor: pointer; white-space: nowrap; border-bottom: 2px solid transparent; }
        .order-tab:hover { color: #000; }
        .order-tab.active { color: #000; border-color: #000; }

        .order-card { border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 25px; overflow: hidden; }
        .order-card-header { background: #f9fafb; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; border-bottom: 1px solid var(--border-color); }
        .order-card-body { padding: 20px; }
        .order-item { display: flex; gap: 15px; margin-bottom: 15px; }
        .order-item img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; }
        .order-status { padding: 4px 10px; border-radius: 40px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #dcfce7; color: #166534; }
    </style>
    @yield('profile_css')
@endsection

@section('content')
<div class="container section">
    <div class="profile-layout">
        <aside class="profile-sidebar">
            <div class="profile-header">
                <span style="font-size: 14px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Account</span>
                <div class="profile-title">{{ auth()->user()->name }}</div>
            </div>
            
            <nav class="profile-nav">
                <a href="{{ route('profile.orders') }}" class="profile-nav-link {{ Route::is('profile.orders') ? 'active' : '' }}">
                    <i data-lucide="package" size="18"></i> My Orders
                </a>
                <a href="{{ route('profile.wishlist') }}" class="profile-nav-link {{ Route::is('profile.wishlist') ? 'active' : '' }}">
                    <i data-lucide="heart" size="18"></i> Wishlist
                </a>
                <a href="{{ route('profile.settings') }}" class="profile-nav-link {{ Route::is('profile.settings') ? 'active' : '' }}">
                    <i data-lucide="settings" size="18"></i> Account Settings
                </a>
                <a href="{{ route('logout') }}" class="profile-nav-link" style="margin-top: 20px; color: #ef4444;">
                    <i data-lucide="log-out" size="18"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="profile-content">
            @yield('profile_content')
        </main>
    </div>
</div>
@endsection

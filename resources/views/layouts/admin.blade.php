<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTHR Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- External Libraries for Reports -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
</head>
<body>
    <div class="admin-layout">
        @hasSection('custom_sidebar')
            @yield('custom_sidebar')
        @else
        <aside class="sidebar">
            <div class="sidebar-header">
                CLOTHR
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <i data-lucide="shopping-bag"></i>
                    Orders
                </a>
                <a href="{{ route('admin.products') }}" class="nav-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                    <i data-lucide="package"></i>
                    Products
                </a>
                <a href="{{ route('admin.categories') }}" class="nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <i data-lucide="layers"></i>
                    Categories
                </a>
                <a href="{{ route('admin.archive') }}" class="nav-item {{ request()->routeIs('admin.archive') ? 'active' : '' }}">
                    <i data-lucide="archive"></i>
                    Archive
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i data-lucide="bar-chart-2"></i>
                    Reports
                </a>
                <a href="{{ route('admin.reviews') }}" class="nav-item {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <i data-lucide="star"></i>
                    Reviews
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i data-lucide="users"></i>
                    Users
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i data-lucide="settings"></i>
                    Settings
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="logout-btn">
                    <i data-lucide="log-out" style="margin-right: 12px; height: 18px; width: 18px;"></i>
                    Logout
                    <i data-lucide="arrow-right" style="margin-left: auto; width: 16px; height: 16px;"></i>
                </a>
            </div>
        </aside>
        @endif
        
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    @yield('header_left_logo', '')
                    <span style="font-weight: 800; font-size: 20px;">CLOTHR</span>
                    <div class="page-info">
                        <span class="page-title">@yield('title', 'Dashboard')</span>
                        <span class="page-subtitle" style="display: block;">@yield('subtitle', 'Sales analytics & insights')</span>
                    </div>
                </div>
                
                <div class="header-right">
                    <div style="position: relative;">
                        <i data-lucide="bell" class="header-icon" onclick="toggleNotifications()"></i>
                        @yield('bell_badge', '')
                    </div>
                    <a href="/" target="_blank" class="view-store-btn @yield('view_store_class', '')" style="display: flex; align-items: center; gap: 8px;">
                        @yield('view_store_icon', '')
                        View Store
                    </a>
                    
                    <a href="{{ route('admin.settings') }}" class="user-profile" style="text-decoration: none; cursor: pointer;">
                        <div class="avatar">
                            <img src="{{ auth()->user()->avatar ?? 'https://i.pravatar.cc/150?u=admin' }}" alt="Admin">
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ auth()->user()->name ?? 'Admin User' }}</span>
                            <span class="user-email">{{ auth()->user()->email ?? 'admin@clothr.com' }}</span>
                        </div>
                    </a>
                </div>
            </header>
            
            <div class="content-body">
                @yield('content')
            </div>

            <!-- Floating Help Button -->
            @yield('floating_help', '')
        </main>
    </div>

    <div id="toast" class="toast">Settings saved successfully</div>
    
    <script>
        lucide.createIcons();

        function showToast(message) {
            const toast = document.getElementById('toast');
            if (message) toast.innerText = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function toggleNotifications() {
            showToast('No new notifications');
        }
    </script>
    @yield('scripts')
</body>
</html>

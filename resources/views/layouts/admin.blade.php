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
    
    <style>
        .bell-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 10px;
            border: 2px solid white;
            z-index: 10;
        }
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 40px;
            right: 0;
            width: 360px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 100;
            overflow: hidden;
            text-align: left;
        }
        .notification-dropdown.show {
            display: block;
        }
        .notif-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-header h3 {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            color: var(--text-dark);
        }
        .mark-all-btn {
            font-size: 12px;
            font-weight: 600;
            color: #3b82f6;
            background: none;
            border: none;
            cursor: pointer;
        }
        .mark-all-btn:hover {
            text-decoration: underline;
        }
        .notif-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .notif-item {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        .notif-item:hover {
            background: #f9fafb;
        }
        .notif-item.unread {
            background: #eff6ff;
        }
        .notif-item.unread:hover {
            background: #e0f2fe;
        }
        .notif-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-medium);
            flex-shrink: 0;
        }
        .notif-item.unread .notif-icon {
            background: #dbeafe;
            color: #2563eb;
        }
        .notif-content {
            flex-grow: 1;
        }
        .notif-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .notif-message {
            font-size: 12px;
            color: var(--text-medium);
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .notif-time {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 500;
        }
        .notif-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-medium);
            font-size: 14px;
        }
    </style>

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
                <a href="{{ route('admin.wishlists') }}" class="nav-item {{ request()->routeIs('admin.wishlists') ? 'active' : '' }}">
                    <i data-lucide="heart"></i>
                    Wishlists
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
                <form method="POST" action="{{ route('logout') }}" style="display: block;">
                    @csrf
                    <button type="submit" class="logout-btn" style="width:100%; text-align:left; background:none; border:none; cursor:pointer;" onclick="localStorage.removeItem('clothr_cart');">
                        <i data-lucide="log-out" style="margin-right: 12px; height: 18px; width: 18px;"></i>
                        Logout
                        <i data-lucide="arrow-right" style="margin-left: auto; width: 16px; height: 16px;"></i>
                    </button>
                </form>
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
                        <div style="position: relative; cursor: pointer; padding: 4px;" onclick="toggleNotifications(event)">
                            <i data-lucide="bell" class="header-icon" style="width: 28px; height: 28px; color: var(--text-dark);"></i>
                            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                                <span class="bell-badge" id="bellBadge" style="top: 0px; right: 0px;">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                            @else
                                <span class="bell-badge" id="bellBadge" style="display: none; top: 0px; right: 0px;">0</span>
                            @endif
                        </div>
                        
                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notif-header">
                                <h3>Notifications</h3>
                                <button class="mark-all-btn" id="markAllBtn" onclick="markAllAsRead(event)" style="{{ (!isset($unreadNotificationCount) || $unreadNotificationCount == 0) ? 'display: none;' : '' }}">Mark all as read</button>
                            </div>
                            <div class="notif-list" id="notificationList">
                                <!-- Loaded via AJAX -->
                                <div class="notif-empty">Loading...</div>
                            </div>
                        </div>
                        
                        @yield('bell_badge', '')
                    </div>
                    <a href="/" target="_blank" class="view-store-btn @yield('view_store_class', '')" style="display: flex; align-items: center; gap: 8px;">
                        @yield('view_store_icon', '')
                        View Store
                    </a>
                    
                    <a href="{{ route('admin.settings') }}" class="user-profile" style="text-decoration: none; cursor: pointer;">
                        <div class="avatar">
                            @if(optional(auth()->user())->avatar)
                                <img src="{{ asset(auth()->user()->avatar) }}" alt="Admin" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <div style="width: 100%; height: 100%; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#94a3b8" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ optional(auth()->user())->name ?? 'Admin User' }}</span>
                            <span class="user-email">{{ optional(auth()->user())->email ?? 'admin@clothr.com' }}</span>
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

        @if(session('success'))
            showToast("{{ session('success') }}");
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}");
        @endif

        function toggleNotifications(e) {
            if(e) e.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            
            if(dropdown.classList.contains('show')) {
                fetchNotifications();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('[data-lucide="bell"]').parentElement;
            if (dropdown.classList.contains('show') && !dropdown.contains(e.target) && !bell.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function fetchNotifications() {
            fetch('/admin/notifications')
                .then(res => res.json())
                .then(data => {
                    renderNotifications(data);
                })
                .catch(err => console.error("Error fetching notifications", err));
        }

        function renderNotifications(data) {
            const list = document.getElementById('notificationList');
            const markAllBtn = document.getElementById('markAllBtn');
            const badge = document.getElementById('bellBadge');
            
            list.innerHTML = '';
            
            if (data.length === 0) {
                list.innerHTML = '<div class="notif-empty"><i data-lucide="bell-off" style="margin: 0 auto 12px; display: block; width: 32px; height: 32px; color: #d1d5db;"></i>No notifications yet</div>';
                markAllBtn.style.display = 'none';
                badge.style.display = 'none';
                lucide.createIcons();
                return;
            }
            
            markAllBtn.style.display = 'block';
            
            let unreadCount = 0;
            
            data.forEach(item => {
                if(!item.is_read) unreadCount++;
                
                const icon = item.type === 'new_order' ? 'shopping-bag' : 'user';
                const unreadClass = item.is_read ? '' : 'unread';
                
                const html = `
                    <div class="notif-item ${unreadClass}" onclick="markAsRead(${item.id}, '${item.link}')">
                        <div class="notif-icon">
                            <i data-lucide="${icon}" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">${item.title}</div>
                            <div class="notif-message">${item.message}</div>
                            <div class="notif-time">${item.created_at}</div>
                        </div>
                    </div>
                `;
                list.insertAdjacentHTML('beforeend', html);
            });
            
            lucide.createIcons();
            
            if(unreadCount > 0) {
                badge.style.display = 'block';
                badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
            } else {
                badge.style.display = 'none';
                markAllBtn.style.display = 'none';
            }
        }

        function markAsRead(id, link) {
            fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                window.location.href = link;
            });
        }

        function markAllAsRead(e) {
            if(e) e.stopPropagation();
            fetch(`/admin/notifications/read-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                document.getElementById('bellBadge').style.display = 'none';
                document.getElementById('markAllBtn').style.display = 'none';
                document.querySelectorAll('.notif-item').forEach(item => item.classList.remove('unread'));
            });
        }

        // Poll every 30 seconds
        setInterval(function() {
            // Only update badge silently, don't redraw dropdown to avoid interrupting user
            fetch('/admin/notifications')
                .then(res => res.json())
                .then(data => {
                    const unreadCount = data.filter(n => !n.is_read).length;
                    const badge = document.getElementById('bellBadge');
                    
                    if(unreadCount > 0) {
                        badge.style.display = 'block';
                        badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                        document.getElementById('markAllBtn').style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                    
                    // If dropdown is open, refresh it quietly
                    if(document.getElementById('notificationDropdown').classList.contains('show')) {
                        renderNotifications(data);
                    }
                })
                .catch(() => {});
        }, 30000);

    </script>
    @yield('scripts')
</body>
</html>

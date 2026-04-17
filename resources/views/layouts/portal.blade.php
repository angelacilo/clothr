<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | CLOTHR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0c0c0c;
            --card-bg: #141414;
            --card-border: #262626;
            --text-primary: #ffffff;
            --text-muted: #888888;
            --accent-green: #22c55e;
            --accent-orange: #f59e0b;
            --accent-blue: #3b82f6;
            --accent-red: #ef4444;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-primary); 
            margin: 0; 
            padding-top: 80px;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Navbar */
        .navbar { 
            height: 80px; 
            background-color: rgba(12, 12, 12, 0.8); 
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border); 
            position: fixed; top: 0; left: 0; right: 0; 
            display: flex; align-items: center; justify-content: space-between; 
            padding: 0 2.5rem; z-index: 1000; 
        }
        .nav-brand { font-size: 1.5rem; font-weight: 800; text-decoration: none; color: white; letter-spacing: -1px; }
        .nav-title { font-weight: 600; color: var(--accent-green); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px; }
        .nav-right { display: flex; align-items: center; gap: 2rem; }
        
        .nav-info { 
            display: flex; align-items: center; gap: 8px; 
            font-size: 0.85rem; color: var(--text-muted);
            background: #1a1a1a; padding: 6px 16px; border-radius: 99px;
            border: 1px solid var(--card-border);
        }
        .nav-info span:first-child { color: #fff; font-weight: 600; }
        
        .btn-logout { 
            background: transparent; border: 1px solid var(--card-border); color: var(--text-muted); 
            padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; 
        }
        .btn-logout:hover { color: #fff; border-color: #444; }

        /* General UI Elements */
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .card { background-color: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; }
        .grid { display: grid; gap: 1.5rem; }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-main { grid-template-columns: 2fr 1fr; }

        @media (max-width: 1024px) { .grid-main { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .grid-cols-4, .grid-cols-3 { grid-template-columns: 1fr; } .navbar { padding: 0 1rem; } }

        .stat-card { display: flex; flex-direction: column; padding: 1.5rem; position: relative; overflow: hidden; }
        .stat-label { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2.25rem; font-weight: 700; color: #fff; }
        .stat-meta { font-size: 0.8rem; margin-top: 0.5rem; font-weight: 600; }

        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.25rem; font-weight: 700; color: #fff; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; border-bottom: 1px solid var(--card-border); text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 16px 12px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 0.95rem; }
        
        .order-id { color: var(--accent-green); text-decoration: none; font-weight: 600; border-bottom: 1.5px solid rgba(34, 197, 94, 0.2); transition: all 0.2s; }
        .order-id:hover { border-bottom-color: var(--accent-green); }

        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-green { background-color: var(--accent-green); color: #000; }
        .btn-outline { background-color: transparent; border: 1.5px solid var(--card-border); color: #fff; }
        .btn-outline:hover { background-color: #1a1a1a; border-color: #444; }
        .btn-dark { background-color: #222; color: #fff; border: 1px solid #333; }
        .btn-dark:hover { background-color: #2a2a2a; }
        .btn-red { background-color: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-sm { padding: 6px 12px; font-size: 0.85rem; }

        .badge { padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid transparent; }
        .badge-green { background-color: rgba(34, 197, 94, 0.1); color: var(--accent-green); border-color: rgba(34, 197, 94, 0.2); }
        .badge-orange { background-color: rgba(245, 158, 11, 0.1); color: var(--accent-orange); border-color: rgba(245, 158, 11, 0.2); }
        .badge-blue { background-color: rgba(59, 130, 246, 0.1); color: var(--accent-blue); border-color: rgba(59, 130, 246, 0.2); }
        .badge-red { background-color: rgba(239, 68, 68, 0.1); color: var(--accent-red); border-color: rgba(239, 68, 68, 0.2); }

        /* Rider Items */
        .rider-item { display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #1a1a1a; border: 1px solid var(--card-border); border-radius: 12px; margin-bottom: 0.5rem; transition: transform 0.2s; }
        .rider-item:hover { transform: translateX(4px); border-color: #333; }
        .rider-info { display: flex; align-items: center; gap: 14px; }
        .rider-avatar { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; }
        .rider-name { font-weight: 600; color: #fff; font-size: 1rem; line-height: 1.2; }
        .rider-meta { font-size: 0.8rem; color: var(--text-muted); }

        /* Delivery Card */
        .delivery-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.75rem; margin-bottom: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .delivery-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; }
        .delivery-title { font-weight: 700; font-size: 1.15rem; color: #fff; letter-spacing: -0.2px; }
        .delivery-meta { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.75rem; }
        .delivery-footer { display: flex; gap: 12px; }

        /* Availability Toggle */
        .toggle-container { display: flex; align-items: center; gap: 12px; }
        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        input:checked + .slider { background-color: var(--accent-green); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* Tabs */
        .tab { padding: 8px 16px; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; font-weight: 600; border-radius: 8px; transition: all 0.2s; }
        .tab:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .tab-active { color: var(--accent-green); background: rgba(34, 197, 94, 0.1); }

        /* Modals */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 1.5rem; }
        .modal { background-color: #141414; border: 1px solid var(--card-border); border-radius: 20px; width: 100%; max-width: 480px; padding: 2.5rem; position: relative; }
        .modal-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.5px; color: #fff; }
        .modal-close { position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.5rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.6rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px 14px; background-color: #0c0c0c; border: 1px solid var(--card-border); border-radius: 10px; color: white; box-sizing: border-box; font-size: 1rem; transition: border-color 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--accent-green); }

        /* Alert Banners */
        .alert-banner { padding: 12px 20px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; }
        .alert-success { background: rgba(34, 197, 94, 0.1); color: var(--accent-green); border: 1px solid rgba(34, 197, 94, 0.2); }
        .alert-error { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.2); }

    </style>
</head>
<body>
    <nav class="navbar">
        <div style="display: flex; flex-direction: column;">
            <a href="@yield('brand_route', '#')" class="nav-brand">CLOTHR</a>
            <span class="nav-title">@yield('portal_title')</span>
        </div>
        
        <div class="nav-right">
            @yield('nav_extra')
            <div class="nav-user">
                {{ auth()->user()->name }}
                @yield('badge')
            </div>
            <form action="@yield('logout_route')" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert-banner alert-success">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-banner alert-error">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;">&times;</button>
            </div>
        @endif

        @yield('content')
    </div>

    @yield('modals')

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        }
        document.onkeydown = function(evt) {
            if (evt.key === "Escape") {
                document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
            }
        };
    </script>
</body>
</html>

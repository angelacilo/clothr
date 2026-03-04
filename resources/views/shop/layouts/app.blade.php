<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop') - CLOTHR</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>
    <header style="display:flex;align-items:center;justify-content:space-between;padding:16px 2rem;border-bottom:1px solid #eee;">
        <a href="/" style="font-size:1.5rem;font-weight:800;letter-spacing:2px;text-decoration:none;color:#111;">CLOTHR</a>
        <nav style="display:flex;gap:1.5rem;align-items:center;">
            <a href="/products" style="text-decoration:none;color:#333;">Shop</a>
            <a href="/cart" style="text-decoration:none;color:#333;">Cart</a>
            <a href="/orders" style="text-decoration:none;color:#333;">My Orders</a>
            @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#333;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="text-decoration:none;color:#333;">Login</a>
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer style="border-top:1px solid #eee;padding:2rem;text-align:center;color:#888;font-size:13px;">
        &copy; {{ date('Y') }} CLOTHR. All rights reserved.
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Login | CLOTHR</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0c0c0c;
            --card-bg: #141414;
            --accent-primary: #22c55e; /* Green for Courier */
            --text-primary: #ffffff;
            --text-muted: #a0a0a0;
            --border-color: #262626;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-image: radial-gradient(circle at 20% 30%, rgba(34, 197, 94, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 70%, rgba(34, 197, 94, 0.08) 0%, transparent 50%);
        }

        .login-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            letter-spacing: -1px;
            margin-bottom: 0.25rem;
            background: linear-gradient(to bottom right, #fff, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--accent-primary);
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.6rem;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background-color: #0c0c0c;
            color: #fff;
            font-size: 1rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .checkbox-group input {
            width: auto;
            accent-color: var(--accent-primary);
        }

        button {
            width: 100%;
            padding: 16px;
            background-color: var(--accent-primary);
            color: #000;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        button:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        .error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .alert {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">CLOTHR</div>
        <div class="subtitle">Courier Portal</div>

        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('courier.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@courier.com" required autofocus>
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="display: inline; margin: 0; color: var(--text-muted);">Keep me signed in</label>
            </div>

            <button type="submit">Sign in to Courier Portal</button>
        </form>
    </div>
</body>
</html>

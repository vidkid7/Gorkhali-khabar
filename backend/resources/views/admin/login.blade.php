<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · {{ config('app.name', 'Gorkhali') }} Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('icons/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('icons/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ url('icons/logo.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            font-family: 'Mukta', 'Noto Sans Devanagari', system-ui, sans-serif;
            background: linear-gradient(135deg, #0b1220 0%, #1e293b 100%);
            color: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%; max-width: 400px;
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .login-card h1 {
            margin: 0 0 6px; font-size: 22px; color: #dc2626;
        }
        .login-card .sub { color: #94a3b8; font-size: 13px; margin-bottom: 24px; }
        .login-card .brand-mark {
            display: block; margin: 0 auto 16px;
            max-width: 280px; width: 100%; height: auto;
        }
        .form-row { margin-bottom: 14px; }
        .form-row label {
            display: block; font-size: 12px;
            color: #94a3b8; margin-bottom: 6px; font-weight: 500;
        }
        .form-row input {
            width: 100%; padding: 10px 12px;
            background: #0b1220; color: #e5e7eb;
            border: 1px solid #1f2937; border-radius: 6px;
            font-size: 14px; font-family: inherit;
        }
        .form-row input:focus {
            outline: none; border-color: #dc2626;
        }
        .btn-primary {
            width: 100%; padding: 11px;
            background: #dc2626; color: white;
            border: none; border-radius: 6px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; margin-top: 8px;
        }
        .btn-primary:hover { background: #b91c1c; }
        .errors { color: #f87171; font-size: 12px; margin-top: 6px; }
        .alert {
            padding: 10px 12px; border-radius: 6px;
            margin-bottom: 14px; font-size: 13px;
        }
        .alert-error {
            background: rgba(220,38,38,0.15); color: #f87171;
            border: 1px solid rgba(220,38,38,0.3);
        }
    </style>
        <img src="{{ url('icons/logo.png') }}" alt="{{ config('app.name', 'Gorkhali Khabar') }}" class="brand-mark">
<body>
    <div class="login-card">
        <h1>{{ config('app.name', 'Gorkhali') }}</h1>
        <p class="sub">Admin Panel · कृपया लगइन गर्नुहोस्</p>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}">
            @csrf
            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="form-row">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-primary">Sign in</button>
        </form>
    </div>
</body>
</html>
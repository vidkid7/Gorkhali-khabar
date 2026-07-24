@php
    $adminPath = trim((string) env('ADMIN_PATH', 'gorkhali-admin'), '/');
@endphp
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gorkhali Khabar') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('icons/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('icons/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ url('icons/logo.png') }}">
    <style>
        :root {
            --primary: #07579b;
            --primary-hover: #06477f;
            --accent: #d60000;
            --bg: #f5f7f9;
            --surface: #ffffff;
            --text: #101418;
            --muted: #5f6b76;
            --border: #dce2e8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            font-family: 'Noto Sans Devanagari', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .card {
            max-width: 520px; width: 100%;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08), 0 3px 8px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            padding: 40px;
            text-align: center;
        }
        .logo {
            width: 80px; height: 80px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2260bf 0%, #1a4fa0 100%);
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 14px;
        }
        h1 { margin: 0 0 8px; font-size: 28px; }
        p.tagline { color: var(--muted); margin: 0 0 28px; font-size: 14px; }
        .btn {
            display: inline-block;
            padding: 11px 22px;
            background: var(--primary);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.15s;
        }
        .btn:hover { background: var(--primary-hover); }
        .meta { margin-top: 24px; font-size: 12px; color: var(--muted); }
        .status {
            display: inline-block; padding: 3px 10px;
            border-radius: 999px; background: rgba(21,128,61,0.1);
            color: #15803d; font-size: 11px; font-weight: 600;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ url('icons/logo.png') }}" alt="{{ config('app.name', 'Gorkhali Khabar') }}" class="brand-mark" style="max-width:340px;width:100%;height:auto;margin:0 auto 18px;display:block;">
        <p class="tagline">नेपालको विश्वसनीय अनलाइन समाचार पोर्टल</p>
        <div class="status">● Service Operational</div>
        <a href="{{ url($adminPath) }}" class="btn">Admin Panel →</a>
        <div class="meta">
            API: <code>/api/health</code> · Admin: <code>/{{ $adminPath }}</code>
        </div>
    </div>
</body>
</html>
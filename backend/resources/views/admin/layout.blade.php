@php
    // Honor the staff user's saved admin theme preference (light / dark).
    $userTheme = auth()->user()?->admin_theme ?: 'light';
@endphp
<!DOCTYPE html>
<html lang="ne" data-theme="{{ $userTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ config('app.name', 'Gorkhali') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('icons/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('icons/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ url('icons/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Noto+Serif+Devanagari:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ─── Brand tokens — keep in sync with src/app/globals.css ─── */
        :root,
        [data-theme="light"] {
            --bg: #f5f7f9;
            --surface: #ffffff;
            --surface-alt: #edf1f4;
            --text: #101418;
            --muted: #5f6b76;
            --muted-foreground: #7f8b96;
            --border: #dce2e8;
            --border-strong: #bcc7d1;
            --primary: #07579b;
            --primary-hover: #06477f;
            --primary-light: rgba(7, 87, 155, 0.09);
            --accent: #d60000;
            --accent-hover: #980000;
            --accent-light: rgba(214, 0, 0, 0.08);
            --success: #15803d;
            --success-light: rgba(21, 128, 61, 0.08);
            --warning: #b45309;
            --warning-light: rgba(180, 83, 9, 0.08);
            --error: #dc2626;
            --error-light: rgba(220, 38, 38, 0.08);
            --info: #2563eb;
            --info-light: rgba(37, 99, 235, 0.08);
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.03);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
            --shadow-lg: 0 12px 30px rgba(0,0,0,0.08), 0 3px 8px rgba(0,0,0,0.04);
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
        }
        [data-theme="dark"] {
            --bg: #0c121a;
            --surface: #151e29;
            --surface-alt: #1d2935;
            --text: #f5f7fa;
            --muted: #a3afba;
            --muted-foreground: #7e8a96;
            --border: #2d3a49;
            --border-strong: #435366;
            --primary: #3b9ad9;
            --primary-hover: #5fb1e3;
            --primary-light: rgba(59, 154, 217, 0.16);
            --accent: #ff4d4d;
            --accent-hover: #f87171;
            --accent-light: rgba(255, 77, 77, 0.12);
            --success: #22c55e;
            --success-light: rgba(34, 197, 94, 0.1);
            --warning: #fbbf24;
            --warning-light: rgba(251, 191, 36, 0.1);
            --error: #ef4444;
            --error-light: rgba(239, 68, 68, 0.1);
            --info: #38bdf8;
            --info-light: rgba(56, 189, 248, 0.1);
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Noto Sans Devanagari', 'DM Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, h4 { font-family: 'Noto Serif Devanagari', 'Noto Sans Devanagari', serif; margin: 0 0 12px; }
        a { color: inherit; text-decoration: none; }

        .admin-shell { display: flex; min-height: 100vh; }

        /* ─── Sidebar ─── */
        .sidebar {
            width: 260px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            position: fixed; top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            display: flex; flex-direction: column;
            z-index: 20;
        }
        .sidebar-brand {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand .mark {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 18px;
            box-shadow: var(--shadow-sm);
        }
        .sidebar-brand .brand-logo {
            width: 100%;
            max-width: 220px;
            height: auto;
            display: block;
            padding: 4px 0;
        }
        .sidebar-brand {
            padding: 14px 14px 6px !important;
            border-bottom: 1px solid var(--border);
            display: flex !important;
            justify-content: center !important;
        }
        .sidebar-brand .text { line-height: 1.1; }
        .sidebar-brand .text .name { font-weight: 700; font-size: 15px; }
        .sidebar-brand .text .sub { font-size: 11px; color: var(--muted); }

        .nav-section { padding: 12px 12px; flex: 1; }
        .nav-group-title {
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--muted-foreground); padding: 12px 12px 6px;
            font-weight: 600;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: var(--radius-md);
            color: var(--muted);
            font-size: 13px; font-weight: 500;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: var(--surface-alt); color: var(--text); }
        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }
        .nav-item .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--muted-foreground); opacity: 0.4; }
        .nav-item.active .dot { background: var(--primary); opacity: 1; }

        /* ─── Topbar ─── */
        .main { margin-left: 260px; flex: 1; min-width: 0; }
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 10;
            box-shadow: var(--shadow-xs);
        }
        .topbar h2 { margin: 0; font-size: 17px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .topbar-btn {
            padding: 7px 11px; background: var(--surface-alt);
            border: 1px solid var(--border); border-radius: var(--radius-md);
            color: var(--text); font-size: 13px;
            display: inline-flex; align-items: center; gap: 6px;
            cursor: pointer; transition: background 0.15s;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--border); }
        .user-chip {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 12px 6px 6px;
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: 999px; font-size: 13px;
        }
        .user-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .role-badge {
            font-size: 10px; padding: 2px 7px; border-radius: 4px;
            background: var(--primary); color: white;
            text-transform: uppercase; letter-spacing: 0.05em;
            font-weight: 600;
        }
        .role-badge.role-admin { background: var(--accent); }
        .role-badge.role-editor { background: var(--info); }
        .role-badge.role-author { background: var(--success); }

        /* ─── Content ─── */
        .content { padding: 24px; max-width: 1320px; margin: 0 auto; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-xs);
        }
        .card-title { margin: 0 0 14px; font-size: 15px; font-weight: 600; color: var(--text); }

        /* ─── Stats ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .stat-card:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
        .stat-card::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--primary);
        }
        .stat-card.accent::before { background: var(--accent); }
        .stat-card.success::before { background: var(--success); }
        .stat-card.warning::before { background: var(--warning); }
        .stat-card.info::before { background: var(--info); }
        .stat-card .label {
            font-size: 11px; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.05em;
            font-weight: 600;
        }
        .stat-card .value {
            font-size: 26px; font-weight: 700; margin-top: 6px;
            color: var(--text); line-height: 1.1;
        }
        .stat-card .delta {
            font-size: 11px; color: var(--muted-foreground); margin-top: 4px;
        }

        /* ─── Tables ─── */
        table {
            width: 100%; border-collapse: collapse; font-size: 13px;
        }
        table th, table td {
            padding: 11px 12px; text-align: left;
            border-bottom: 1px solid var(--border);
        }
        table th {
            font-size: 11px; text-transform: uppercase;
            letter-spacing: 0.05em; color: var(--muted);
            font-weight: 600; background: var(--surface-alt);
        }
        table tbody tr:hover td { background: var(--surface-alt); }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            font-size: 13px; font-weight: 500;
            cursor: pointer; transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
        }
        .btn:hover { background: var(--surface-alt); }
        .btn-primary {
            background: var(--primary); border-color: var(--primary); color: white;
        }
        .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .btn-danger {
            background: var(--error); border-color: var(--error); color: white;
        }
        .btn-danger:hover { opacity: 0.9; }
        .btn-success {
            background: var(--success); border-color: var(--success); color: white;
        }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* ─── Forms ─── */
        .form-row { margin-bottom: 14px; }
        .form-row label {
            display: block; font-size: 12px;
            color: var(--muted); margin-bottom: 6px;
            font-weight: 600;
        }
        .form-row input, .form-row select, .form-row textarea {
            width: 100%; padding: 9px 12px;
            background: var(--surface); color: var(--text);
            border: 1px solid var(--border); border-radius: var(--radius-md);
            font-size: 13px; font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-row input:focus, .form-row select:focus, .form-row textarea:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .form-row textarea { min-height: 110px; resize: vertical; }
        .form-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
        }
        .form-actions { display: flex; gap: 10px; margin-top: 20px; }
        .form-group { border: 1px solid var(--border); border-radius: 8px; padding: 12px; margin: 0; }
        .form-group legend { font-size: 12px; font-weight: 600; color: var(--muted); padding: 0 6px; }
        .form-checkboxes { display: flex; flex-wrap: wrap; gap: 8px 20px; }
        .checkbox-row { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; }
        .checkbox-row input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }

        /* ─── Alerts ─── */
        .alert { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: var(--success-light); color: var(--success); border: 1px solid var(--success); }

        /* ─── Badges ─── */
        .badge {
            display: inline-block; padding: 2px 9px; border-radius: 4px;
            font-size: 11px; font-weight: 600;
        }
        .badge-success { background: var(--success-light); color: var(--success); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-danger  { background: var(--error-light); color: var(--error); }
        .badge-info    { background: var(--info-light); color: var(--info); }
        .badge-muted   { background: var(--surface-alt); color: var(--muted); }

        /* ─── Misc ─── */
        .toolbar {
            display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
            margin-bottom: 14px;
        }
        .pagination { display: flex; gap: 4px; margin-top: 14px; }
        .pagination a, .pagination span {
            padding: 6px 11px; background: var(--surface-alt);
            border-radius: 4px; font-size: 12px;
        }
        .pagination .active { background: var(--primary); color: white; }
        .alert {
            padding: 11px 14px; border-radius: var(--radius-md);
            margin-bottom: 14px; font-size: 13px;
        }
        .alert-success { background: var(--success-light); color: var(--success); border: 1px solid var(--success); }
        .alert-error   { background: var(--error-light); color: var(--error); border: 1px solid var(--error); }
        .alert-warning { background: var(--warning-light); color: var(--warning); border: 1px solid var(--warning); }
        .errors-list { color: var(--error); font-size: 12px; margin-top: 6px; }
        .errors-list li { margin-left: 16px; }
        .empty {
            text-align: center; padding: 40px 20px;
            color: var(--muted); font-size: 13px;
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px; gap: 12px; flex-wrap: wrap;
        }
        .page-header h1 { margin: 0; font-size: 22px; }
        .breadcrumb { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
        .breadcrumb a { color: var(--muted); }
        .breadcrumb a:hover { color: var(--text); }
        .inline-form { display: inline; }

        @media (max-width: 880px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.2s; }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: inline-flex !important; }
        }
        .menu-toggle {
            display: none; padding: 6px 11px;
            background: var(--surface-alt); border: 1px solid var(--border);
            color: var(--text); border-radius: 4px; cursor: pointer;
        }

        /* ─── Dashboard-specific ─── */
        .dash-hero {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; padding: 18px 22px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white; border-radius: var(--radius-lg);
            margin-bottom: 18px;
            box-shadow: var(--shadow-md);
        }
        .dash-hero h1 { color: white; margin: 0 0 4px; font-size: 22px; }
        .dash-hero .hero-sub { color: rgba(255,255,255,0.88); font-size: 13px; margin: 0; }
        .dash-hero .hero-right { display: flex; gap: 8px; }
        .hero-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 14px; border-radius: var(--radius-md);
            background: rgba(255,255,255,0.15); color: white;
            border: 1px solid rgba(255,255,255,0.25);
            font-size: 13px; font-weight: 500;
            transition: background 0.15s;
            text-decoration: none;
        }
        .hero-btn:hover { background: rgba(255,255,255,0.25); }
        .hero-btn-primary { background: white; color: var(--primary); border-color: white; }
        .hero-btn-primary:hover { background: rgba(255,255,255,0.92); color: var(--primary); }

        .stats-grid { gap: 14px; margin-bottom: 14px; }
        .stat-card-lg { padding: 20px 22px; display: flex; align-items: center; gap: 14px; }
        .stat-card-lg .stat-icon {
            font-size: 28px; line-height: 1; flex-shrink: 0;
            opacity: 0.85;
        }
        .stat-card-lg .stat-body { flex: 1; min-width: 0; }
        .stat-card-lg .value { font-size: 26px; }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .mini-stat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            transition: border-color 0.15s, background 0.15s;
        }
        .mini-stat:hover { border-color: var(--primary); background: var(--surface-alt); }
        .mini-stat-value {
            font-size: 20px; font-weight: 700;
            color: var(--text); line-height: 1.1;
        }
        .mini-stat-label {
            font-size: 11px; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.04em;
            font-weight: 600; margin-top: 3px;
        }
        .mini-stat-sub { font-size: 11px; color: var(--muted-foreground); margin-top: 2px; }

        .dash-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .dash-row-3 { grid-template-columns: 1fr 1fr 1fr; }
        @media (max-width: 1080px) { .dash-row-3 { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 880px)  { .dash-row, .dash-row-3 { grid-template-columns: 1fr; } }

        .chart-card { display: flex; flex-direction: column; }
        .chart-card-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 8px;
        }
        .chart-card-header .card-title { margin-bottom: 0; }
        .legend-dot {
            display: inline-block; width: 9px; height: 9px;
            border-radius: 50%; margin-right: 4px;
        }
        .chart-svg {
            width: 100%; height: auto;
            margin-top: 6px;
        }
        .kpi-pill {
            background: var(--primary-light); color: var(--primary);
            padding: 4px 10px; border-radius: 999px;
            font-size: 13px; font-weight: 700;
        }
        .kpi-pill small { font-weight: 500; opacity: 0.7; }

        .hourly-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 4px; margin-top: 8px;
        }
        .hourly-cell {
            aspect-ratio: 1 / 1;
            border-radius: 4px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-size: 9px;
            background: rgba(56, 189, 248, calc(0.15 + var(--intensity) * 0.85));
            color: var(--text);
            border: 1px solid var(--border);
            position: relative;
        }
        .hourly-cell .hourly-val { font-weight: 700; font-size: 11px; }
        .hourly-cell .hourly-lbl { color: var(--muted); font-size: 9px; margin-top: 2px; }

        .pipeline { display: flex; flex-direction: column; gap: 12px; margin-top: 12px; }
        .pipeline-row { }
        .pipeline-row-head {
            display: flex; justify-content: space-between;
            font-size: 12px; margin-bottom: 4px;
        }
        .pipeline-label { color: var(--text); font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .pipeline-count { color: var(--muted); font-weight: 600; }
        .pipeline-bar { height: 8px; background: var(--surface-alt); border-radius: 4px; overflow: hidden; }
        .pipeline-bar-fill { height: 100%; transition: width 0.4s; border-radius: 4px; }
        .pipeline-label .dot { width: 8px; height: 8px; border-radius: 50%; }
        .delta-badge {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 11px; font-weight: 600; margin-left: 6px;
        }
        .delta-badge.up   { background: var(--success-light); color: var(--success); }
        .delta-badge.down { background: var(--error-light);   color: var(--error); }

        .cat-list { display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
        .cat-row {
            display: grid; grid-template-columns: 14px 1fr 100px 36px;
            align-items: center; gap: 10px;
            font-size: 13px;
        }
        .cat-swatch { width: 12px; height: 12px; border-radius: 3px; }
        .cat-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cat-bar { background: var(--surface-alt); height: 6px; border-radius: 3px; overflow: hidden; }
        .cat-bar-fill { display: block; height: 100%; background: var(--primary); border-radius: 3px; }
        .cat-count { font-weight: 700; text-align: right; color: var(--text); }

        .top-table th, .top-table td { padding: 10px 12px; }
        .rank {
            display: inline-flex; width: 24px; height: 24px;
            align-items: center; justify-content: center;
            border-radius: 50%; font-size: 11px; font-weight: 700;
            background: var(--surface-alt); color: var(--muted);
        }
        .rank-1 { background: linear-gradient(135deg, #fbbf24, #d97706); color: white; }
        .rank-2 { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: white; }
        .rank-3 { background: linear-gradient(135deg, #d97706, #92400e); color: white; }
        .top-title { font-weight: 500; color: var(--text); }
        .cat-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 2px 8px; border-radius: 4px;
            background: color-mix(in srgb, var(--c, var(--primary)) 15%, transparent);
            color: var(--c, var(--primary));
            font-size: 12px; font-weight: 500;
        }
        .cat-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--c, var(--primary)); }
        .view-count { font-weight: 700; color: var(--text); }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px; margin-top: 8px;
        }
        .quick-action {
            display: flex; align-items: center; gap: 12px;
            padding: 12px; border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            text-decoration: none;
            transition: border-color 0.15s, transform 0.15s;
        }
        .quick-action:hover { border-color: var(--primary); transform: translateY(-1px); }
        .qa-icon {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-md);
            font-size: 20px;
            flex-shrink: 0;
        }
        .qa-title { font-weight: 600; font-size: 13px; }
        .qa-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }

        .queue-summary {
            display: flex; justify-content: space-between; align-items: baseline;
            padding: 10px 12px; background: var(--surface-alt); border-radius: var(--radius-md);
            margin-bottom: 10px;
        }
        .queue-row {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            margin-bottom: 6px;
            color: var(--text); text-decoration: none;
            transition: border-color 0.15s;
        }
        .queue-row:hover { border-color: var(--primary); }
        .queue-icon { font-size: 16px; }
        .queue-label { flex: 1; font-size: 13px; }
        .queue-count {
            font-weight: 700; padding: 2px 10px; border-radius: 999px;
            font-size: 11px;
        }
        .queue-count.has { background: var(--error-light); color: var(--error); }
        .queue-count.none { background: var(--success-light); color: var(--success); }

        .activity-feed { list-style: none; padding: 0; margin: 8px 0 0; max-height: 320px; overflow-y: auto; }
        .activity-item {
            display: flex; gap: 10px; padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 8px; height: 8px; border-radius: 50%;
            margin-top: 7px; flex-shrink: 0;
            background: var(--muted);
        }
        .activity-dot.activity-create { background: var(--success); }
        .activity-dot.activity-update { background: var(--info); }
        .activity-dot.activity-delete { background: var(--error); }
        .activity-dot.activity-publish,
        .activity-dot.activity-archive { background: var(--warning); }
        .activity-body { flex: 1; min-width: 0; }
        .activity-line { font-size: 13px; }
        .activity-verb {
            color: var(--muted); margin: 0 6px;
            font-size: 12px;
        }
        .activity-entity { font-weight: 600; color: var(--text); }

        .health-list { display: flex; flex-direction: column; gap: 8px; margin-top: 6px; }
        .health-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; background: var(--surface-alt);
            border-radius: var(--radius-sm);
        }
        .health-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .health-dot.ok  { background: var(--success); box-shadow: 0 0 0 4px var(--success-light); }
        .health-dot.bad { background: var(--error);   box-shadow: 0 0 0 4px var(--error-light); }
        .health-label { flex: 1; font-size: 13px; font-weight: 500; }
        .health-status {
            font-size: 11px; font-weight: 700; padding: 2px 8px;
            border-radius: 4px;
        }
        .health-status.ok  { background: var(--success-light); color: var(--success); }
        .health-status.bad { background: var(--error-light);   color: var(--error); }

        .mini-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0; border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .mini-row:last-child { border-bottom: none; }
        .mini-row-title {
            flex: 1; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            text-decoration: none;
        }
        .mini-row-count {
            font-weight: 700; color: var(--primary);
            font-size: 12px;
            background: var(--primary-light);
            padding: 1px 8px; border-radius: 999px;
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        @auth
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ url('icons/logo.png') }}"
                     alt="{{ config('app.name', 'गोर्खाली खबर') }}" class="brand-logo">
            </div>
            @include('admin.partials.sidebar', ['user' => auth()->user()])
        </aside>
        @endauth

        <div class="main">
            @auth
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:10px;">
                    <button class="menu-toggle" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                    <h2>@yield('heading', 'Dashboard')</h2>
                </div>
                <div class="topbar-actions">
                    <a href="{{ route('admin.profile.show') }}" class="user-chip" style="text-decoration:none;">
                        <span class="user-avatar">{{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                        {{ auth()->user()->name }}
                        @php $role = strtolower(auth()->user()->role); @endphp
                        <span class="role-badge role-{{ $role }}">{{ auth()->user()->role }}</span>
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="topbar-btn">Logout</button>
                    </form>
                </div>
            </header>
            @endauth

            <main class="content">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
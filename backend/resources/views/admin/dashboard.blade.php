@extends('admin.layout')

@section('title', 'Dashboard')
@section('heading', 'ड्यासबोर्ड')

@php
    // ---- Derived deltas ----
    $monthDelta = $stats['published_last_month'] > 0
        ? round((($stats['published_this_month'] - $stats['published_last_month']) / max($stats['published_last_month'], 1)) * 100)
        : ($stats['published_this_month'] > 0 ? 100 : 0);
    $dayDelta = $stats['page_views_yesterday'] > 0
        ? round((($stats['page_views_today'] - $stats['page_views_yesterday']) / max($stats['page_views_yesterday'], 1)) * 100)
        : ($stats['page_views_today'] > 0 ? 100 : 0);

    $totalContent = $stats['published_articles'] + $stats['draft_articles'] + $stats['pending_articles'] + $stats['archived_articles'];

    // ---- Card grid ----
    $primaryCards = [
        ['label' => 'कुल लेखहरू',       'value' => $stats['total_articles'],         'sub' => number_format($stats['published_articles']).' प्रकाशित',  'accent' => '',        'icon' => '📰', 'href' => route('admin.articles.index')],
        ['label' => 'कुल भ्युज',        'value' => $stats['total_views'],             'sub' => 'आज: '.number_format($stats['page_views_today']),         'accent' => 'info',   'icon' => '👁', 'href' => route('admin.analytics.index')],
        ['label' => 'टिप्पणीहरू',       'value' => $stats['pending_comments'],       'sub' => number_format($stats['approved_comments']).' स्वीकृत',  'accent' => 'warning','icon' => '💬', 'href' => route('admin.comments.index')],
        ['label' => 'प्रयोगकर्ता',      'value' => $stats['recent_users'],           'sub' => number_format($stats['total_users']).' कुल · '.number_format($stats['staff_users']).' स्टाफ', 'accent' => 'success','icon' => '👥', 'href' => route('admin.users.index')],
        ['label' => 'बुकमार्क',          'value' => $stats['total_bookmarks'],        'sub' => 'पाठकहरूबाट सुरक्षित',                                'accent' => 'accent', 'icon' => '🔖', 'href' => route('admin.bookmarks.index')],
        ['label' => 'वर्गहरू',            'value' => $stats['total_categories'],       'sub' => number_format($stats['total_tags']).' ट्याग',              'accent' => '',        'icon' => '📂', 'href' => route('admin.categories.index')],
    ];

    $secondaryCards = [
        ['label' => 'प्रकाशित',          'value' => $stats['published_articles'],  'sub' => 'Published',   'accent' => 'success', 'href' => route('admin.articles.index').'?status=PUBLISHED'],
        ['label' => 'पेन्डिङ समीक्षा',   'value' => $stats['pending_articles'],    'sub' => 'Pending',    'accent' => 'warning', 'href' => route('admin.articles.index').'?status=PENDING'],
        ['label' => 'ड्राफ्ट',            'value' => $stats['draft_articles'],      'sub' => 'Drafts',     'accent' => 'muted',   'href' => route('admin.articles.index').'?status=DRAFT'],
        ['label' => 'फिचर्ड',            'value' => $stats['featured_articles'],   'sub' => 'Featured',   'accent' => 'info',    'href' => route('admin.articles.index')],
        ['label' => 'रिल्स',             'value' => $stats['total_reels'],        'sub' => 'Reels',      'accent' => '',        'href' => route('admin.reels.index')],
        ['label' => 'पेज भ्युज (३० दिन)', 'value' => $stats['page_views_month'], 'sub' => 'Last 30d',   'accent' => 'accent', 'href' => route('admin.analytics.index')],
    ];

    // ---- Trend chart data ----
    $maxHourly = max($hourlyBuckets ?: [1]);
    $maxPublish = max($publishBuckets ?: [1]);
    $maxDaily = max(array_column($dailyViews, 'total') ?: [1]);

    // Helper for chart bar
    $fmtDay = fn ($d) => \Carbon\Carbon::parse($d)->format('M d');
@endphp

@section('content')

    {{-- ─── Hero header ─── --}}
    <div class="dash-hero">
        <div class="hero-left">
            <h1>नमस्कार, {{ auth()->user()->name }} 🙏</h1>
            <p class="hero-sub">
                आज <strong>{{ \Carbon\Carbon::now()->format('d M Y, l') }}</strong> ·
                अन्तिम २४ घण्टामा <strong>{{ number_format($stats['page_views_today']) }}</strong> पेज भ्युज,
                <strong>{{ number_format($stats['new_users_today']) }}</strong> नयाँ प्रयोगकर्ता,
                र <strong>{{ $stats['pending_comments'] }}</strong> पेन्डिङ टिप्पणी।
            </p>
        </div>
        <div class="hero-right">
            <a href="{{ route('admin.articles.create') }}" class="hero-btn hero-btn-primary">
                <span>＋</span> नयाँ लेख
            </a>
            <a href="{{ route('admin.media.index') }}" class="hero-btn">
                <span>🖼</span> मिडिया
            </a>
        </div>
    </div>

    {{-- ─── Primary stat grid ─── --}}
    <div class="stats-grid">
        @foreach ($primaryCards as $card)
            <a href="{{ $card['href'] }}" class="stat-card stat-card-lg {{ $card['accent'] }}" style="text-decoration:none;">
                <div class="stat-icon">{{ $card['icon'] }}</div>
                <div class="stat-body">
                    <div class="label">{{ $card['label'] }}</div>
                    <div class="value">{{ number_format($card['value']) }}</div>
                    <div class="delta">{{ $card['sub'] }}</div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ─── Secondary stat row ─── --}}
    <div class="mini-stats">
        @foreach ($secondaryCards as $card)
            <a href="{{ $card['href'] }}" class="mini-stat" style="text-decoration:none;">
                <div class="mini-stat-value">{{ number_format($card['value']) }}</div>
                <div class="mini-stat-label">{{ $card['label'] }}</div>
                <div class="mini-stat-sub">{{ $card['sub'] }}</div>
            </a>
        @endforeach
    </div>

    {{-- ─── Charts row ─── --}}
    <div class="dash-row">
        {{-- Page views trend (last 14 days, line+bar combo) --}}
        <div class="card chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="card-title">पेज भ्युज · दैनिक</h3>
                    <small style="color:var(--muted);">अन्तिम १४ दिन</small>
                </div>
                <div style="display:flex;align-items:center;gap:14px;">
                    <span class="legend-dot" style="background:var(--info);"></span>
                    <small style="color:var(--muted);">Page Views</small>
                    <span class="kpi-pill">{{ number_format(array_sum(array_column($dailyViews, 'total'))) }} <small>कुल</small></span>
                </div>
            </div>

            @php
                $maxVal = max(1, max(array_column($dailyViews ?: [['total'=>1]], 'total')));
                $chartW = 760;
                $chartH = 200;
                $pad = 24;
                $w = $chartW - $pad * 2;
                $h = $chartH - $pad * 2;
                $count = max(1, count($dailyViews));
                $barW = $w / $count - 4;
            @endphp

            <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="chart-svg" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--info)" stop-opacity="0.85"/>
                        <stop offset="100%" stop-color="var(--info)" stop-opacity="0.2"/>
                    </linearGradient>
                </defs>
                {{-- gridlines --}}
                @for ($g = 0; $g <= 4; $g++)
                    @php $y = $pad + ($h / 4) * $g; @endphp
                    <line x1="{{ $pad }}" y1="{{ $y }}" x2="{{ $chartW - $pad }}" y2="{{ $y }}"
                          stroke="var(--border)" stroke-dasharray="2 4" stroke-width="1"/>
                    <text x="{{ $pad - 6 }}" y="{{ $y + 4 }}" font-size="10" fill="var(--muted)" text-anchor="end">
                        {{ number_format($maxVal * (1 - $g / 4)) }}
                    </text>
                @endfor
                {{-- bars --}}
                @foreach ($dailyViews as $i => $row)
                    @php
                        $barH = $h * ($row['total'] / $maxVal);
                        $x = $pad + ($w / $count) * $i + 2;
                        $y = $pad + $h - $barH;
                    @endphp
                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $barH }}"
                          fill="url(#barGrad)" rx="3">
                        <title>{{ \Carbon\Carbon::parse($row['day'])->format('M d') }}: {{ $row['total'] }}</title>
                    </rect>
                @endforeach
                {{-- x-axis labels (sparse) --}}
                @if (count($dailyViews) > 0)
                    @foreach (array_chunk($dailyViews, max(1, ceil(count($dailyViews) / 7)), true) as $chunk)
                        @php
                            $idx = array_key_first($chunk);
                            $row = $dailyViews[$idx];
                        @endphp
                        <text x="{{ $pad + ($w / $count) * $idx + $barW / 2 }}"
                              y="{{ $chartH - 6 }}" font-size="10" fill="var(--muted)" text-anchor="middle">
                            {{ \Carbon\Carbon::parse($row['day'])->format('M d') }}
                        </text>
                    @endforeach
                @endif
            </svg>
        </div>

        {{-- Hourly heatmap --}}
        <div class="card chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="card-title">आजको घण्टाको वितरण</h3>
                    <small style="color:var(--muted);">Hourly views · today</small>
                </div>
                <span class="kpi-pill">{{ number_format(array_sum($hourlyBuckets)) }} <small>भ्युज</small></span>
            </div>
            <div class="hourly-grid">
                @for ($h = 0; $h < 24; $h++)
                    @php
                        $val = $hourlyBuckets[$h] ?? 0;
                        $intensity = $maxHourly > 0 ? $val / $maxHourly : 0;
                    @endphp
                    <div class="hourly-cell" style="--intensity: {{ $intensity }};" title="{{ sprintf('%02d:00', $h) }} · {{ $val }}">
                        <span class="hourly-val">{{ $val }}</span>
                        <span class="hourly-lbl">{{ sprintf('%02d', $h) }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- ─── Content pipeline + Categories donut ─── --}}
    <div class="dash-row">
        {{-- Content pipeline funnel --}}
        <div class="card">
            <h3 class="card-title">सामग्री पाइपलाइन</h3>
            <p style="color:var(--muted);font-size:12px;margin:0 0 14px;">
                लेख स्थिति अनुसार वर्गीकरण
            </p>
            @php
                $pipeline = [
                    ['label' => 'प्रकाशित', 'count' => $stats['published_articles'], 'color' => 'var(--success)', 'pct' => 0],
                    ['label' => 'पेन्डिङ',   'count' => $stats['pending_articles'],   'color' => 'var(--warning)', 'pct' => 0],
                    ['label' => 'ड्राफ्ट',    'count' => $stats['draft_articles'],     'color' => 'var(--muted)',   'pct' => 0],
                    ['label' => 'संग्रहित',  'count' => $stats['archived_articles'],  'color' => 'var(--border-strong)', 'pct' => 0],
                ];
                $pubPct = $totalContent > 0 ? round($stats['published_articles'] / $totalContent * 100) : 0;
                $pipeline[0]['pct'] = $pubPct;
                $pipeline[1]['pct'] = $totalContent > 0 ? round($stats['pending_articles'] / $totalContent * 100) : 0;
                $pipeline[2]['pct'] = $totalContent > 0 ? round($stats['draft_articles'] / $totalContent * 100) : 0;
                $pipeline[3]['pct'] = $totalContent > 0 ? round($stats['archived_articles'] / $totalContent * 100) : 0;
            @endphp
            <div class="pipeline">
                @foreach ($pipeline as $step)
                    <div class="pipeline-row">
                        <div class="pipeline-row-head">
                            <span class="pipeline-label">
                                <span class="dot" style="background:{{ $step['color'] }};"></span>
                                {{ $step['label'] }}
                            </span>
                            <span class="pipeline-count">{{ number_format($step['count']) }} · {{ $step['pct'] }}%</span>
                        </div>
                        <div class="pipeline-bar">
                            <div class="pipeline-bar-fill" style="width:{{ $step['pct'] }}%;background:{{ $step['color'] }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="color:var(--muted);font-size:12px;">प्रकाशित दर</div>
                    <div style="font-size:22px;font-weight:700;color:var(--success);">{{ $pubPct }}%</div>
                </div>
                <div style="text-align:right;">
                    <div style="color:var(--muted);font-size:12px;">यस महिना थपिए</div>
                    <div style="font-size:22px;font-weight:700;color:var(--info);">
                        {{ number_format($stats['published_this_month']) }}
                        <span class="delta-badge {{ $monthDelta >= 0 ? 'up' : 'down' }}">
                            {{ $monthDelta >= 0 ? '↑' : '↓' }} {{ abs($monthDelta) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top categories donut --}}
        <div class="card">
            <h3 class="card-title">शीर्ष वर्गहरू</h3>
            <p style="color:var(--muted);font-size:12px;margin:0 0 14px;">प्रकाशित लेख अनुसार</p>
            @if ($topCategories->isEmpty())
                <div class="empty">वर्गहरू छैनन्।</div>
            @else
                @php
                    $maxCat = max($topCategories->max('articles_count') ?? 0, 1);
                @endphp
                <div class="cat-list">
                    @foreach ($topCategories as $cat)
                        <div class="cat-row">
                            <span class="cat-swatch" style="background:{{ $cat->color ?? '#07579b' }};"></span>
                            <span class="cat-name" title="{{ $cat->name }}">{{ \Illuminate\Support\Str::limit($cat->name, 24) }}</span>
                            <span class="cat-bar">
                                <span class="cat-bar-fill" style="width:{{ ($cat->articles_count / $maxCat) * 100 }}%;"></span>
                            </span>
                            <span class="cat-count">{{ $cat->articles_count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Top articles + Quick actions ─── --}}
    <div class="dash-row">
        <div class="card">
            <h3 class="card-title">शीर्ष लेखहरू · भ्युज अनुसार</h3>
            @if ($topArticles->isEmpty())
                <div class="empty">अहिलेसम्म कुनै प्रकाशित लेख छैन।</div>
            @else
                <table class="top-table">
                    <thead>
                        <tr><th>#</th><th>शीर्षक</th><th>वर्ग</th><th>भ्युज</th></tr>
                    </thead>
                    <tbody>
                    @foreach ($topArticles as $i => $article)
                        <tr>
                            <td><span class="rank rank-{{ $i + 1 }}">{{ $i + 1 }}</span></td>
                            <td>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="top-title">
                                    {{ \Illuminate\Support\Str::limit($article->title ?? $article->title_en ?? '—', 60) }}
                                </a>
                                <small style="color:var(--muted);display:block;">{{ $article->author?->name ?? '—' }} · {{ optional($article->created_at)->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if ($article->category)
                                    <span class="cat-pill" style="--c:{{ $article->category->color ?? '#07579b' }};">
                                        <span class="dot"></span>{{ \Illuminate\Support\Str::limit($article->category->name, 16) }}
                                    </span>
                                @else — @endif
                            </td>
                            <td><span class="view-count">{{ number_format($article->view_count ?? 0) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card">
            <h3 class="card-title">द्रुत कार्यहरू</h3>
            <div class="quick-actions">
                <a href="{{ route('admin.articles.create') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--primary-light);color:var(--primary);">＋</span>
                    <div>
                        <div class="qa-title">नयाँ लेख</div>
                        <div class="qa-sub">Article लेख्नुहोस्</div>
                    </div>
                </a>
                <a href="{{ route('admin.media.index') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--info-light);color:var(--info);">🖼</span>
                    <div>
                        <div class="qa-title">मिडिया अपलोड</div>
                        <div class="qa-sub">तस्वीर/भिडियो</div>
                    </div>
                </a>
                <a href="{{ route('admin.breaking-news.index') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--error-light);color:var(--error);">📡</span>
                    <div>
                        <div class="qa-title">ब्रेकिङ न्युज</div>
                        <div class="qa-sub">तत्काल समाचार</div>
                    </div>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--success-light);color:var(--success);">⚙</span>
                    <div>
                        <div class="qa-title">सेटिङ</div>
                        <div class="qa-sub">साइट कन्फिगरेसन</div>
                    </div>
                </a>
                <a href="{{ route('admin.users.index') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--warning-light);color:var(--warning);">👥</span>
                    <div>
                        <div class="qa-title">प्रयोगकर्ता</div>
                        <div class="qa-sub">खाता व्यवस्थापन</div>
                    </div>
                </a>
                <a href="{{ route('admin.audit-log.index') }}" class="quick-action">
                    <span class="qa-icon" style="background:var(--surface-alt);color:var(--muted);">📋</span>
                    <div>
                        <div class="qa-title">अडिट लग</div>
                        <div class="qa-sub">गतिविधि इतिहास</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- ─── Pending queue + Activity feed ─── --}}
    <div class="dash-row">
        <div class="card">
            <h3 class="card-title">⚠ कार्य आवश्यक</h3>
            @php
                $queueItems = [
                    ['label' => 'पेन्डिङ टिप्पणी',     'count' => $pendingQueue['pending_comments'],  'href' => route('admin.comments.index'), 'icon' => '💬'],
                    ['label' => 'समीक्षा बाँकी लेख',  'count' => $pendingQueue['pending_articles'],  'href' => route('admin.articles.index').'?status=PENDING', 'icon' => '📝'],
                    ['label' => 'निष्क्रिय खाता',       'count' => $pendingQueue['inactive_users'],    'href' => route('admin.users.index'),    'icon' => '🚫'],
                    ['label' => 'अप्रमाणित इमेल',       'count' => $pendingQueue['unverified_emails'], 'href' => route('admin.users.index'),    'icon' => '✉'],
                    ['label' => 'ड्राफ्ट लेख',          'count' => $pendingQueue['draft_articles'],    'href' => route('admin.articles.index').'?status=DRAFT', 'icon' => '📄'],
                ];
                $queueTotal = array_sum(array_column($queueItems, 'count'));
            @endphp
            <div class="queue-summary">
                <span style="color:var(--muted);">कुल कार्य बाँकी</span>
                <span style="font-size:24px;font-weight:700;color:var(--{{ $queueTotal > 0 ? 'error' : 'success' }});">
                    {{ $queueTotal }}
                </span>
            </div>
            @foreach ($queueItems as $q)
                <a href="{{ $q['href'] }}" class="queue-row">
                    <span class="queue-icon">{{ $q['icon'] }}</span>
                    <span class="queue-label">{{ $q['label'] }}</span>
                    <span class="queue-count {{ $q['count'] > 0 ? 'has' : 'none' }}">{{ $q['count'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="card">
            <h3 class="card-title">हालैका गतिविधि</h3>
            @if ($recentActivity->isEmpty())
                <div class="empty">कुनै गतिविधि छैन।</div>
            @else
                <ul class="activity-feed">
                    @foreach ($recentActivity as $log)
                        <li class="activity-item">
                            <span class="activity-dot activity-{{ strtolower($log->action) }}"></span>
                            <div class="activity-body">
                                <div class="activity-line">
                                    <strong>{{ $log->admin?->name ?? 'System' }}</strong>
                                    <span class="activity-verb">{{ ucfirst(strtolower($log->action)) }}</span>
                                    <span class="activity-entity">{{ $log->entity }}</span>
                                </div>
                                <small style="color:var(--muted);font-size:11px;">
                                    {{ $log->created_at?->diffForHumans() ?? '—' }}
                                </small>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.audit-log.index') }}" class="btn" style="margin-top:12px;width:100%;justify-content:center;">
                    सबै अडिट लग हेर्नुहोस् →
                </a>
            @endif
        </div>
    </div>

    {{-- ─── System health + Top bookmarks + Top commenters ─── --}}
    <div class="dash-row dash-row-3">
        <div class="card">
            <h3 class="card-title">⚙ प्रणाली स्वास्थ्य</h3>
            <div class="health-list">
                @foreach ($health as $check)
                    <div class="health-row">
                        <span class="health-dot {{ $check['ok'] ? 'ok' : 'bad' }}"></span>
                        <span class="health-label">{{ $check['label'] }}</span>
                        <span class="health-status {{ $check['ok'] ? 'ok' : 'bad' }}">
                            {{ $check['ok'] ? 'OK' : 'FAIL' }}
                            @if (isset($check['ms']))
                                <small style="opacity:0.7;margin-left:6px;">{{ $check['ms'] }}ms</small>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">🔖 शीर्ष बुकमार्क गरिएका लेख</h3>
            @if ($topBookmarked->isEmpty())
                <div class="empty">अहिलेसम्म बुकमार्क छैन।</div>
            @else
                @foreach ($topBookmarked as $b)
                    <div class="mini-row">
                        <span style="color:var(--accent);">🔖</span>
                        <a href="{{ route('admin.articles.edit', $b->id) }}" class="mini-row-title">
                            {{ \Illuminate\Support\Str::limit($b->title, 50) }}
                        </a>
                        <span class="mini-row-count">{{ $b->bookmarks_count }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="card">
            <h3 class="card-title">💬 सबैभन्दा बढी टिप्पणी गर्ने</h3>
            @if ($topCommenters->isEmpty())
                <div class="empty">अहिलेसम्म टिप्पणी छैन।</div>
            @else
                @foreach ($topCommenters as $u)
                    <div class="mini-row">
                        <span class="user-avatar" style="width:28px;height:28px;font-size:11px;">
                            {{ mb_substr($u['name'] ?? '?', 0, 1) }}
                        </span>
                        <span class="mini-row-title">{{ $u['name'] ?? '—' }}</span>
                        <span class="mini-row-count">{{ $u['comments_count'] }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ─── Recent articles table ─── --}}
    <div class="card">
        <h3 class="card-title">हालैका लेखहरू</h3>
        @if ($recentArticles->isEmpty())
            <div class="empty">अहिलेसम्म कुनै लेख छैन।</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>शीर्षक</th>
                        <th>वर्ग</th>
                        <th>लेखक</th>
                        <th>स्थिति</th>
                        <th>भ्युज</th>
                        <th>मिति</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($recentArticles as $article)
                    <tr>
                        <td>
                            <a href="{{ route('admin.articles.edit', $article) }}" style="font-weight:500;">
                                {{ \Illuminate\Support\Str::limit($article->title ?? $article->title_en ?? '—', 60) }}
                            </a>
                        </td>
                        <td>
                            @if ($article->category)
                                <span class="cat-pill" style="--c:{{ $article->category->color ?? '#07579b' }};">
                                    <span class="dot"></span>{{ $article->category->name }}
                                </span>
                            @else — @endif
                        </td>
                        <td>{{ $article->author?->name ?? '—' }}</td>
                        <td>
                            @php
                                $badge = match ($article->status ?? 'DRAFT') {
                                    'PUBLISHED' => 'badge-success',
                                    'PENDING' => 'badge-warning',
                                    'ARCHIVED' => 'badge-muted',
                                    default => 'badge-muted',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $article->status ?? 'DRAFT' }}</span>
                        </td>
                        <td style="font-weight:600;">{{ number_format($article->view_count ?? 0) }}</td>
                        <td style="color:var(--muted);font-size:12px;">
                            {{ optional($article->created_at)->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
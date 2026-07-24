@extends('admin.layout')
@section('title', 'Analytics')
@section('heading', 'विश्लेषण')

@section('content')
    <div class="page-header">
        <h1>विश्लेषण</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">आज</div>
            <div class="value">{{ number_format($stats['views_today']) }}</div>
            <div class="delta">Page Views</div>
        </div>
        <div class="stat-card">
            <div class="label">७ दिन</div>
            <div class="value">{{ number_format($stats['views_7d']) }}</div>
            <div class="delta">Page Views</div>
        </div>
        <div class="stat-card">
            <div class="label">३० दिन</div>
            <div class="value">{{ number_format($stats['views_30d']) }}</div>
            <div class="delta">Page Views</div>
        </div>
        <div class="stat-card">
            <div class="label">कुल Article Views</div>
            <div class="value">{{ number_format($stats['total_views']) }}</div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">शीर्ष लेखहरू</h3>
        @if ($stats['top_articles']->isEmpty())
            <div class="empty">अहिलेसम्म कुनै डाटा छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th class="text-right">भ्युज</th></tr></thead>
                <tbody>
                @foreach ($stats['top_articles'] as $article)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit($article->title, 60) }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($article->view_count) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($daily->isNotEmpty())
        @php $maxDaily = max($daily->max('total') ?? 0, 1); @endphp
        <div class="card">
            <h3 class="card-title">दैनिक पेज भ्युज (अन्तिम १४ दिन)</h3>
            @foreach ($daily as $row)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <div style="width:90px;font-size:12px;color:var(--text-muted);">{{ \Carbon\Carbon::parse($row->day)->format('Y-m-d') }}</div>
                    <div style="flex:1;background:var(--surface-alt);height:18px;border-radius:4px;overflow:hidden;">
                        <div style="background:var(--info);height:100%;width:{{ ($row->total / $maxDaily) * 100 }}%;"></div>
                    </div>
                    <div style="width:50px;text-align:right;font-weight:600;font-size:12px;">{{ number_format($row->total) }}</div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
@extends('admin.layout')

@section('title', 'Articles')
@section('heading', 'लेखहरू')

@section('content')
    <div class="page-header">
        <h1>लेखहरू</h1>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">+ नयाँ लेख</a>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="शीर्षक खोज्नुहोस्...">
            <select name="status">
                <option value="">सबै स्थिति</option>
                @foreach (['DRAFT','PENDING','PUBLISHED','ARCHIVED'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">सबै वर्ग</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') === $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn">Filter</button>
            @if (request()->hasAny(['search','status','category']))
                <a href="{{ route('admin.articles.index') }}" class="btn">Reset</a>
            @endif
        </form>

        @if ($articles->isEmpty())
            <div class="empty">कुनै लेख फेला परेन।</div>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>
                            <a href="{{ route('admin.articles.edit', $article) }}" style="font-weight:500;">
                                {{ \Illuminate\Support\Str::limit($article->title ?? $article->title_en ?? '—', 60) }}
                            </a>
                            @if ($article->is_featured)
                                <span class="badge badge-info" style="margin-left:6px;">FEATURED</span>
                            @endif
                        </td>
                        <td>{{ $article->category?->name ?? '—' }}</td>
                        <td>{{ $article->author?->name ?? '—' }}</td>
                        <td>
                            @php
                                $badge = match ($article->status) {
                                    'PUBLISHED' => 'badge-success',
                                    'PENDING' => 'badge-warning',
                                    'ARCHIVED' => 'badge-muted',
                                    default => 'badge-muted',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $article->status }}</span>
                        </td>
                        <td>{{ number_format($article->view_count ?? 0) }}</td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($article->created_at)->format('Y-m-d') }}</td>
                        <td style="text-align:right;">
                            @if ($article->status !== 'PUBLISHED')
                                <form action="{{ route('admin.articles.publish', $article) }}" method="POST" class="inline-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Publish</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
@endsection
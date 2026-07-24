@extends('admin.layout')
@section('title', 'Bookmarks')
@section('heading', 'बुकमार्कहरू')

@section('content')
    <div class="page-header">
        <h1>बुकमार्कहरू</h1>
        <small style="color:var(--text-muted);font-size:12px;">पाठकहरूले सुरक्षित गरेका लेखहरू</small>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="card">
            <h3 class="card-title">शीर्ष बुकमार्क गरिएको लेख</h3>
            @if ($topArticles->isEmpty())
                <div class="empty">कुनै बुकमार्क छैन।</div>
            @else
                @foreach ($topArticles as $row)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;">{{ \Illuminate\Support\Str::limit($row->article?->title ?? '—', 50) }}</span>
                        <span class="badge badge-info">{{ $row->total }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="card">
            <h3 class="card-title">सबैभन्दा बढी बुकमार्क गर्ने प्रयोगकर्ता</h3>
            @if ($topUsers->isEmpty())
                <div class="empty">कुनै डाटा छैन।</div>
            @else
                @foreach ($topUsers as $row)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;">{{ $row->user?->name ?? '—' }}</span>
                        <span class="badge badge-info">{{ $row->total }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="लेख शीर्षक खोज्नुहोस्...">
            <button class="btn">Filter</button>
        </form>

        @if ($bookmarks->isEmpty())
            <div class="empty">कुनै बुकमार्क फेला परेन।</div>
        @else
            <table>
                <thead><tr><th>प्रयोगकर्ता</th><th>लेख</th><th>मिति</th><th></th></tr></thead>
                <tbody>
                @foreach ($bookmarks as $b)
                    <tr>
                        <td>{{ $b->user?->name ?? '—' }}<br><small style="color:var(--text-muted);">{{ $b->user?->email }}</small></td>
                        <td>{{ \Illuminate\Support\Str::limit($b->article?->title ?? '—', 50) }}</td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($b->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.bookmarks.destroy', $b) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $bookmarks->links() }}</div>
        @endif
    </div>
@endsection
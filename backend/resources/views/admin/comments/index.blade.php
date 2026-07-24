@extends('admin.layout')
@section('title', 'Comments')
@section('heading', 'टिप्पणीहरू')

@section('content')
    <div class="page-header">
        <h1>टिप्पणीहरू</h1>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <select name="status">
                <option value="">सबै स्थिति</option>
                @foreach (['PENDING','APPROVED','REJECTED','SPAM'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button class="btn">Filter</button>
        </form>

        @if ($comments->isEmpty())
            <div class="empty">कुनै टिप्पणी फेला परेन।</div>
        @else
            <table>
                <thead><tr><th>लेख</th><th>प्रयोगकर्ता</th><th>सामग्री</th><th>स्थिति</th><th>मिति</th><th></th></tr></thead>
                <tbody>
                @foreach ($comments as $comment)
                    <tr>
                        <td><small>{{ \Illuminate\Support\Str::limit($comment->article?->title ?? '—', 40) }}</small></td>
                        <td>{{ $comment->user?->name ?? '—' }}</td>
                        <td style="max-width:300px;">{{ \Illuminate\Support\Str::limit($comment->content, 80) }}</td>
                        <td>
                            <form action="{{ route('admin.comments.update', $comment) }}" method="POST" class="inline-form">
                                @csrf @method('PUT')
                                <select name="status" onchange="this.form.submit()" style="padding:4px 8px;font-size:11px;">
                                    @foreach (['PENDING','APPROVED','REJECTED','SPAM'] as $status)
                                        <option value="{{ $status }}" @selected($comment->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($comment->created_at)->diffForHumans() }}</td>
                        <td>
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $comments->links() }}</div>
        @endif
    </div>
@endsection
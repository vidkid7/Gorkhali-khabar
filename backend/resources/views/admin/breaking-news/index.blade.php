@extends('admin.layout')
@section('title', 'Breaking News')
@section('heading', 'ब्रेकिङ न्युज')

@section('content')
    <div class="page-header">
        <h1>ब्रेकिङ न्युज</h1>
        <a href="{{ route('admin.breaking-news.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>

    <div class="card">
        @if ($items->isEmpty())
            <div class="empty">कुनै ब्रेकिङ न्युज छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>लेख</th><th>Priority</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td><a href="{{ route('admin.breaking-news.edit', $item) }}" style="font-weight:500;">{{ $item->title }}</a></td>
                        <td><small>{{ $item->article?->title ?? '—' }}</small></td>
                        <td>{{ $item->priority ?? 0 }}</td>
                        <td><span class="badge {{ $item->is_active ? 'badge-success' : 'badge-muted' }}">{{ $item->is_active ? 'ON' : 'OFF' }}</span></td>
                        <td>
                            @if (auth()->user()->role === 'ADMIN')
                            <form action="{{ route('admin.breaking-news.destroy', $item) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $items->links() }}</div>
        @endif
    </div>
@endsection

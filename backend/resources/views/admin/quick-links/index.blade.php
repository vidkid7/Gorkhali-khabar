@extends('admin.layout')
@section('title', 'Quick Links')
@section('heading', 'द्रुत लिंक')

@section('content')
    <div class="page-header">
        <h1>द्रुत लिंक</h1>
        <a href="{{ route('admin.quick-links.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>
    <div class="card">
        @if ($links->isEmpty())
            <div class="empty">कुनै द्रुत लिंक छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>URL</th><th>Sort</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach ($links as $link)
                    <tr>
                        <td><a href="{{ route('admin.quick-links.edit', $link) }}" style="font-weight:500;">{{ $link->title }}</a></td>
                        <td><small><a href="{{ $link->url }}" target="_blank" style="color:var(--info);">{{ \Illuminate\Support\Str::limit($link->url, 40) }}</a></small></td>
                        <td>{{ $link->sort_order ?? 0 }}</td>
                        <td><span class="badge {{ $link->is_active ? 'badge-success' : 'badge-muted' }}">{{ $link->is_active ? 'ON' : 'OFF' }}</span></td>
                        <td>
                            <form action="{{ route('admin.quick-links.destroy', $link) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $links->links() }}</div>
        @endif
    </div>
@endsection
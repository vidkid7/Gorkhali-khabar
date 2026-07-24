@extends('admin.layout')
@section('title', 'Galleries')
@section('heading', 'ग्यालेरीहरू')

@section('content')
    <div class="page-header">
        <h1>ग्यालेरीहरू</h1>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">+ नयाँ ग्यालेरी</a>
    </div>

    <div class="card">
        @if ($galleries->isEmpty())
            <div class="empty">कुनै ग्यालेरी छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>Slug</th><th>प्रकाशित</th><th>मिति</th><th></th></tr></thead>
                <tbody>
                @foreach ($galleries as $gallery)
                    <tr>
                        <td><a href="{{ route('admin.galleries.edit', $gallery) }}" style="font-weight:500;">{{ $gallery->title }}</a></td>
                        <td><code style="font-size:11px;">{{ $gallery->slug }}</code></td>
                        <td><span class="badge {{ $gallery->is_published ? 'badge-success' : 'badge-muted' }}">{{ $gallery->is_published ? 'YES' : 'NO' }}</span></td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($gallery->created_at)->format('Y-m-d') }}</td>
                        <td>
                            <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $galleries->links() }}</div>
        @endif
    </div>
@endsection
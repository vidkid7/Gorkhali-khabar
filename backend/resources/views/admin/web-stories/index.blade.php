@extends('admin.layout')
@section('title', 'Web Stories')
@section('heading', 'वेब स्टोरी')

@section('content')
    <div class="page-header">
        <h1>वेब स्टोरी</h1>
        <a href="{{ route('admin.web-stories.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>

    <div class="card">
        @if ($stories->isEmpty())
            <div class="empty">कुनै वेब स्टोरी छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>वर्ग</th><th>Slides</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach ($stories as $story)
                    <tr>
                        <td><a href="{{ route('admin.web-stories.edit', $story) }}" style="font-weight:500;">{{ $story->title }}</a></td>
                        <td>{{ $story->category?->name ?? '—' }}</td>
                        <td>{{ is_array($story->slides) ? count($story->slides) : 0 }}</td>
                        <td><span class="badge {{ $story->is_active ? 'badge-success' : 'badge-muted' }}">{{ $story->is_active ? 'ON' : 'OFF' }}</span></td>
                        <td>
                            <form action="{{ route('admin.web-stories.destroy', $story) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $stories->links() }}</div>
        @endif
    </div>
@endsection
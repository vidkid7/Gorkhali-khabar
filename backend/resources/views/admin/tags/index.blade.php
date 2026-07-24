@extends('admin.layout')
@section('title', 'Tags')
@section('heading', 'ट्यागहरू')

@section('content')
    <div class="page-header">
        <h1>ट्यागहरू</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">+ नयाँ ट्याग</a>
    </div>

    <div class="card">
        @if ($tags->isEmpty())
            <div class="empty">कुनै ट्याग छैन।</div>
        @else
            <table>
                <thead><tr><th>नाम</th><th>Slug</th><th>प्रयोग</th><th></th></tr></thead>
                <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td><a href="{{ route('admin.tags.edit', $tag) }}" style="font-weight:500;">{{ $tag->name }}</a></td>
                        <td><code style="font-size:11px;">{{ $tag->slug }}</code></td>
                        <td>{{ number_format($tag->article_tags_count ?? 0) }}</td>
                        <td style="text-align:right;">
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $tags->links() }}</div>
        @endif
    </div>
@endsection
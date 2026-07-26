@extends('admin.layout')
@section('title', 'Categories')
@section('heading', 'वर्गहरू')

@section('content')
    <div class="page-header">
        <h1>वर्गहरू</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ नयाँ वर्ग</a>
    </div>

    <div class="card">
        @if ($categories->isEmpty())
            <div class="empty">कुनै वर्ग छैन।</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>नाम</th>
                        <th>Slug</th>
                        <th>Color</th>
                        <th>लेख</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}" style="font-weight:500;">{{ $category->name }}</a>
                            @if ($category->name_en)
                                <small style="color:var(--text-muted);">/ {{ $category->name_en }}</small>
                            @endif
                        </td>
                        <td><code style="font-size:11px;">{{ $category->slug }}</code></td>
                        <td>
                            @if ($category->color)
                                <span style="display:inline-block;width:18px;height:18px;border-radius:4px;background:{{ $category->color }};"></span>
                                <small style="margin-left:6px;">{{ $category->color }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($category->articles_count ?? 0) }}</td>
                        <td>
                            @if (auth()->user()->role === 'ADMIN')
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection

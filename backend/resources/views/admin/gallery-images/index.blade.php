@extends('admin.layout')
@section('title', 'Gallery Images')
@section('heading', 'ग्यालेरी तस्वीरहरू')

@section('content')
    <div class="page-header">
        <h1>ग्यालेरी तस्वीरहरू</h1>
        <a href="{{ route('admin.gallery-images.create') }}" class="btn btn-primary">+ नयाँ तस्वीर</a>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <select name="gallery">
                <option value="">सबै ग्यालेरी</option>
                @foreach ($galleries as $g)
                    <option value="{{ $g->id }}" @selected(request('gallery') === $g->id)>{{ $g->title }}</option>
                @endforeach
            </select>
            <button class="btn">Filter</button>
        </form>

        @if ($images->isEmpty())
            <div class="empty">कुनै तस्वीर छैन।</div>
        @else
            <table>
                <thead>
                    <tr><th>Preview</th><th>ग्यालेरी</th><th>Caption</th><th>Sort</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($images as $img)
                    <tr>
                        <td>
                            <img src="{{ $img->image_url }}" alt="" style="width:80px;height:50px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td>{{ $img->gallery?->title ?? '—' }}</td>
                        <td style="max-width:300px;">{{ \Illuminate\Support\Str::limit($img->caption ?? '—', 60) }}</td>
                        <td>{{ $img->sort_order ?? 0 }}</td>
                        <td>
                            <form action="{{ route('admin.gallery-images.destroy', $img) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $images->links() }}</div>
        @endif
    </div>
@endsection
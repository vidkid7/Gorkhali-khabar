@extends('admin.layout')
@section('title', 'Media')
@section('heading', 'मिडिया लाइब्रेरी')

@section('content')
    <div class="page-header">
        <h1>मिडिया लाइब्रेरी</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="toolbar">
            @csrf
            <input type="file" name="file" required accept="image/*,video/*,application/pdf">
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        @if ($media->isEmpty())
            <div class="empty">कुनै मिडिया फाइल छैन।</div>
        @else
            <table>
                <thead><tr><th>Preview</th><th>Filename</th><th>Type</th><th>Size</th><th>Uploader</th><th></th></tr></thead>
                <tbody>
                @foreach ($media as $file)
                    <tr>
                        <td>
                            @if (str_starts_with($file->mime_type ?? '', 'image/'))
                                <img src="{{ $file->url }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                            @else
                                <span style="font-size:20px;">📄</span>
                            @endif
                        </td>
                        <td><small>{{ \Illuminate\Support\Str::limit($file->filename ?? '—', 40) }}</small></td>
                        <td><code style="font-size:11px;">{{ $file->mime_type ?? '—' }}</code></td>
                        <td>{{ number_format(($file->size ?? 0) / 1024, 1) }} KB</td>
                        <td>{{ $file->uploader?->name ?? '—' }}</td>
                        <td>
                            @if (auth()->user()->role === 'ADMIN')
                            <form action="{{ route('admin.media.destroy', $file) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $media->links() }}</div>
        @endif
    </div>
@endsection

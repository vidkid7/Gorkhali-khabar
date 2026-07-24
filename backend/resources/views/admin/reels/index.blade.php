@extends('admin.layout')
@section('title', 'Reels')
@section('heading', 'रिल्स')

@section('content')
    <div class="page-header">
        <h1>रिल्स</h1>
        <a href="{{ route('admin.reels.create') }}" class="btn btn-primary">+ नयाँ रिल</a>
    </div>

    <div class="card">
        @if ($reels->isEmpty())
            <div class="empty">कुनै रिल छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>Video</th><th>Duration</th><th>Published</th><th></th></tr></thead>
                <tbody>
                @foreach ($reels as $reel)
                    <tr>
                        <td><a href="{{ route('admin.reels.edit', $reel) }}" style="font-weight:500;">{{ \Illuminate\Support\Str::limit($reel->title, 40) }}</a></td>
                        <td><small style="color:var(--text-muted);">{{ \Illuminate\Support\Str::limit($reel->video_url ?? '', 40) }}</small></td>
                        <td>{{ $reel->duration ?? '—' }}s</td>
                        <td><span class="badge {{ $reel->is_published ? 'badge-success' : 'badge-muted' }}">{{ $reel->is_published ? 'YES' : 'NO' }}</span></td>
                        <td>
                            <form action="{{ route('admin.reels.destroy', $reel) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $reels->links() }}</div>
        @endif
    </div>
@endsection
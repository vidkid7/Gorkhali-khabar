@extends('admin.layout')
@section('title', 'Matches')
@section('heading', 'म्याचहरू')

@section('content')
    <div class="page-header">
        <h1>म्याचहरू</h1>
        <a href="{{ route('admin.matches.create') }}" class="btn btn-primary">+ नयाँ म्याच</a>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <select name="tournament">
                <option value="">सबै प्रतियोगिता</option>
                @foreach ($tournaments as $t)
                    <option value="{{ $t->id }}" @selected(request('tournament') === $t->id)>{{ $t->name }}</option>
                @endforeach
            </select>
            <button class="btn">Filter</button>
        </form>

        @if ($matches->isEmpty())
            <div class="empty">कुनै म्याच छैन।</div>
        @else
            <table>
                <thead>
                    <tr><th>प्रतियोगिता</th><th>Home</th><th>Score</th><th>Away</th><th>मिति</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($matches as $m)
                    <tr>
                        <td>{{ $m->tournament?->name ?? '—' }}</td>
                        <td>{{ $m->homeTeam?->name ?? '—' }}</td>
                        <td style="font-weight:700;text-align:center;">
                            {{ $m->home_score ?? '-' }} : {{ $m->away_score ?? '-' }}
                            @if ($m->status === 'LIVE')
                                <span class="badge badge-danger" style="margin-left:6px;">LIVE</span>
                            @endif
                        </td>
                        <td>{{ $m->awayTeam?->name ?? '—' }}</td>
                        <td>{{ optional($m->match_date)->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge {{ $m->status === 'FT' ? 'badge-success' : ($m->status === 'LIVE' ? 'badge-danger' : 'badge-muted') }}">{{ $m->status }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.matches.destroy', $m) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $matches->links() }}</div>
        @endif
    </div>
@endsection
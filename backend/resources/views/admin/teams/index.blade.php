@extends('admin.layout')
@section('title', 'Teams')
@section('heading', 'टोलीहरू')

@section('content')
    <div class="page-header">
        <h1>टोलीहरू</h1>
        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">+ नयाँ टोली</a>
    </div>

    <div class="card">
        @if ($teams->isEmpty())
            <div class="empty">कुनै टोली छैन।</div>
        @else
            <table>
                <thead>
                    <tr><th>नाम</th><th>English</th><th>Home</th><th>Away</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($teams as $team)
                    <tr>
                        <td><a href="{{ route('admin.teams.edit', $team) }}" style="font-weight:500;">{{ $team->name }}</a></td>
                        <td>{{ $team->name_en ?? '—' }}</td>
                        <td>{{ $team->home_matches_count ?? 0 }}</td>
                        <td>{{ $team->away_matches_count ?? 0 }}</td>
                        <td>
                            <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $teams->links() }}</div>
        @endif
    </div>
@endsection
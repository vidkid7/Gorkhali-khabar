@extends('admin.layout')
@section('title', 'Sports')
@section('heading', 'खेलकुद')

@section('content')
    <div class="page-header">
        <h1>खेलकुद</h1>
        <a href="{{ route('admin.sports.create') }}" class="btn btn-primary">+ नयाँ प्रतियोगिता</a>
    </div>

    <div class="card">
        @if ($tournaments->isEmpty())
            <div class="empty">कुनै प्रतियोगिता छैन।</div>
        @else
            <table>
                <thead><tr><th>नाम</th><th>Sport</th><th>Season</th><th>Start</th><th>End</th><th>Matches</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach ($tournaments as $t)
                    <tr>
                        <td><a href="{{ route('admin.sports.edit', $t) }}" style="font-weight:500;">{{ $t->name }}</a></td>
                        <td>{{ $t->sport_type }}</td>
                        <td>{{ $t->season ?? '—' }}</td>
                        <td>{{ optional($t->start_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($t->end_date)->format('Y-m-d') }}</td>
                        <td>{{ $t->matches_count ?? 0 }}</td>
                        <td><span class="badge {{ $t->is_active ? 'badge-success' : 'badge-muted' }}">{{ $t->is_active ? 'ON' : 'OFF' }}</span></td>
                        <td>
                            <form action="{{ route('admin.sports.destroy', $t) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $tournaments->links() }}</div>
        @endif
    </div>
@endsection
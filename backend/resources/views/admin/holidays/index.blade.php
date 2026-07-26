@extends('admin.layout')
@section('title', 'Holidays')
@section('heading', 'बिदाहरू')

@section('content')
    <div class="page-header">
        <h1>बिदाहरू</h1>
        <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>
    <div class="card">
        @if ($holidays->isEmpty())
            <div class="empty">कुनै बिदा छैन।</div>
        @else
            <table>
                <thead><tr><th>नाम</th><th>मिति</th><th>Type</th><th>Public</th><th></th></tr></thead>
                <tbody>
                @foreach ($holidays as $holiday)
                    <tr>
                        <td><a href="{{ route('admin.holidays.edit', $holiday) }}" style="font-weight:500;">{{ $holiday->name }}</a></td>
                        <td>{{ optional($holiday->date)->format('Y-m-d') }}</td>
                        <td>{{ $holiday->type ?? '—' }}</td>
                        <td><span class="badge {{ $holiday->is_public_holiday ? 'badge-success' : 'badge-muted' }}">{{ $holiday->is_public_holiday ? 'YES' : 'NO' }}</span></td>
                        <td>
                            @if (auth()->user()->role === 'ADMIN')
                            <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $holidays->links() }}</div>
        @endif
    </div>
@endsection

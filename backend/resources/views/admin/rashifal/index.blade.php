@extends('admin.layout')
@section('title', 'Rashifal')
@section('heading', 'राशिफल')

@section('content')
    <div class="page-header">
        <h1>राशिफल</h1>
        <a href="{{ route('admin.rashifal.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>
    <div class="card">
        @if ($items->isEmpty())
            <div class="empty">कुनै राशिफल छैन।</div>
        @else
            <table>
                <thead><tr><th>राशि</th><th>मिति</th><th>भविष्यवाणी</th><th>शुभ अंक</th><th></th></tr></thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td><a href="{{ route('admin.rashifal.edit', $item) }}" style="font-weight:500;">{{ $item->sign }}</a></td>
                        <td>{{ optional($item->date)->format('Y-m-d') }}</td>
                        <td style="max-width:300px;">{{ \Illuminate\Support\Str::limit($item->prediction, 80) }}</td>
                        <td>{{ $item->lucky_number ?? '—' }}</td>
                        <td>
                            <form action="{{ route('admin.rashifal.destroy', $item) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $items->links() }}</div>
        @endif
    </div>
@endsection
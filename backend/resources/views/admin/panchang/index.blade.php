@extends('admin.layout')
@section('title', 'Panchang')
@section('heading', 'पञ्चाङ्ग')

@section('content')
    <div class="page-header">
        <h1>पञ्चाङ्ग</h1>
        <a href="{{ route('admin.panchang.create') }}" class="btn btn-primary">+ नयाँ</a>
    </div>

    <div class="card">
        @if ($entries->isEmpty())
            <div class="empty">कुनै पञ्चाङ्ग डाटा छैन।</div>
        @else
            <table>
                <thead>
                    <tr><th>AD Date</th><th>BS Date</th><th>Tithi</th><th>Nakshatra</th><th>Yoga</th><th>Festivals</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ optional($entry->ad_date)->format('Y-m-d') }}</td>
                        <td><code style="font-size:11px;">{{ $entry->bs_date ?? '—' }}</code></td>
                        <td>{{ $entry->tithi ?? '—' }}</td>
                        <td>{{ $entry->nakshatra ?? '—' }}</td>
                        <td>{{ $entry->yoga ?? '—' }}</td>
                        <td style="max-width:240px;">{{ \Illuminate\Support\Str::limit($entry->festivals ?? '—', 60) }}</td>
                        <td>
                            <form action="{{ route('admin.panchang.destroy', $entry) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $entries->links() }}</div>
        @endif
    </div>
@endsection
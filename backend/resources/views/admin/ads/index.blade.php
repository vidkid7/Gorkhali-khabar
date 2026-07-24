@extends('admin.layout')
@section('title', 'Ads')
@section('heading', 'विज्ञापन')

@section('content')
    <div class="page-header">
        <h1>विज्ञापन</h1>
        <a href="{{ route('admin.ads.create') }}" class="btn btn-primary">+ नयाँ विज्ञापन</a>
    </div>

    <div class="card">
        @if ($ads->isEmpty())
            <div class="empty">कुनै विज्ञापन छैन।</div>
        @else
            <table>
                <thead><tr><th>शीर्षक</th><th>Position</th><th>Impressions</th><th>Clicks</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach ($ads as $ad)
                    <tr>
                        <td><a href="{{ route('admin.ads.edit', $ad) }}" style="font-weight:500;">{{ $ad->title }}</a></td>
                        <td>{{ $ad->position?->name ?? '—' }}</td>
                        <td>{{ number_format($ad->impressions ?? 0) }}</td>
                        <td>{{ number_format($ad->clicks ?? 0) }}</td>
                        <td><span class="badge {{ $ad->is_active ? 'badge-success' : 'badge-muted' }}">{{ $ad->is_active ? 'ON' : 'OFF' }}</span></td>
                        <td>
                            <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $ads->links() }}</div>
        @endif
    </div>
@endsection
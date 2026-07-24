@extends('admin.layout')
@section('title', 'Newsletter')
@section('heading', 'न्यूजलेटर सदस्यता')

@section('content')
    <div class="page-header">
        <h1>न्यूजलेटर सदस्यता</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">कुल सदस्य</div>
            <div class="value">{{ number_format(\App\Models\NewsletterSubscription::count()) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">यस महिना</div>
            <div class="value">{{ number_format(\App\Models\NewsletterSubscription::where('created_at', '>=', now()->startOfMonth())->count()) }}</div>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="इमेल खोज्नुहोस्...">
            <button class="btn">Filter</button>
        </form>

        @if ($subs->isEmpty())
            <div class="empty">कुनै सदस्य छैन।</div>
        @else
            <table>
                <thead><tr><th>इमेल</th><th>नाम</th><th>मिति</th><th></th></tr></thead>
                <tbody>
                @foreach ($subs as $sub)
                    <tr>
                        <td><code style="font-size:11px;">{{ $sub->email }}</code></td>
                        <td>{{ $sub->name ?? '—' }}</td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($sub->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.newsletter.destroy', $sub) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $subs->links() }}</div>
        @endif
    </div>
@endsection
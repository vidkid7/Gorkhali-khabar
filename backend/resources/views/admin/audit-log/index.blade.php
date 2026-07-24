@extends('admin.layout')
@section('title', 'Audit Log')
@section('heading', 'अडिट लग')

@section('content')
    <div class="page-header">
        <h1>अडिट लग</h1>
        <small style="color:var(--text-muted);font-size:12px;">अन्तिम ५०० कार्यहरू</small>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Action (CREATE, UPDATE...)">
            <input type="text" name="entity" value="{{ request('entity') }}" placeholder="Entity (Article, User...)">
            <button class="btn">Filter</button>
        </form>

        @if ($logs->isEmpty())
            <div class="empty">अडिट लग खाली छ।</div>
        @else
            <table>
                <thead><tr><th>मिति</th><th>एडमिन</th><th>Action</th><th>Entity</th><th>ID</th><th>IP</th></tr></thead>
                <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->admin?->name ?? '—' }}</td>
                        <td><span class="badge badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->entity }}</td>
                        <td><code style="font-size:11px;">{{ $log->entity_id ?? '—' }}</code></td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
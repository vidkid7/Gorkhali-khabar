@extends('admin.layout')
@section('title', 'Users')
@section('heading', 'प्रयोगकर्ताहरू')

@section('content')
    <div class="page-header">
        <h1>प्रयोगकर्ताहरू</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ नयाँ प्रयोगकर्ता</a>
    </div>

    <div class="card">
        <form method="GET" class="toolbar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="नाम वा इमेल खोज्नुहोस्...">
            <select name="role">
                <option value="">सबै भूमिका</option>
                @foreach (['READER','AUTHOR','EDITOR','ADMIN'] as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <button class="btn">Filter</button>
        </form>

        @if ($users->isEmpty())
            <div class="empty">कुनै प्रयोगकर्ता फेला परेन।</div>
        @else
            <table>
                <thead><tr><th>नाम</th><th>Email</th><th>भूमिका</th><th>स्थिति</th><th>अन्तिम लगइन</th><th></th></tr></thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name ?? '—' }}</td>
                        <td><code style="font-size:11px;">{{ $user->email }}</code></td>
                        <td>
                            <span class="badge badge-{{ $user->role === 'ADMIN' ? 'danger' : ($user->role === 'EDITOR' ? 'info' : 'muted') }}">{{ $user->role }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">
                                {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ optional($user->last_login_at)->diffForHumans() ?? '—' }}</td>
                        <td style="text-align:right;">
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
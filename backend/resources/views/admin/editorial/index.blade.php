@extends('admin.layout')
@section('title', $config['heading'])
@section('heading', $config['heading'])

@section('content')
    <div class="page-header">
        <h1>{{ $config['heading'] }}</h1>
        <a class="btn btn-primary" href="{{ route('admin.'.$config['resource'].'.create') }}">+ नयाँ</a>
    </div>
    <div class="card">
        @if ($items->isEmpty())
            <div class="empty">कुनै रेकर्ड छैन।</div>
        @else
            <table>
                <thead><tr>
                    @foreach ($config['columns'] as $column)<th>{{ str_replace('_', ' ', $column) }}</th>@endforeach
                    <th>क्रिया</th>
                </tr></thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        @foreach ($config['columns'] as $column)
                            <td>
                                @if (is_bool($item->{$column}))
                                    <span class="badge {{ $item->{$column} ? 'badge-success' : 'badge-muted' }}">{{ $item->{$column} ? 'Yes' : 'No' }}</span>
                                @elseif ($item->{$column} instanceof \Carbon\CarbonInterface)
                                    {{ $item->{$column}->format('Y-m-d H:i') }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) $item->{$column}, 80) }}
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <a class="btn btn-sm" href="{{ route('admin.'.$config['resource'].'.edit', $item) }}">Edit</a>
                            <form class="inline-form" method="POST" action="{{ route('admin.'.$config['resource'].'.destroy', $item) }}" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
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

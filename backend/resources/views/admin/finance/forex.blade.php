@extends('admin.layout')
@section('title', 'Forex Rates')
@section('heading', 'विनिमय दर')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.finance.gold-silver') }}">सुन-चाँदी</a></div>

    <div class="page-header">
        <h1>विनिमय दर</h1>
    </div>

    <div class="card">
        <h3 class="card-title">नयाँ दर थप्नुहोस्</h3>
        <form method="POST" action="{{ route('admin.finance.forex.store') }}" class="form-grid">
            @csrf
            <div class="form-row">
                <label>Currency *</label>
                <input type="text" name="currency" placeholder="USD" required>
            </div>
            <div class="form-row">
                <label>Currency Name</label>
                <input type="text" name="currency_name" placeholder="US Dollar">
            </div>
            <div class="form-row">
                <label>Buy Rate *</label>
                <input type="number" step="0.0001" name="buy_rate" required>
            </div>
            <div class="form-row">
                <label>Sell Rate *</label>
                <input type="number" step="0.0001" name="sell_rate" required>
            </div>
            <div class="form-row">
                <label>Date *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-row" style="display:flex;align-items:flex-end;">
                <button class="btn btn-primary">थप्नुहोस्</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 class="card-title">हालको दर</h3>
        @if ($rates->isEmpty())
            <div class="empty">कुनै दर छैन।</div>
        @else
            <table>
                <thead><tr><th>मिति</th><th>Currency</th><th>Name</th><th>Buy</th><th>Sell</th><th></th></tr></thead>
                <tbody>
                @foreach ($rates as $rate)
                    <tr>
                        <td>{{ optional($rate->date)->format('Y-m-d') }}</td>
                        <td><strong>{{ $rate->currency }}</strong></td>
                        <td>{{ $rate->currency_name ?? '—' }}</td>
                        <td>{{ number_format($rate->buy_rate, 4) }}</td>
                        <td>{{ number_format($rate->sell_rate, 4) }}</td>
                        <td>
                            <form action="{{ route('admin.finance.forex.destroy', $rate) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $rates->links() }}</div>
        @endif
    </div>
@endsection
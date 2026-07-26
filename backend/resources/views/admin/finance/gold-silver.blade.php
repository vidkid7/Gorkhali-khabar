@extends('admin.layout')
@section('title', 'Gold-Silver')
@section('heading', 'सुन-चाँदी मूल्य')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.finance.forex') }}">विनिमय दर</a></div>

    <div class="page-header">
        <h1>सुन-चाँदी मूल्य</h1>
    </div>

    <div class="card">
        <h3 class="card-title">नयाँ मूल्य थप्नुहोस्</h3>
        <form method="POST" action="{{ route('admin.finance.gold-silver.store') }}" class="form-grid">
            @csrf
            <div class="form-row">
                <label>Metal *</label>
                <select name="metal" required>
                    <option value="GOLD">GOLD</option>
                    <option value="SILVER">SILVER</option>
                </select>
            </div>
            <div class="form-row">
                <label>Purity</label>
                <input type="text" name="purity" placeholder="24K">
            </div>
            <div class="form-row">
                <label>Unit *</label>
                <input type="text" name="unit" placeholder="tola, gram" required>
            </div>
            <div class="form-row">
                <label>Price / Unit *</label>
                <input type="number" step="0.01" name="price_per_unit" required>
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
        <h3 class="card-title">हालको मूल्य</h3>
        @if ($prices->isEmpty())
            <div class="empty">कुनै मूल्य छैन।</div>
        @else
            <table>
                <thead><tr><th>मिति</th><th>Metal</th><th>Purity</th><th>Unit</th><th>Price</th><th></th></tr></thead>
                <tbody>
                @foreach ($prices as $price)
                    <tr>
                        <td>{{ optional($price->date)->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $price->metal === 'GOLD' ? 'badge-warning' : 'badge-info' }}">{{ $price->metal }}</span></td>
                        <td>{{ $price->purity ?? '—' }}</td>
                        <td>{{ $price->unit }}</td>
                        <td>{{ number_format($price->price_per_unit, 2) }}</td>
                        <td>
                            @if (auth()->user()->role === 'ADMIN')
                            <form action="{{ route('admin.finance.gold-silver.destroy', $price) }}" method="POST" class="inline-form" onsubmit="return confirm('मेटाउने हो?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $prices->links() }}</div>
        @endif
    </div>
@endsection

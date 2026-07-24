@extends('admin.layout')
@section('title', $item->exists ? 'Edit Rashifal' : 'New Rashifal')
@section('heading', $item->exists ? 'राशिफल सम्पादन' : 'नयाँ राशिफल')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.rashifal.index') }}">राशिफल</a> / {{ $item->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $item->exists ? route('admin.rashifal.update', $item) : route('admin.rashifal.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>राशि *</label>
                    <select name="sign" required>
                        @foreach ($signs as $sign)
                            <option value="{{ $sign }}" @selected(old('sign', $item->sign) === $sign)>{{ $sign }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>मिति *</label>
                    <input type="date" name="date" value="{{ old('date', optional($item->date)->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="form-row">
                <label>भविष्यवाणी (नेपाली) *</label>
                <textarea name="prediction" rows="4" required>{{ old('prediction', $item->prediction) }}</textarea>
            </div>
            <div class="form-row">
                <label>भविष्यवाणी (English)</label>
                <textarea name="prediction_en" rows="4">{{ old('prediction_en', $item->prediction_en) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>शुभ अंक</label>
                    <input type="text" name="lucky_number" value="{{ old('lucky_number', $item->lucky_number) }}">
                </div>
                <div class="form-row">
                    <label>शुभ रंग</label>
                    <input type="text" name="lucky_color" value="{{ old('lucky_color', $item->lucky_color) }}">
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $item->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.rashifal.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
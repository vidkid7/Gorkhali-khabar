@extends('admin.layout')
@section('title', $entry->exists ? 'Edit Panchang' : 'New Panchang')
@section('heading', $entry->exists ? 'पञ्चाङ्ग सम्पादन' : 'नयाँ पञ्चाङ्ग')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.panchang.index') }}">पञ्चाङ्ग</a> / {{ $entry->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $entry->exists ? route('admin.panchang.update', $entry) : route('admin.panchang.store') }}">
        @csrf
        @if ($entry->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>AD Date *</label>
                    <input type="date" name="ad_date" value="{{ old('ad_date', optional($entry->ad_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-row">
                    <label>BS Date (विक्रम सम्वत)</label>
                    <input type="text" name="bs_date" value="{{ old('bs_date', $entry->bs_date) }}" placeholder="2081/04/15">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Tithi (तिथि)</label>
                    <input type="text" name="tithi" value="{{ old('tithi', $entry->tithi) }}">
                </div>
                <div class="form-row">
                    <label>Nakshatra (नक्षत्र)</label>
                    <input type="text" name="nakshatra" value="{{ old('nakshatra', $entry->nakshatra) }}">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Yoga (योग)</label>
                    <input type="text" name="yoga" value="{{ old('yoga', $entry->yoga) }}">
                </div>
                <div class="form-row">
                    <label>Karana (करण)</label>
                    <input type="text" name="karana" value="{{ old('karana', $entry->karana) }}">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Sunrise</label>
                    <input type="text" name="sunrise" value="{{ old('sunrise', $entry->sunrise) }}" placeholder="05:30">
                </div>
                <div class="form-row">
                    <label>Sunset</label>
                    <input type="text" name="sunset" value="{{ old('sunset', $entry->sunset) }}" placeholder="18:45">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Moonrise</label>
                    <input type="text" name="moonrise" value="{{ old('moonrise', $entry->moonrise) }}">
                </div>
                <div class="form-row">
                    <label>Moonset</label>
                    <input type="text" name="moonset" value="{{ old('moonset', $entry->moonset) }}">
                </div>
            </div>

            <div class="form-row">
                <label>Festivals / चाडपर्व</label>
                <input type="text" name="festivals" value="{{ old('festivals', $entry->festivals) }}" placeholder="Dashain, Tihar, ...">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $entry->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.panchang.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
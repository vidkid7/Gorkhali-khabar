@extends('admin.layout')
@section('title', $ad->exists ? 'Edit Ad' : 'New Ad')
@section('heading', $ad->exists ? 'विज्ञापन सम्पादन' : 'नयाँ विज्ञापन')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.ads.index') }}">विज्ञापन</a> / {{ $ad->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $ad->exists ? route('admin.ads.update', $ad) : route('admin.ads.store') }}">
        @csrf
        @if ($ad->exists) @method('PUT') @endif
        <div class="card">
            <div class="form-row">
                <label>शीर्षक *</label>
                <input type="text" name="title" value="{{ old('title', $ad->title) }}" required>
            </div>
            <div class="form-row">
                <label>Image URL</label>
                <input type="text" name="image_url" value="{{ old('image_url', $ad->image_url) }}">
            </div>
            <div class="form-row">
                <label>Target URL *</label>
                <input type="text" name="target_url" value="{{ old('target_url', $ad->target_url) }}" required>
            </div>
            <div class="form-row">
                <label>Position *</label>
                <select name="ad_position_id" required>
                    <option value="">Position छान्नुहोस्</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}" @selected(old('ad_position_id', $ad->ad_position_id) === $position->id)>{{ $position->name }} ({{ $position->type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($ad->start_date)->format('Y-m-d')) }}">
                </div>
                <div class="form-row">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($ad->end_date)->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ad->is_active ?? true))>
                    Active
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $ad->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.ads.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
@extends('admin.layout')
@section('title', $holiday->exists ? 'Edit Holiday' : 'New Holiday')
@section('heading', $holiday->exists ? 'बिदा सम्पादन' : 'नयाँ बिदा')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.holidays.index') }}">बिदाहरू</a> / {{ $holiday->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $holiday->exists ? route('admin.holidays.update', $holiday) : route('admin.holidays.store') }}">
        @csrf
        @if ($holiday->exists) @method('PUT') @endif
        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $holiday->name) }}" required>
                </div>
                <div class="form-row">
                    <label>नाम (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $holiday->name_en) }}">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>मिति *</label>
                    <input type="date" name="date" value="{{ old('date', optional($holiday->date)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-row">
                    <label>Type</label>
                    <input type="text" name="type" value="{{ old('type', $holiday->type) }}">
                </div>
            </div>
            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $holiday->description) }}</textarea>
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_public_holiday" value="1" @checked(old('is_public_holiday', $holiday->is_public_holiday ?? true))>
                    Public Holiday
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $holiday->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.holidays.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
@extends('admin.layout')
@section('title', $tournament->exists ? 'Edit Tournament' : 'New Tournament')
@section('heading', $tournament->exists ? 'प्रतियोगिता सम्पादन' : 'नयाँ प्रतियोगिता')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.sports.index') }}">खेलकुद</a> / {{ $tournament->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $tournament->exists ? route('admin.sports.update', $tournament) : route('admin.sports.store') }}">
        @csrf
        @if ($tournament->exists) @method('PUT') @endif
        <div class="card">
            <div class="form-row">
                <label>नाम *</label>
                <input type="text" name="name" value="{{ old('name', $tournament->name) }}" required>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Sport Type *</label>
                    <input type="text" name="sport_type" value="{{ old('sport_type', $tournament->sport_type) }}" required>
                </div>
                <div class="form-row">
                    <label>Season</label>
                    <input type="text" name="season" value="{{ old('season', $tournament->season) }}">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($tournament->start_date)->format('Y-m-d')) }}">
                </div>
                <div class="form-row">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($tournament->end_date)->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tournament->is_active ?? true))>
                    Active
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $tournament->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.sports.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
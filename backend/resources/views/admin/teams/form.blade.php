@extends('admin.layout')
@section('title', $team->exists ? 'Edit Team' : 'New Team')
@section('heading', $team->exists ? 'टोली सम्पादन' : 'नयाँ टोली')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.teams.index') }}">टोलीहरू</a> / {{ $team->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $team->exists ? route('admin.teams.update', $team) : route('admin.teams.store') }}">
        @csrf
        @if ($team->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label>नाम *</label>
                <input type="text" name="name" value="{{ old('name', $team->name) }}" required>
            </div>
            <div class="form-row">
                <label>नाम (English)</label>
                <input type="text" name="name_en" value="{{ old('name_en', $team->name_en) }}">
            </div>
            <div class="form-row">
                <label>Logo URL</label>
                <input type="text" name="logo" value="{{ old('logo', $team->logo) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $team->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.teams.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
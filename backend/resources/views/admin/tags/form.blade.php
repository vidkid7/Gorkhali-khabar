@extends('admin.layout')
@section('title', $tag->exists ? 'Edit Tag' : 'New Tag')
@section('heading', $tag->exists ? 'ट्याग सम्पादन' : 'नयाँ ट्याग')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.tags.index') }}">ट्यागहरू</a> / {{ $tag->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $tag->exists ? route('admin.tags.update', $tag) : route('admin.tags.store') }}">
        @csrf
        @if ($tag->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $tag->name) }}" required>
                </div>
                <div class="form-row">
                    <label>नाम (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $tag->name_en) }}">
                </div>
            </div>
            <div class="form-row">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $tag->slug) }}" placeholder="auto from name">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $tag->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.tags.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
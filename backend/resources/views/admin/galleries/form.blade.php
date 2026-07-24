@extends('admin.layout')
@section('title', $gallery->exists ? 'Edit Gallery' : 'New Gallery')
@section('heading', $gallery->exists ? 'ग्यालेरी सम्पादन' : 'नयाँ ग्यालेरी')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.galleries.index') }}">ग्यालेरीहरू</a> / {{ $gallery->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $gallery->exists ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}">
        @csrf
        @if ($gallery->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>शीर्षक *</label>
                    <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required>
                </div>
                <div class="form-row">
                    <label>शीर्षक (English)</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $gallery->title_en) }}">
                </div>
            </div>
            <div class="form-row">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $gallery->slug) }}" placeholder="auto from title">
            </div>
            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $gallery->description) }}</textarea>
            </div>
            <div class="form-row">
                <label>Cover Image URL</label>
                <input type="text" name="cover_image" value="{{ old('cover_image', $gallery->cover_image) }}">
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gallery->is_published ?? false))>
                    Published
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $gallery->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.galleries.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
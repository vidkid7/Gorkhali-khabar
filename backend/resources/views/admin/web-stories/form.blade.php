@extends('admin.layout')
@section('title', $story->exists ? 'Edit Web Story' : 'New Web Story')
@section('heading', $story->exists ? 'वेब स्टोरी सम्पादन' : 'नयाँ वेब स्टोरी')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.web-stories.index') }}">वेब स्टोरी</a> / {{ $story->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $story->exists ? route('admin.web-stories.update', $story) : route('admin.web-stories.store') }}">
        @csrf
        @if ($story->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label>शीर्षक *</label>
                <input type="text" name="title" value="{{ old('title', $story->title) }}" required>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $story->slug) }}">
                </div>
                <div class="form-row">
                    <label>वर्ग</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $story->category_id) === $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label>Cover Image URL</label>
                <input type="text" name="cover_image" value="{{ old('cover_image', $story->cover_image) }}">
            </div>
            <div class="form-row">
                <label>Slides (JSON)</label>
                <textarea name="slides" rows="6">{{ old('slides', is_array($story->slides) ? json_encode($story->slides, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '[]') }}</textarea>
                <small style="color:var(--text-muted);font-size:11px;">JSON array: <code>[{"image":"...","caption":"..."}]</code></small>
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $story->is_active ?? true))>
                    Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $story->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.web-stories.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
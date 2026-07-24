@extends('admin.layout')
@section('title', $image->exists ? 'Edit Image' : 'New Image')
@section('heading', $image->exists ? 'तस्वीर सम्पादन' : 'नयाँ तस्वीर')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.gallery-images.index') }}">तस्वीरहरू</a> / {{ $image->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $image->exists ? route('admin.gallery-images.update', $image) : route('admin.gallery-images.store') }}">
        @csrf
        @if ($image->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label>ग्यालेरी *</label>
                <select name="gallery_id" required>
                    <option value="">छान्नुहोस्</option>
                    @foreach ($galleries as $g)
                        <option value="{{ $g->id }}" @selected(old('gallery_id', $image->gallery_id) === $g->id)>{{ $g->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <label>Image URL *</label>
                <input type="text" name="image_url" value="{{ old('image_url', $image->image_url) }}" required>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Caption</label>
                    <input type="text" name="caption" value="{{ old('caption', $image->caption) }}">
                </div>
                <div class="form-row">
                    <label>Alt Text</label>
                    <input type="text" name="alt_text" value="{{ old('alt_text', $image->alt_text) }}">
                </div>
            </div>

            <div class="form-row">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $image->sort_order ?? 0)" style="max-width:120px;">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $image->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.gallery-images.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
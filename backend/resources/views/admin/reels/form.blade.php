@extends('admin.layout')
@section('title', $reel->exists ? 'Edit Reel' : 'New Reel')
@section('heading', $reel->exists ? 'रिल सम्पादन' : 'नयाँ रिल')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.reels.index') }}">रिल्स</a> / {{ $reel->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $reel->exists ? route('admin.reels.update', $reel) : route('admin.reels.store') }}">
        @csrf
        @if ($reel->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label>शीर्षक *</label>
                <input type="text" name="title" value="{{ old('title', $reel->title) }}" required>
            </div>
            <div class="form-row">
                <label>Video URL *</label>
                <input type="text" name="video_url" value="{{ old('video_url', $reel->video_url) }}" required>
            </div>
            <div class="form-row">
                <label>Thumbnail URL</label>
                <input type="text" name="thumbnail_url" value="{{ old('thumbnail_url', $reel->thumbnail_url) }}">
            </div>
            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $reel->description) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Duration (seconds)</label>
                    <input type="number" name="duration" value="{{ old('duration', $reel->duration) }}">
                </div>
                <div class="form-row">
                    <label style="display:flex;align-items:center;gap:8px;padding-top:30px;">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $reel->is_published ?? false))>
                        Published
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $reel->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.reels.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
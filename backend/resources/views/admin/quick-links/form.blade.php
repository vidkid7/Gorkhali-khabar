@extends('admin.layout')
@section('title', $link->exists ? 'Edit Quick Link' : 'New Quick Link')
@section('heading', $link->exists ? 'द्रुत लिंक सम्पादन' : 'नयाँ द्रुत लिंक')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.quick-links.index') }}">द्रुत लिंक</a> / {{ $link->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $link->exists ? route('admin.quick-links.update', $link) : route('admin.quick-links.store') }}">
        @csrf
        @if ($link->exists) @method('PUT') @endif
        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>शीर्षक *</label>
                    <input type="text" name="title" value="{{ old('title', $link->title) }}" required>
                </div>
                <div class="form-row">
                    <label>शीर्षक (English)</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $link->title_en) }}">
                </div>
            </div>
            <div class="form-row">
                <label>URL *</label>
                <input type="text" name="url" value="{{ old('url', $link->url) }}" required>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Icon (Lucide name)</label>
                    <input type="text" name="icon" value="{{ old('icon', $link->icon) }}">
                </div>
                <div class="form-row">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $link->sort_order ?? 0) }}">
                </div>
            </div>
            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $link->is_active ?? true))>
                    Active
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $link->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.quick-links.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
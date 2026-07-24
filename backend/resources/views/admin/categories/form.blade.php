@extends('admin.layout')
@section('title', $category->exists ? 'Edit Category' : 'New Category')
@section('heading', $category->exists ? 'वर्ग सम्पादन' : 'नयाँ वर्ग')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.categories.index') }}">वर्गहरू</a> / {{ $category->exists ? 'Edit' : 'New' }}</div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:16px;">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
                </div>
                <div class="form-row">
                    <label>नाम (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}">
                </div>
            </div>

            <div class="form-row">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="auto from name">
            </div>

            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Color (hex)</label>
                    <input type="text" name="color" value="{{ old('color', $category->color ?? '#c62828') }}">
                </div>
                <div class="form-row">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                </div>
            </div>

            <div class="form-row">
                <label>Parent Category</label>
                <select name="parent_id">
                    <option value="">— None —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) === $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
                    Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $category->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
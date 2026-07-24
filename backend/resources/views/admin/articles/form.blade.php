@extends('admin.layout')

@section('title', $article->exists ? 'Edit Article' : 'New Article')
@section('heading', $article->exists ? 'लेख सम्पादन' : 'नयाँ लेख')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('admin.articles.index') }}">लेखहरू</a> /
        {{ $article->exists ? 'Edit' : 'New' }}
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <div class="card">
            <h3 class="card-title">आधारभूत जानकारी</h3>

            <div class="form-row">
                <label>शीर्षक (नेपाली) *</label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required>
            </div>

            <div class="form-row">
                <label>शीर्षक (English)</label>
                <input type="text" name="title_en" value="{{ old('title_en', $article->title_en) }}">
            </div>

            <div class="form-row">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="auto-generated from title if empty">
            </div>

            <div class="form-row">
                <label>वर्ग *</label>
                <select name="category_id" required>
                    <option value="">वर्ग छान्नुहोस्</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $article->category_id) === $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <label>Featured Image URL</label>
                <input type="text" name="featured_image" value="{{ old('featured_image', $article->featured_image) }}">
            </div>

            <div class="form-row">
                <label>संक्षेप (नेपाली)</label>
                <textarea name="excerpt" rows="2">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <div class="form-row">
                <label>संक्षेप (English)</label>
                <textarea name="excerpt_en" rows="2">{{ old('excerpt_en', $article->excerpt_en) }}</textarea>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">सामग्री</h3>
            <div class="form-row">
                <label>सामग्री (नेपाली) *</label>
                <textarea name="content" rows="14" required>{{ old('content', $article->content) }}</textarea>
            </div>
            <div class="form-row">
                <label>सामग्री (English)</label>
                <textarea name="content_en" rows="10">{{ old('content_en', $article->content_en) }}</textarea>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">सेटिङ</h3>
            <div class="form-grid">
                <div class="form-row">
                    <label>स्थिति *</label>
                    <select name="status" required>
                        @foreach (['DRAFT','PENDING','PUBLISHED','ARCHIVED'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $article->status ?: 'DRAFT') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>&nbsp;</label>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))>
                        Featured Article
                    </label>
                </div>
            </div>

            <div class="form-row">
                <label>ट्यागहरू</label>
                <select name="tags[]" multiple size="5" style="min-height:120px;">
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', $selectedTags), true))>{{ $tag->name }}</option>
                    @endforeach
                </select>
                <small style="color:var(--text-muted);font-size:12px;">Ctrl/Cmd-click to select multiple</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $article->exists ? 'अपडेट गर्नुहोस्' : 'सिर्जना गर्नुहोस्' }}</button>
            <a href="{{ route('admin.articles.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
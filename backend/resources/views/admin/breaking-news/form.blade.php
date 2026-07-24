@extends('admin.layout')
@section('title', $item->exists ? 'Edit Breaking News' : 'New Breaking News')
@section('heading', $item->exists ? 'ब्रेकिङ न्युज सम्पादन' : 'नयाँ ब्रेकिङ न्युज')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.breaking-news.index') }}">ब्रेकिङ न्युज</a> / {{ $item->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $item->exists ? route('admin.breaking-news.update', $item) : route('admin.breaking-news.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>शीर्षक (नेपाली) *</label>
                    <input type="text" name="title" value="{{ old('title', $item->title) }}" required>
                </div>
                <div class="form-row">
                    <label>शीर्षक (English)</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $item->title_en) }}">
                </div>
            </div>

            <div class="form-row">
                <label>लेख (लिंक)</label>
                <select name="article_id">
                    <option value="">— Custom URL —</option>
                    @foreach ($articles as $article)
                        <option value="{{ $article->id }}" @selected(old('article_id', $item->article_id) === $article->id)>{{ \Illuminate\Support\Str::limit($article->title, 60) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <label>Custom URL (वैकल्पिक)</label>
                <input type="text" name="url" value="{{ old('url', $item->url) }}">
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Priority</label>
                    <input type="number" name="priority" value="{{ old('priority', $item->priority ?? 0) }}">
                </div>
                <div class="form-row">
                    <label style="display:flex;align-items:center;gap:8px;padding-top:30px;">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
                        Active
                    </label>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>सुरु मिति</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($item->starts_at)->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-row">
                    <label>अन्त्य मिति</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($item->ends_at)->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $item->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.breaking-news.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
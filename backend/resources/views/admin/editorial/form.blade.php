@extends('admin.layout')
@section('title', ($item->exists ? 'Edit ' : 'New ').$config['label'])
@section('heading', $config['heading'])

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.'.$config['resource'].'.index') }}">{{ $config['heading'] }}</a> / {{ $item->exists ? 'Edit' : 'New' }}</div>
    @if ($errors->any())
        <div class="alert alert-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ $item->exists ? route('admin.'.$config['resource'].'.update', $item) : route('admin.'.$config['resource'].'.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="card">
            @foreach ($config['fields'] as $name => $field)
                @php
                    $type = $field['type'] ?? 'text';
                    $value = old($name, $item->{$name});
                    if ($value instanceof \Carbon\CarbonInterface && $type === 'datetime-local') $value = $value->format('Y-m-d\TH:i');
                @endphp
                <div class="form-row">
                    @if ($type === 'checkbox')
                        <label style="display:flex;align-items:center;gap:8px;">
                            <input style="width:auto" type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $item->exists ? (bool) $item->{$name} : true))>
                            {{ $field['label'] }}
                        </label>
                    @else
                        <label for="{{ $name }}">{{ $field['label'] }}</label>
                        @if ($type === 'textarea')
                            <textarea id="{{ $name }}" name="{{ $name }}" rows="8">{{ $value }}</textarea>
                        @elseif ($type === 'select')
                            <select id="{{ $name }}" name="{{ $name }}">
                                @foreach ($field['options'] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}">
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ $item->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a class="btn" href="{{ route('admin.'.$config['resource'].'.index') }}">रद्द</a>
        </div>
    </form>

    @if ($config['resource'] === 'live-blogs' && $item->exists)
        <div class="card" style="margin-top:24px;">
            <h2>लाइभ अपडेटहरू</h2>
            <form method="POST" action="{{ route('admin.live-blog-posts.store', $item) }}">
                @csrf
                <div class="form-row"><label>शीर्षक</label><input name="title"></div>
                <div class="form-row"><label>अपडेट *</label><textarea name="body" required></textarea></div>
                <div class="form-row"><label>Update (English)</label><textarea name="body_en"></textarea></div>
                <button class="btn btn-primary" type="submit">अपडेट थप्नुहोस्</button>
            </form>
            <hr style="border:0;border-top:1px solid var(--border);margin:20px 0;">
            @forelse ($item->posts as $post)
                <div style="border-bottom:1px solid var(--border);padding:12px 0;">
                    <strong>{{ $post->title ?: 'Live update' }}</strong>
                    <small style="color:var(--muted);">{{ $post->published_at?->format('Y-m-d H:i') }}</small>
                    <p>{{ $post->body }}</p>
                    <form method="POST" action="{{ route('admin.live-blog-posts.destroy', [$item, $post]) }}" onsubmit="return confirm('मेटाउने हो?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            @empty
                <div class="empty">अहिलेसम्म कुनै लाइभ अपडेट छैन।</div>
            @endforelse
        </div>
    @endif
@endsection

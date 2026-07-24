@extends('admin.layout')
@section('title', $user->exists ? 'Edit User' : 'New User')
@section('heading', $user->exists ? 'प्रयोगकर्ता सम्पादन' : 'नयाँ प्रयोगकर्ता')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.users.index') }}">प्रयोगकर्ताहरू</a> / {{ $user->exists ? 'Edit' : 'New' }}</div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:16px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label>नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-row">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>भूमिका *</label>
                    <select name="role" required>
                        @foreach (['READER','AUTHOR','EDITOR','ADMIN'] as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role ?: 'READER') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>भाषा</label>
                    <select name="language">
                        <option value="ne" @selected(old('language', $user->language ?: 'ne') === 'ne')>नेपाली</option>
                        <option value="en" @selected(old('language', $user->language ?: 'ne') === 'en')>English</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))>
                    Active
                </label>
            </div>

            <div class="form-row">
                <label>Password {{ $user->exists ? '(खाली छोड्नुहोस् यदि परिवर्तन नगर्ने हो)' : '*' }}</label>
                <input type="password" name="password" {{ $user->exists ? '' : 'required' }} minlength="8">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $user->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection
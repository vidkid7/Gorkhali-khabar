@extends('admin.layout')

@section('title', 'My Profile')
@section('heading', 'मेरो प्रोफाइल')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">ड्यासबोर्ड</a> / Profile
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:16px;">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="card">
            <h3 class="card-title">व्यक्तिगत जानकारी</h3>
            <div class="form-grid">
                <div class="form-row">
                    <label>नाम *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-row">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    <small style="color:var(--text-muted);font-size:11px;">
                        भूमिका: <strong>{{ $user->role }}</strong> — परिवर्तन गर्न प्रयोगकर्ता पृष्ठ प्रयोग गर्नुहोस्
                    </small>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>भाषा</label>
                    <select name="language">
                        <option value="ne" @selected(old('language', $user->language ?: 'ne') === 'ne')>नेपाली</option>
                        <option value="en" @selected(old('language', $user->language ?: 'ne') === 'en')>English</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>सार्वजनिक थिम</label>
                    <select name="theme">
                        <option value="light" @selected(old('theme', $user->theme ?: 'light') === 'light')>Light</option>
                        <option value="dark" @selected(old('theme', $user->theme ?: 'light') === 'dark')>Dark</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <label>एडमिन थिम</label>
                <select name="admin_theme" style="max-width:200px;">
                    <option value="light" @selected(old('admin_theme', $user->admin_theme ?: 'light') === 'light')>Light</option>
                    <option value="dark" @selected(old('admin_theme', $user->admin_theme ?: 'light') === 'dark')>Dark</option>
                </select>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">पासवर्ड परिवर्तन <small style="color:var(--text-muted);font-size:12px;font-weight:normal;">(वैकल्पिक)</small></h3>
            <div class="form-row">
                <label>हालको पासवर्ड</label>
                <input type="password" name="current_password" autocomplete="current-password">
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>नयाँ पासवर्ड</label>
                    <input type="password" name="password" minlength="8" autocomplete="new-password">
                </div>
                <div class="form-row">
                    <label>नयाँ पासवर्ड (पुष्टि)</label>
                    <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">प्रोफाइल सुरक्षित गर्नुहोस्</button>
        </div>
    </form>
@endsection
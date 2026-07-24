@extends('admin.layout')
@section('title', 'Settings')
@section('heading', 'सेटिङहरू')

@section('content')
    <div class="page-header">
        <h1>सेटिङहरू</h1>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        {{-- ब्रान्डिङ / Branding --}}
        <div class="card">
            <h3 class="card-title">ब्रान्डिङ</h3>
            <div class="form-grid">
                <fieldset class="form-group">
                    <legend>साइटको नाम (Site Name)</legend>
                    <div class="form-row">
                        <label>नेपाली</label>
                        <input type="text" name="site_name__ne"
                            value="{{ old('site_name__ne', is_array($settings['site_name'] ?? null) ? ($settings['site_name']['ne'] ?? '') : ($settings['site_name'] ?? '')) }}">
                    </div>
                    <div class="form-row">
                        <label>English</label>
                        <input type="text" name="site_name__en"
                            value="{{ old('site_name__en', is_array($settings['site_name'] ?? null) ? ($settings['site_name']['en'] ?? '') : ($settings['site_name'] ?? '')) }}">
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <legend>ट्यागलाइन (Tagline)</legend>
                    <div class="form-row">
                        <label>नेपाली</label>
                        <input type="text" name="site_tagline__ne"
                            value="{{ old('site_tagline__ne', is_array($settings['site_tagline'] ?? null) ? ($settings['site_tagline']['ne'] ?? '') : ($settings['site_tagline'] ?? '')) }}">
                    </div>
                    <div class="form-row">
                        <label>English</label>
                        <input type="text" name="site_tagline__en"
                            value="{{ old('site_tagline__en', is_array($settings['site_tagline'] ?? null) ? ($settings['site_tagline']['en'] ?? '') : ($settings['site_tagline'] ?? '')) }}">
                    </div>
                </fieldset>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>साइट लोगो (URL)</label>
                    <input type="text" name="site_logo" value="{{ old('site_logo', $settings['site_logo'] ?? '') }}">
                </div>
                <div class="form-row">
                    <label>फेभिकन (URL)</label>
                    <input type="text" name="site_favicon" value="{{ old('site_favicon', $settings['site_favicon'] ?? '') }}">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>प्राथमिक रङ (Primary Color)</label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#07579B') }}">
                </div>
            </div>
        </div>

        {{-- सम्पर्क जानकारी / Contact --}}
        <div class="card">
            <h3 class="card-title">सम्पर्क जानकारी</h3>
            <div class="form-grid">
                <div class="form-row">
                    <label>फोन</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                </div>
                <div class="form-row">
                    <label>इमेल</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                </div>
            </div>
            <fieldset class="form-group">
                <legend>ठेगाना (Address)</legend>
                <div class="form-grid">
                    <div class="form-row">
                        <label>नेपाली</label>
                        <input type="text" name="contact_address__ne"
                            value="{{ old('contact_address__ne', is_array($settings['contact_address'] ?? null) ? ($settings['contact_address']['ne'] ?? '') : ($settings['contact_address'] ?? '')) }}">
                    </div>
                    <div class="form-row">
                        <label>English</label>
                        <input type="text" name="contact_address__en"
                            value="{{ old('contact_address__en', is_array($settings['contact_address'] ?? null) ? ($settings['contact_address']['en'] ?? '') : ($settings['contact_address'] ?? '')) }}">
                    </div>
                </div>
            </fieldset>
            <div class="form-row">
                <label>दर्ता नम्बर</label>
                <input type="text" name="registration_number" value="{{ old('registration_number', $settings['registration_number'] ?? '') }}">
            </div>
        </div>

        {{-- सामाजिक सञ्जाल / Social Media --}}
        <div class="card">
            <h3 class="card-title">सामाजिक सञ्जाल</h3>
            <div class="form-grid">
                <div class="form-row">
                    <label>Facebook</label>
                    <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/...">
                </div>
                <div class="form-row">
                    <label>X (Twitter)</label>
                    <input type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://twitter.com/...">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>YouTube</label>
                    <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/...">
                </div>
                <div class="form-row">
                    <label>Instagram</label>
                    <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/...">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>TikTok</label>
                    <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}" placeholder="https://tiktok.com/...">
                </div>
            </div>
        </div>

        {{-- प्रतिलिपि अधिकार / Copyright --}}
        <div class="card">
            <h3 class="card-title">प्रतिलिपि अधिकार</h3>
            <fieldset class="form-group">
                <legend>प्रतिलिपि अधिकार पाठ (Copyright Text)</legend>
                <div class="form-grid">
                    <div class="form-row">
                        <label>नेपाली</label>
                        <input type="text" name="copyright_text__ne"
                            value="{{ old('copyright_text__ne', is_array($settings['copyright_text'] ?? null) ? ($settings['copyright_text']['ne'] ?? '') : ($settings['copyright_text'] ?? '© {year} गोर्खाली खबर।')) }}">
                    </div>
                    <div class="form-row">
                        <label>English</label>
                        <input type="text" name="copyright_text__en"
                            value="{{ old('copyright_text__en', is_array($settings['copyright_text'] ?? null) ? ($settings['copyright_text']['en'] ?? '') : ($settings['copyright_text'] ?? '© {year} Gorkhali Khabar.')) }}">
                    </div>
                </div>
            </fieldset>
        </div>

        {{-- सुविधाहरू / Features --}}
        <div class="card">
            <h3 class="card-title">सुविधाहरू</h3>
            <div class="form-grid form-checkboxes">
                <label class="checkbox-row">
                    <input type="hidden" name="features_comments" value="0">
                    <input type="checkbox" name="features_comments" value="1" {{ old('features_comments', $settings['features_comments'] ?? '1') == '1' ? 'checked' : '' }}>
                    टिप्पणी (Comments)
                </label>
                <label class="checkbox-row">
                    <input type="hidden" name="features_bookmarks" value="0">
                    <input type="checkbox" name="features_bookmarks" value="1" {{ old('features_bookmarks', $settings['features_bookmarks'] ?? '1') == '1' ? 'checked' : '' }}>
                    बुकमार्क (Bookmarks)
                </label>
                <label class="checkbox-row">
                    <input type="hidden" name="features_reels" value="0">
                    <input type="checkbox" name="features_reels" value="1" {{ old('features_reels', $settings['features_reels'] ?? '1') == '1' ? 'checked' : '' }}>
                    रिल्स (Reels)
                </label>
                <label class="checkbox-row">
                    <input type="hidden" name="features_galleries" value="0">
                    <input type="checkbox" name="features_galleries" value="1" {{ old('features_galleries', $settings['features_galleries'] ?? '1') == '1' ? 'checked' : '' }}>
                    ग्यालरी (Galleries)
                </label>
            </div>
        </div>

        {{-- विज्ञापन / Ads --}}
        <div class="card">
            <h3 class="card-title">विज्ञापन / Ads.txt</h3>
            <div class="form-row">
                <label>AdSense Publisher ID</label>
                <input type="text" name="adsense_publisher_id" value="{{ old('adsense_publisher_id', $settings['adsense_publisher_id'] ?? '') }}">
            </div>
            <div class="form-row">
                <label>Google Analytics ID</label>
                <input type="text" name="analytics_id" value="{{ old('analytics_id', $settings['analytics_id'] ?? '') }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">सेटिङ सुरक्षित गर्नुहोस्</button>
        </div>
    </form>
@endsection
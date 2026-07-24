<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $rawSettings = SiteSetting::query()->pluck('value', 'key')->all();

        // Transform values for safe display in form fields
        $settings = array_map(function (mixed $value): mixed {
            if (is_array($value)) {
                // Locale-keyed objects: prefer current locale, fallback to English, then first value
                $locale = app()->getLocale();
                return $value[$locale] ?? $value['en'] ?? reset($value) ?? '';
            }
            if (is_bool($value)) {
                return $value ? '1' : '0';
            }
            return $value ?? '';
        }, $rawSettings);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request, AuditService $audit): RedirectResponse
    {
        $raw = $request->except(['_token', '_method']);
        if (! $raw) {
            return back()->with('error', 'कुनै सेटिङ प्रदान गरिएको छैन।');
        }

        // Merge __ne/__en suffixed keys into locale JSON objects
        $values = [];
        $localeKeys = [];
        foreach ($raw as $key => $value) {
            if (str_ends_with($key, '__ne')) {
                $base = substr($key, 0, -4);
                $localeKeys[$base] ??= [];
                $localeKeys[$base]['ne'] = $value;
            } elseif (str_ends_with($key, '__en')) {
                $base = substr($key, 0, -4);
                $localeKeys[$base] ??= [];
                $localeKeys[$base]['en'] = $value;
            } else {
                $values[$key] = $value;
            }
        }
        foreach ($localeKeys as $base => $locale) {
            $values[$base] = $locale;
        }

        $oldValues = SiteSetting::query()->whereIn('key', array_keys($values))->pluck('value', 'key')->all();
        DB::transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });

        $audit->record($request->user(), 'SETTINGS_CHANGE', 'SiteSettings', oldValue: $oldValues, newValue: $values);

        return back()->with('status', 'सेटिङहरू अपडेट गरियो।');
    }
}
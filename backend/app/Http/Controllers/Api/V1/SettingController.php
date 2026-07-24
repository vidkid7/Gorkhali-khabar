<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SiteSetting::query()->pluck('value', 'key')->all();
        $data = array_merge($settings, [
            'site_name' => ['ne' => 'गोर्खाली खबर', 'en' => 'Gorkhali Khabar'],
            'site_tagline' => ['ne' => 'सत्य, सन्तुलित र समयमै', 'en' => 'Truthful, balanced, and timely'],
            'site_logo' => '/icons/logo.png',
            'site_favicon' => '/icons/logo.png',
            'copyright_text' => ['ne' => '© {year} गोर्खाली खबर।', 'en' => '© {year} Gorkhali Khabar.'],
        ]);

        return ApiResponse::success($data)->header('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=300');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuickLink;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class QuickLinkController extends Controller
{
    public function index(): JsonResponse
    {
        $links = QuickLink::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get([
                'slug',
                'href',
                'title_ne',
                'title_en',
                'description_ne',
                'description_en',
                'icon_key',
                'accent_color',
            ]);

        return ApiResponse::success($links);
    }
}

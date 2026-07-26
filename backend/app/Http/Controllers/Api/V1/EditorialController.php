<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use App\Models\EditorialMenu;
use App\Models\HomepageSection;
use App\Models\LiveBlog;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorialController extends Controller
{
    public function page(string $slug): JsonResponse
    {
        $page = ContentPage::query()->where('slug', $slug)->where('is_published', true)->first();
        return $page ? ApiResponse::success($page) : ApiResponse::error('Page not found', 404);
    }

    public function menus(Request $request, ?string $location = null): JsonResponse
    {
        $selectedLocation = $location ?? $request->query('location', 'header');

        return ApiResponse::success(
            EditorialMenu::query()->where('location', $selectedLocation)->where('is_active', true)
                ->orderBy('sort_order')->get()
        );
    }

    public function homepageSections(): JsonResponse
    {
        return ApiResponse::success(
            HomepageSection::query()->where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    public function liveBlogs(): JsonResponse
    {
        return ApiResponse::success(
            LiveBlog::query()->whereIn('status', ['LIVE', 'ENDED'])->with('posts')->orderByDesc('started_at')->get()
        );
    }

    public function liveBlog(string $slug): JsonResponse
    {
        $blog = LiveBlog::query()->where('slug', $slug)->whereIn('status', ['LIVE', 'ENDED'])->with('posts')->first();
        return $blog ? ApiResponse::success($blog) : ApiResponse::error('Live blog not found', 404);
    }
}

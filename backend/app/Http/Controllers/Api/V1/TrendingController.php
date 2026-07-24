<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PageView;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrendingController extends Controller
{
    public function index(): JsonResponse
    {
        $trending = PageView::query()
            ->select('article_id', DB::raw('COUNT(*) as recent_views'))
            ->whereNotNull('article_id')
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('article_id')
            ->orderByDesc('recent_views')
            ->limit(5)
            ->get();

        if ($trending->isEmpty()) {
            return ApiResponse::success([]);
        }

        $viewCounts = $trending->pluck('recent_views', 'article_id');
        $articles = Article::query()
            ->where('status', 'PUBLISHED')
            ->whereIn('id', $viewCounts->keys())
            ->with([
                'category:id,name,slug',
                'author:id,name,image',
            ])
            ->get([
                'id',
                'title',
                'slug',
                'excerpt',
                'featured_image',
                'published_at',
                'reading_time',
                'view_count',
                'category_id',
                'author_id',
            ])
            ->sortBy(fn (Article $article): int => -((int) $viewCounts->get($article->id, 0)))
            ->values();

        return ApiResponse::success($articles);
    }
}

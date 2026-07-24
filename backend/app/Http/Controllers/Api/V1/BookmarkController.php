<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Bookmark;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 10)));
        $query = Bookmark::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('article', fn ($query) => $query->where('status', 'PUBLISHED'))
            ->with(['article' => fn ($query) => $query->select([
                'id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'reading_time', 'category_id', 'author_id',
            ])->with(['category:id,name,slug', 'author:id,name,image'])])
            ->orderByDesc('created_at');
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->forPage($page, $pageSize)->get(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['article_id' => ['required', 'string']]);
        $article = Article::query()->whereKey($data['article_id'])->where('status', 'PUBLISHED')->first();
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }
        if (Bookmark::query()->where('user_id', $request->user()->id)->where('article_id', $article->id)->exists()) {
            return ApiResponse::error('लेख पहिले नै सुरक्षित गरिएको छ', 409);
        }

        $bookmark = Bookmark::query()->create(['user_id' => $request->user()->id, 'article_id' => $article->id]);

        return ApiResponse::success($bookmark->load(['article' => fn ($query) => $query->select('id', 'title', 'slug', 'featured_image')]), 201);
    }

    public function destroy(Request $request, string $articleId): JsonResponse
    {
        $bookmark = Bookmark::query()->where('user_id', $request->user()->id)->where('article_id', $articleId)->first();
        if (! $bookmark) {
            return ApiResponse::error('बुकमार्क फेला परेन', 404);
        }
        $bookmark->delete();

        return ApiResponse::success(['article_id' => $articleId]);
    }
}

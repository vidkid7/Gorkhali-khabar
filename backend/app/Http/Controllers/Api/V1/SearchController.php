<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $term = trim($request->query('q', ''));
        if ($term === '' || mb_strlen($term) < 2) {
            return ApiResponse::error('खोज शब्द कम्तीमा २ अक्षरको हुनुपर्छ', 400);
        }

        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 10)));
        $search = '%'.mb_strtolower($term).'%';
        $query = Article::query()
            ->where('status', 'PUBLISHED')
            ->where(function (Builder $query) use ($search): void {
                $query->whereRaw('LOWER(title) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$search]);
            })
            ->with([
                'category:id,name,slug',
                'author:id,name,image',
            ]);

        $total = (clone $query)->count();
        $articles = $query
            ->orderByDesc('published_at')
            ->forPage($page, $pageSize)
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
            ]);

        return ApiResponse::success([
            'data' => $articles,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }
}

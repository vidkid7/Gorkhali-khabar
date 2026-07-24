<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['article_id' => ['required', 'string']]);
        $article = Article::query()->whereKey($request->string('article_id'))->where('status', 'PUBLISHED')->first();
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }

        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 20)));
        $query = Comment::query()
            ->where('article_id', $article->id)
            ->whereNull('parent_id')
            ->where('status', 'APPROVED')
            ->with([
                'user:id,name,image',
                'children' => fn ($query) => $query->where('status', 'APPROVED')->orderBy('created_at')->with([
                    'user:id,name,image',
                    'children' => fn ($query) => $query->where('status', 'APPROVED')->orderBy('created_at')->with('user:id,name,image'),
                ]),
            ]);
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->orderByDesc('created_at')->forPage($page, $pageSize)->get(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->email_verified) {
            return ApiResponse::error('इमेल प्रमाणित गर्नुपर्छ', 403);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:2000'],
            'article_id' => ['required', 'string'],
            'parent_id' => ['nullable', 'string'],
        ]);
        $article = Article::query()->whereKey($data['article_id'])->where('status', 'PUBLISHED')->first();
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }
        if (! empty($data['parent_id']) && ! Comment::query()->whereKey($data['parent_id'])->where('article_id', $article->id)->exists()) {
            return ApiResponse::error('टिप्पणी फेला परेन', 404);
        }

        $comment = DB::transaction(function () use ($data, $article, $request): Comment {
            $content = preg_replace('/<(script|style)\\b[^>]*>.*?<\/\\1>/is', '', $data['content']) ?? $data['content'];
            $comment = Comment::query()->create([
                'content' => trim(strip_tags($content)),
                'status' => 'PENDING',
                'article_id' => $article->id,
                'user_id' => $request->user()->id,
                'parent_id' => $data['parent_id'] ?? null,
            ]);
            Article::query()->whereKey($article->id)->increment('comment_count');

            return $comment;
        });

        return ApiResponse::success($comment->load('user:id,name,image'), 201);
    }

    public function update(Request $request, AuditService $audit, string $id): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:PENDING,APPROVED,REJECTED,SPAM']]);
        $comment = Comment::query()->find($id);
        if (! $comment) {
            return ApiResponse::error('टिप्पणी फेला परेन', 404);
        }
        $oldStatus = $comment->status;
        $comment->update(['status' => $data['status']]);
        $audit->record($request->user(), 'UPDATE', 'Comment', $comment->id, ['status' => $oldStatus], ['status' => $comment->status]);

        return ApiResponse::success($comment->fresh()->load('user:id,name,image'));
    }
}

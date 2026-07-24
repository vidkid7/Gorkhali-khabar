<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\BreakingNews;
use App\Models\PageView;
use App\Services\AuditService;
use App\Services\ContentSanitizer;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 10)));
        $query = Article::query()
            ->where('status', 'PUBLISHED')
            ->with([
                'category:id,name,slug',
                'author:id,name,image',
                'articleTags.tag:id,name,slug',
            ]);

        if ($request->filled('category')) {
            $query->where('category_id', $request->string('category'));
        }

        if ($request->filled('search')) {
            $search = '%'.strtolower($request->string('search')->toString()).'%';
            $query->where(function (Builder $query) use ($search): void {
                $query->whereRaw('LOWER(title) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$search]);
            });
        }

        $total = (clone $query)->count();
        $articles = $query
            ->orderByDesc('published_at')
            ->forPage($page, $pageSize)
            ->get()
            ->map(function (Article $article): array {
                $data = $article->toArray();
                $data['tags'] = $data['article_tags'];
                unset($data['article_tags']);

                return $data;
            });

        return ApiResponse::success([
            'data' => $articles,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function store(Request $request, ContentSanitizer $sanitizer, AuditService $audit): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'featured_image' => ['nullable', 'string'],
            'ai_summary' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:DRAFT,PUBLISHED,ARCHIVED'],
            'is_featured' => ['sometimes', 'boolean'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['string', 'exists:tags,id'],
        ]);

        if (Article::query()->where('slug', $data['slug'])->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }

        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $data['content'] = $sanitizer->html($data['content']);
        $data['content_en'] = $sanitizer->html($data['content_en'] ?? null);
        $plainContent = trim(strip_tags($data['content']));
        $wordCount = $plainContent === '' ? 0 : count(preg_split('/\s+/u', $plainContent));
        $data['word_count'] = $wordCount;
        $data['reading_time'] = (int) ceil($wordCount / 200);
        $data['author_id'] = $request->user()->id;
        $data['status'] ??= 'DRAFT';
        $data['published_at'] = $data['status'] === 'PUBLISHED' ? now() : null;

        $article = DB::transaction(function () use ($data, $tagIds): Article {
            $article = Article::query()->create($data);
            foreach (array_unique($tagIds) as $tagId) {
                ArticleTag::query()->create(['article_id' => $article->id, 'tag_id' => $tagId]);
            }

            return $article;
        });

        $audit->record($request->user(), 'CREATE', 'Article', $article->id, newValue: [
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status,
        ]);

        return ApiResponse::success($this->articlePayload($this->articleQuery()->findOrFail($article->id)), 201);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $article = $this->articleQuery()
            ->where('status', 'PUBLISHED')
            ->where('slug', $slug)
            ->first();

        return $article
            ? ApiResponse::success($this->articlePayload($article))
            : ApiResponse::error('लेख फेला परेन', 404);
    }

    public function show(string $id): JsonResponse
    {
        $article = $this->articleQuery()
            ->where('status', 'PUBLISHED')
            ->find($id);

        return $article
            ? ApiResponse::success($this->articlePayload($article))
            : ApiResponse::error('लेख फेला परेन', 404);
    }

    public function update(Request $request, ContentSanitizer $sanitizer, AuditService $audit, string $id): JsonResponse
    {
        $article = Article::query()->find($id);
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }
        if ($request->user()->role === 'AUTHOR' && $article->author_id !== $request->user()->id) {
            return ApiResponse::error('Forbidden', 403);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content' => ['sometimes', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'string', 'exists:categories,id'],
            'featured_image' => ['nullable', 'string'],
            'ai_summary' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:DRAFT,PUBLISHED,ARCHIVED'],
            'is_featured' => ['sometimes', 'boolean'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['string', 'exists:tags,id'],
        ]);

        if (isset($data['slug']) && Article::query()->where('slug', $data['slug'])->whereKeyNot($article->id)->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }

        $tagIds = $data['tag_ids'] ?? null;
        unset($data['tag_ids']);
        if (array_key_exists('content', $data)) {
            $data['content'] = $sanitizer->html($data['content']);
            $plainContent = trim(strip_tags($data['content']));
            $wordCount = $plainContent === '' ? 0 : count(preg_split('/\s+/u', $plainContent));
            $data['word_count'] = $wordCount;
            $data['reading_time'] = (int) ceil($wordCount / 200);
        }
        if (array_key_exists('content_en', $data)) {
            $data['content_en'] = $sanitizer->html($data['content_en']);
        }
        if (($data['status'] ?? null) === 'PUBLISHED' && $article->status !== 'PUBLISHED') {
            $data['published_at'] = now();
        }
        if (isset($data['status']) && $data['status'] !== 'PUBLISHED') {
            $data['is_featured'] = false;
            $data['published_at'] = null;
        }

        $oldValue = ['title' => $article->title, 'status' => $article->status];
        DB::transaction(function () use ($article, $data, $tagIds, $request, $audit, $oldValue): void {
            $article->update($data);
            if ($tagIds !== null) {
                ArticleTag::query()->where('article_id', $article->id)->delete();
                foreach (array_unique($tagIds) as $tagId) {
                    ArticleTag::query()->create(['article_id' => $article->id, 'tag_id' => $tagId]);
                }
            }
            if ($article->status !== 'PUBLISHED') {
                BreakingNews::query()->where('article_id', $article->id)->where('is_active', true)->update(['is_active' => false]);
            }
            $audit->record($request->user(), 'UPDATE', 'Article', $article->id, $oldValue, [
                'title' => $article->title,
                'status' => $article->status,
            ]);
        });

        return ApiResponse::success($this->articlePayload($this->articleQuery()->findOrFail($article->id)));
    }

    public function destroy(Request $request, AuditService $audit, string $id): JsonResponse
    {
        $article = Article::query()->find($id);
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }

        DB::transaction(function () use ($article, $request, $audit): void {
            $audit->record($request->user(), 'DELETE', 'Article', $article->id, oldValue: [
                'title' => $article->title,
                'slug' => $article->slug,
            ]);
            $article->delete();
        });

        return ApiResponse::success(['id' => $id]);
    }

    public function recordView(Request $request, string $id): JsonResponse
    {
        $article = Article::query()->whereKey($id)->where('status', 'PUBLISHED')->first();
        if (! $article) {
            return ApiResponse::error('लेख फेला परेन', 404);
        }

        $viewCount = DB::transaction(function () use ($article, $request): int {
            PageView::query()->create([
                'page_url' => '/articles/'.$article->slug,
                'article_id' => $article->id,
                'user_id' => $request->user()?->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->headers->get('referer'),
            ]);
            Article::query()->whereKey($article->id)->increment('view_count');

            return (int) $article->fresh()->view_count;
        });

        return ApiResponse::success(['view_count' => $viewCount]);
    }

    private function articleQuery(): Builder
    {
        return Article::query()->with([
            'category:id,name,slug',
            'author:id,name,image',
            'articleTags.tag:id,name,slug',
        ]);
    }

    /** @return array<string, mixed> */
    private function articlePayload(Article $article): array
    {
        $data = $article->toArray();
        $data['tags'] = $data['article_tags'];
        unset($data['article_tags']);

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()
            ->with(['category:id,name', 'author:id,name']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            if (in_array($status, ['DRAFT', 'PUBLISHED', 'ARCHIVED', 'PENDING'], true)) {
                $query->where('status', $status);
            }
        }
        if ($categoryId = $request->query('category')) {
            $query->where('category_id', $categoryId);
        }

        $articles = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create(): View
    {
        $article = new Article();
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.articles.form', [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'selectedTags' => [],
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateArticle($request);
        $data = $this->prepareForSave($data, $request);

        $article = DB::transaction(function () use ($data, $request) {
            $article = Article::query()->create($data);

            if ($request->filled('tags')) {
                $this->syncTags($article, (array) $request->input('tags'));
            }

            return $article;
        });

        $audit->record($request->user(), 'CREATE', 'Article', $article->id, newValue: $article->toArray());

        return redirect()->route('admin.articles.edit', $article)
            ->with('status', 'लेख सिर्जना गरियो।');
    }

    public function edit(Article $article): View
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);
        $selectedTags = $article->articleTags()->pluck('tag_id')->all();

        return view('admin.articles.form', [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'selectedTags' => $selectedTags,
        ]);
    }

    public function update(Request $request, AuditService $audit, Article $article): RedirectResponse
    {
        $data = $this->validateArticle($request, $article->id);
        $data = $this->prepareForSave($data, $request);

        DB::transaction(function () use ($data, $request, $article) {
            $old = $article->toArray();
            $article->update($data);
            $this->syncTags($article, (array) $request->input('tags', []));
            $request->attributes->set('_audit_old', $old);
        });

        $audit->record(
            $request->user(),
            'UPDATE',
            'Article',
            $article->id,
            oldValue: $request->attributes->get('_audit_old'),
            newValue: $article->fresh()->toArray()
        );

        return redirect()->route('admin.articles.edit', $article)
            ->with('status', 'लेख अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Article $article): RedirectResponse
    {
        $old = $article->toArray();
        DB::transaction(function () use ($article) {
            $article->articleTags()->delete();
            $article->delete();
        });
        $audit->record($request->user(), 'DELETE', 'Article', $article->id, oldValue: $old);

        return redirect()->route('admin.articles.index')
            ->with('status', 'लेख मेटाइयो।');
    }

    public function publish(Request $request, AuditService $audit, Article $article): RedirectResponse
    {
        $old = $article->only(['status', 'published_at']);
        $article->update([
            'status' => 'PUBLISHED',
            'published_at' => $article->published_at ?? now(),
        ]);
        $audit->record($request->user(), 'PUBLISH', 'Article', $article->id, oldValue: $old, newValue: $article->only(['status', 'published_at']));

        return back()->with('status', 'लेख प्रकाशित गरियो।');
    }

    public function archive(Request $request, AuditService $audit, Article $article): RedirectResponse
    {
        $old = $article->only('status');
        $article->update(['status' => 'ARCHIVED']);
        $audit->record($request->user(), 'ARCHIVE', 'Article', $article->id, oldValue: $old, newValue: ['status' => 'ARCHIVED']);

        return back()->with('status', 'लेख संग्रहित गरियो।');
    }

    private function validateArticle(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'.($id ? ",{$id}" : '')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'excerpt_en' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'status' => ['required', 'in:DRAFT,PENDING,PUBLISHED,ARCHIVED'],
            'is_featured' => ['nullable'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'exists:tags,id'],
        ]);
    }

    private function prepareForSave(array $data, Request $request): array
    {
        $data['is_featured'] = $request->boolean('is_featured');
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['author_id'] = $request->user()->id;
        $data['word_count'] = str_word_count(strip_tags($data['content']));
        $data['reading_time'] = max(1, (int) ceil($data['word_count'] / 200));

        return $data;
    }

    private function syncTags(Article $article, array $tagIds): void
    {
        $article->articleTags()->delete();
        foreach (array_unique(array_filter($tagIds)) as $tagId) {
            ArticleTag::query()->create([
                'article_id' => $article->id,
                'tag_id' => $tagId,
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreakingNewsController extends Controller
{
    public function index(): View
    {
        $items = BreakingNews::query()
            ->with('article:id,title,slug')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.breaking-news.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.breaking-news.form', [
            'item' => new BreakingNews(),
            'articles' => $this->articleOptions(),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active', true);

        $item = BreakingNews::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'BreakingNews', $item->id, newValue: $item->toArray());

        return redirect()->route('admin.breaking-news.index')->with('status', 'ब्रेकिङ न्युज थपियो।');
    }

    public function edit(BreakingNews $breakingNews): View
    {
        return view('admin.breaking-news.form', [
            'item' => $breakingNews,
            'articles' => $this->articleOptions(),
        ]);
    }

    public function update(Request $request, AuditService $audit, BreakingNews $breakingNews): RedirectResponse
    {
        $data = $this->validateData($request, $breakingNews->id);
        $data['is_active'] = $request->boolean('is_active');

        $old = $breakingNews->toArray();
        $breakingNews->update($data);
        $audit->record($request->user(), 'UPDATE', 'BreakingNews', $breakingNews->id, oldValue: $old, newValue: $breakingNews->fresh()->toArray());

        return redirect()->route('admin.breaking-news.index')->with('status', 'ब्रेकिङ न्युज अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, BreakingNews $breakingNews): RedirectResponse
    {
        $old = $breakingNews->toArray();
        $breakingNews->delete();
        $audit->record($request->user(), 'DELETE', 'BreakingNews', $breakingNews->id, oldValue: $old);

        return redirect()->route('admin.breaking-news.index')->with('status', 'ब्रेकिङ न्युज मेटाइयो।');
    }

    private function validateData(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'article_id' => ['nullable', 'string', 'exists:articles,id'],
            'url' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'integer'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['nullable'],
        ]);
    }

    private function articleOptions()
    {
        return Article::query()
            ->where('status', 'PUBLISHED')
            ->orderByDesc('published_at')
            ->limit(200)
            ->get(['id', 'title']);
    }
}
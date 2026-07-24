<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('articles')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(), 'parents' => $this->parentOptions()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Category', $category->id, newValue: $category->toArray());

        return redirect()->route('admin.categories.index')->with('status', 'वर्ग सिर्जना गरियो।');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => $this->parentOptions($category->id),
        ]);
    }

    public function update(Request $request, AuditService $audit, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category->id);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $old = $category->toArray();
        $category->update($data);
        $audit->record($request->user(), 'UPDATE', 'Category', $category->id, oldValue: $old, newValue: $category->fresh()->toArray());

        return redirect()->route('admin.categories.index')->with('status', 'वर्ग अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Category $category): RedirectResponse
    {
        $old = $category->toArray();
        $category->delete();
        $audit->record($request->user(), 'DELETE', 'Category', $category->id, oldValue: $old);

        return redirect()->route('admin.categories.index')->with('status', 'वर्ग मेटाइयो।');
    }

    private function validateData(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'.($id ? ",{$id}" : '')],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
            'parent_id' => ['nullable', 'string', 'exists:categories,id'],
            'is_active' => ['nullable'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function parentOptions(?string $excludeId = null)
    {
        $q = Category::query()->orderBy('name');
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }

        return $q->get(['id', 'name']);
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('articles')
            ->with(['children' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->withCount('articles')])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category) => $this->payload($category));

        return ApiResponse::success($categories);
    }

    public function store(Request $request, AuditService $audit): JsonResponse
    {
        $data = $this->validated($request);
        if (Category::query()->where('slug', $data['slug'])->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }

        $category = Category::query()->create($data);
        $category->setAttribute('articles_count', 0);
        $audit->record($request->user(), 'CREATE', 'Category', $category->id, newValue: [
            'name' => $category->name,
            'slug' => $category->slug,
        ]);

        return ApiResponse::success($this->payload($category), 201);
    }

    public function update(Request $request, AuditService $audit): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $category = Category::query()->find($request->string('id')->toString());
        if (! $category) {
            return ApiResponse::error('वर्ग फेला परेन', 404);
        }

        $data = $this->validated($request);
        if (Category::query()->where('slug', $data['slug'])->whereKeyNot($category->id)->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $oldValue = ['name' => $category->name, 'slug' => $category->slug, 'is_active' => $category->is_active];
        $category->update($data);
        $category->loadCount('articles');
        $audit->record($request->user(), 'UPDATE', 'Category', $category->id, $oldValue, [
            'name' => $category->name,
            'slug' => $category->slug,
            'is_active' => $category->is_active,
        ]);

        return ApiResponse::success($this->payload($category));
    }

    public function destroy(Request $request, AuditService $audit): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $category = Category::query()
            ->withCount(['articles', 'children'])
            ->find($request->string('id')->toString());

        if (! $category) {
            return ApiResponse::error('वर्ग फेला परेन', 404);
        }
        if ($category->articles_count > 0 || $category->children_count > 0) {
            return ApiResponse::error('लेख वा उप-वर्ग भएको वर्ग मेट्न सकिँदैन', 409);
        }

        $audit->record($request->user(), 'DELETE', 'Category', $category->id, oldValue: [
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
        $category->delete();

        return ApiResponse::success(['id' => $category->id]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['sometimes', 'string', 'max:32'],
            'sort_order' => ['sometimes', 'integer'],
            'parent_id' => ['nullable', 'string', 'exists:categories,id'],
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(Category $category): array
    {
        $data = $category->attributesToArray();
        $data['_count'] = ['articles' => (int) ($category->articles_count ?? 0)];
        $data['children'] = $category->relationLoaded('children')
            ? $category->children->map(fn (Category $child) => $this->payload($child))->values()
            : [];

        return $data;
    }
}

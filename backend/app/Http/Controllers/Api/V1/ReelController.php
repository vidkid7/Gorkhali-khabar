<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $canIncludeInactive = $request->boolean('includeInactive')
            && $request->user()?->is_active
            && $request->user()->hasRole('ADMIN');
        $query = Reel::query()
            ->when(! $canIncludeInactive, fn ($query) => $query->where('is_active', true))
            ->orderByDesc('created_at');
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 20)));
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->forPage($page, $pageSize)->get(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $reel = Reel::query()->where('is_active', true)->find($id);

        return $reel ? ApiResponse::success($reel) : ApiResponse::error('रिल फेला परेन', 404);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $reel = Reel::query()->where('is_active', true)->where('slug', $slug)->first();

        return $reel ? ApiResponse::success($reel) : ApiResponse::error('रिल फेला परेन', 404);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $data = $this->validated($request);
        if (Reel::query()->where('slug', $data['slug'])->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }

        return ApiResponse::success(Reel::query()->create($data), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $reel = Reel::query()->find($id);
        if (! $reel) {
            return ApiResponse::error('रिल फेला परेन', 404);
        }
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $data = $this->validated($request, false);
        unset($data['slug']);
        $reel->update($data);

        return ApiResponse::success($reel->fresh());
    }

    public function destroy(string $id): JsonResponse
    {
        $reel = Reel::query()->find($id);
        if (! $reel) {
            return ApiResponse::error('रिल फेला परेन', 404);
        }
        $reel->delete();

        return ApiResponse::success(['id' => $id]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $creating = true): array
    {
        return $request->validate([
            'title' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'video_url' => [$creating ? 'required' : 'sometimes', 'string', 'max:2048'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}

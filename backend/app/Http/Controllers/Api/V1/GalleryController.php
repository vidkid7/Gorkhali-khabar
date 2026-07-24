<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $canIncludeInactive = $request->boolean('includeInactive')
            && $request->user()?->is_active
            && $request->user()->hasRole('ADMIN');
        $query = Gallery::query()
            ->when(! $canIncludeInactive, fn ($query) => $query->where('is_active', true))
            ->withCount('images')
            ->orderByDesc('created_at');
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 20)));
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->forPage($page, $pageSize)->get()->map(fn (Gallery $gallery): array => $this->payload($gallery)),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $gallery = Gallery::query()->where('is_active', true)->withCount('images')->with('images')->find($id);

        return $gallery ? ApiResponse::success($this->payload($gallery)) : ApiResponse::error('ग्यालरी फेला परेन', 404);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $data = $this->validated($request);
        if (Gallery::query()->where('slug', $data['slug'])->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }
        $images = $data['images'] ?? [];
        unset($data['images']);
        $gallery = DB::transaction(function () use ($data, $images): Gallery {
            $gallery = Gallery::query()->create($data);
            $this->replaceImages($gallery, $images);

            return $gallery->loadCount('images')->load('images');
        });

        return ApiResponse::success($this->payload($gallery), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $gallery = Gallery::query()->find($id);
        if (! $gallery) {
            return ApiResponse::error('ग्यालरी फेला परेन', 404);
        }
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $data = $this->validated($request, false);
        $images = $data['images'] ?? null;
        unset($data['images'], $data['slug']);
        $gallery = DB::transaction(function () use ($gallery, $data, $images): Gallery {
            $gallery->update($data);
            if ($images !== null) {
                $this->replaceImages($gallery, $images);
            }

            return $gallery->fresh()->loadCount('images')->load('images');
        });

        return ApiResponse::success($this->payload($gallery));
    }

    public function destroy(string $id): JsonResponse
    {
        $gallery = Gallery::query()->find($id);
        if (! $gallery) {
            return ApiResponse::error('ग्यालरी फेला परेन', 404);
        }
        DB::transaction(fn () => $gallery->delete());

        return ApiResponse::success(['id' => $id]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $creating = true): array
    {
        return $request->validate([
            'title' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'images' => ['sometimes', 'array'],
            'images.*.url' => ['required_with:images', 'string', 'max:2048'],
            'images.*.caption' => ['nullable', 'string'],
            'images.*.caption_en' => ['nullable', 'string'],
            'images.*.sort_order' => ['sometimes', 'integer'],
        ]);
    }

    /** @param list<array<string, mixed>> $images */
    private function replaceImages(Gallery $gallery, array $images): void
    {
        GalleryImage::query()->where('gallery_id', $gallery->id)->delete();
        foreach ($images as $index => $image) {
            GalleryImage::query()->create([
                'gallery_id' => $gallery->id,
                'url' => $image['url'],
                'caption' => $image['caption'] ?? null,
                'caption_en' => $image['caption_en'] ?? null,
                'sort_order' => $image['sort_order'] ?? $index,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function payload(Gallery $gallery): array
    {
        $data = $gallery->toArray();
        $data['_count'] = ['images' => (int) ($gallery->images_count ?? $gallery->images->count())];

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdPosition;
use App\Models\Advertisement;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $now = now();
        $query = Advertisement::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->with('position:id,name,type,width,height')
            ->orderByDesc('created_at');
        if ($request->filled('position')) {
            $query->whereHas('position', fn ($position) => $position->where('type', $request->string('position')));
        }

        return ApiResponse::success($query->get());
    }

    public function positions(): JsonResponse
    {
        return ApiResponse::success(AdPosition::query()->orderByDesc('created_at')->get());
    }

    public function storePosition(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'type' => ['required', 'in:HEADER,SIDEBAR,IN_ARTICLE,FOOTER,BETWEEN_SECTIONS,POPUP'], 'width' => ['nullable', 'integer'], 'height' => ['nullable', 'integer']]);
        if (AdPosition::query()->where('name', $data['name'])->exists()) {
            return ApiResponse::error('यो नाम पहिले नै प्रयोग भइसकेको छ', 409);
        }

        return ApiResponse::success(AdPosition::query()->create($data), 201);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        if (! AdPosition::query()->whereKey($data['position_id'])->exists()) {
            return ApiResponse::error('विज्ञापन स्थान फेला परेन', 404);
        }

        return ApiResponse::success(Advertisement::query()->create($data)->load('position:id,name,type'), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $advertisement = Advertisement::query()->find($id);
        if (! $advertisement) {
            return ApiResponse::error('विज्ञापन फेला परेन', 404);
        }
        $data = $this->validated($request, false);
        if (isset($data['position_id']) && ! AdPosition::query()->whereKey($data['position_id'])->exists()) {
            return ApiResponse::error('विज्ञापन स्थान फेला परेन', 404);
        }
        $advertisement->update($data);

        return ApiResponse::success($advertisement->fresh()->load('position:id,name,type'));
    }

    public function destroy(string $id): JsonResponse
    {
        $advertisement = Advertisement::query()->find($id);
        if (! $advertisement) {
            return ApiResponse::error('विज्ञापन फेला परेन', 404);
        }
        $advertisement->delete();

        return ApiResponse::success(['id' => $id]);
    }

    public function track(string $id, string $metric): JsonResponse
    {
        if (! request()->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $advertisement = Advertisement::query()->find($id);
        if (! $advertisement || ! in_array($metric, ['clicks', 'impressions'], true)) {
            return ApiResponse::error('विज्ञापन फेला परेन', 404);
        }
        Advertisement::query()->whereKey($id)->increment($metric);

        return ApiResponse::success(['id' => $id, $metric => (int) $advertisement->fresh()->{$metric}]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $creating = true): array
    {
        return $request->validate([
            'title' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'target_url' => [$creating ? 'required' : 'sometimes', 'string', 'max:2048'],
            'position_id' => [$creating ? 'required' : 'sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);
    }
}

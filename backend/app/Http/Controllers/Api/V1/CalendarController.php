<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\PanchangData;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarController extends Controller
{
    public function holidays(Request $request): JsonResponse
    {
        $holidays = $this->filterDate(Holiday::query(), $request)
            ->orderBy('bs_year')
            ->orderBy('bs_month')
            ->orderBy('bs_day')
            ->get();

        return $this->publicResponse($holidays);
    }

    public function panchang(Request $request): JsonResponse
    {
        $data = $this->filterDate(PanchangData::query(), $request, true)
            ->orderBy('bs_year')
            ->orderBy('bs_month')
            ->orderBy('bs_day')
            ->get();

        return $this->publicResponse($data);
    }

    public function adminIndex(): JsonResponse
    {
        return ApiResponse::success(
            Holiday::query()->orderByDesc('bs_year')->orderBy('bs_month')->orderBy('bs_day')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        return ApiResponse::success(Holiday::query()->create($this->validated($request)));
    }

    public function update(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $request->validate(['id' => ['required', 'string']]);
        $holiday = Holiday::query()->find($request->string('id')->toString());
        if (! $holiday) {
            return ApiResponse::error('Failed to update holiday', 400);
        }
        $holiday->update($this->validated($request, $holiday->id));

        return ApiResponse::success($holiday->fresh());
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $holiday = Holiday::query()->find($request->string('id')->toString());
        if (! $holiday) {
            return ApiResponse::error('Failed to delete holiday', 404);
        }
        $holiday->delete();

        return ApiResponse::success();
    }

    private function filterDate(Builder $query, Request $request, bool $includeDay = false): Builder
    {
        $year = $request->integer('year');
        $month = $request->integer('month');
        $day = $request->integer('day');

        return $query
            ->when($year > 0, fn (Builder $query) => $query->where('bs_year', $year))
            ->when($month > 0, fn (Builder $query) => $query->where('bs_month', $month))
            ->when($includeDay && $day > 0, fn (Builder $query) => $query->where('bs_day', $day));
    }

    private function publicResponse(mixed $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
        ])->header('Cache-Control', 'public, s-maxage=86400, stale-while-revalidate=172800');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('holidays')->where(fn ($query) => $query
                ->where('bs_year', $request->integer('bs_year'))
                ->where('bs_month', $request->integer('bs_month'))
                ->where('bs_day', $request->integer('bs_day')))->ignore($ignoreId)],
            'title_en' => ['nullable', 'string', 'max:255'],
            'bs_year' => ['required', 'integer', 'between:2000,2100'],
            'bs_month' => ['required', 'integer', 'between:1,12'],
            'bs_day' => ['required', 'integer', 'between:1,32'],
            'ad_date' => ['required', 'date'],
            'type' => ['sometimes', 'string', 'max:255'],
            'is_public' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Rashifal;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class RashifalController extends Controller
{
    private const SIGNS = [
        'mesh', 'brish', 'mithun', 'karkat', 'simha', 'kanya',
        'tula', 'brishchik', 'dhanu', 'makar', 'kumbha', 'meen',
    ];

    private const FALLBACKS = [
        ['आज तपाईंको दिन सकारात्मक रहनेछ। नयाँ अवसरहरू आउँदैछन्।', 'Today looks positive. New opportunities approach.'],
        ['धैर्यता र दृढताले सफलता दिलाउनेछ।', 'Patience and determination will bring success.'],
        ['परिवार र साथीहरूसँगको सम्बन्ध सुदृढ हुनेछ।', 'Relationships with family and friends will strengthen.'],
        ['स्वास्थ्य र सन्तुलित दिनचर्यामा ध्यान दिनुस्।', 'Focus on health and a balanced routine.'],
        ['नयाँ सीप सिक्ने र योजना बनाउने राम्रो दिन हो।', 'It is a good day to learn and make plans.'],
        ['साझेदारी र टोली कार्यमा सफलता मिल्नेछ।', 'Partnerships and teamwork will succeed.'],
        ['शान्त मनले निर्णय गर्दा राम्रो परिणाम आउनेछ।', 'Calm decisions will bring good results.'],
    ];

    public function index(Request $request): JsonResponse
    {
        $latestDate = Rashifal::query()->max('ad_date');
        if (! $latestDate) {
            return response()->json([
                'success' => true,
                'data' => $this->fallback(),
                'date' => now('UTC')->toDateString(),
                'source' => 'generated',
            ])->header('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=1800');
        }

        $entries = Rashifal::query()
            ->where('ad_date', $latestDate)
            ->when($request->filled('sign'), fn ($query) => $query->where('sign', $request->string('sign')))
            ->orderBy('sign')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $entries,
            'date' => Carbon::parse($latestDate)->startOfDay(),
            'source' => 'db',
        ])->header('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=1800');
    }

    public function adminIndex(): JsonResponse
    {
        return ApiResponse::success(
            Rashifal::query()->orderByDesc('ad_date')->orderBy('sign')->limit(24)->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        return ApiResponse::success(Rashifal::query()->create($this->validated($request)));
    }

    public function update(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $request->validate(['id' => ['required', 'string']]);
        $entry = Rashifal::query()->find($request->string('id')->toString());
        if (! $entry) {
            return ApiResponse::error('Failed to update rashifal entry', 400);
        }
        $entry->update($this->validated($request, $entry->id));

        return ApiResponse::success($entry->fresh());
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $entry = Rashifal::query()->find($request->string('id')->toString());
        if (! $entry) {
            return ApiResponse::error('Failed to delete rashifal entry', 404);
        }
        $entry->delete();

        return ApiResponse::success();
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'sign' => ['required', 'string', 'max:255', Rule::unique('rashifal')->where(fn ($query) => $query
                ->where('bs_year', $request->integer('bs_year'))
                ->where('bs_month', $request->integer('bs_month'))
                ->where('bs_day', $request->integer('bs_day')))->ignore($ignoreId)],
            'sign_ne' => ['nullable', 'string', 'max:255'],
            'bs_year' => ['required', 'integer', 'between:2000,2100'],
            'bs_month' => ['required', 'integer', 'between:1,12'],
            'bs_day' => ['required', 'integer', 'between:1,32'],
            'ad_date' => ['required', 'date'],
            'prediction' => ['required', 'string'],
            'prediction_en' => ['nullable', 'string'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function fallback(): array
    {
        $day = (int) floor(now('UTC')->timestamp / 86400);

        return array_map(function (string $sign, int $index) use ($day): array {
            $prediction = self::FALLBACKS[($day + $index) % count(self::FALLBACKS)];

            return [
                'sign' => $sign,
                'prediction' => $prediction[0],
                'prediction_en' => $prediction[1],
                'rating' => 3,
            ];
        }, self::SIGNS, array_keys(self::SIGNS));
    }
}

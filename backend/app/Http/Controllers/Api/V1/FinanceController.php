<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ForexRate;
use App\Models\GoldSilverPrice;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    private const CURRENCY_NAMES_NE = [
        'USD' => 'अमेरिकी डलर', 'EUR' => 'युरो', 'GBP' => 'बेलायती पाउण्ड',
        'INR' => 'भारतीय रुपैयाँ', 'CNY' => 'चिनियाँ युआन', 'AUD' => 'अष्ट्रेलियाली डलर',
        'SGD' => 'सिङ्गापुर डलर', 'CAD' => 'क्यानाडाली डलर', 'JPY' => 'जापानी येन',
        'CHF' => 'स्विस फ्र्यांक', 'SAR' => 'साउदी रियाल', 'QAR' => 'कतारी रियाल',
        'THB' => 'थाई बाट', 'AED' => 'यूएई दिर्हाम', 'MYR' => 'मलेसियाली रिंगिट',
        'KRW' => 'दक्षिण कोरियाली वन',
    ];

    public function exchangeRates(): JsonResponse
    {
        $latestDate = ForexRate::query()->max('date');
        if (! $latestDate) {
            return ApiResponse::error('No forex data', 404);
        }
        $rates = ForexRate::query()->where('date', $latestDate)->orderBy('currency')->get();

        return response()->json([
            'success' => true,
            'data' => $rates->map(fn (ForexRate $rate): array => [
                'code' => $rate->currency,
                'name' => $rate->currency_name ?? $rate->currency,
                'name_ne' => self::CURRENCY_NAMES_NE[$rate->currency] ?? $rate->currency_name ?? $rate->currency,
                'buy' => $rate->buy,
                'sell' => $rate->sell,
                'unit' => $rate->unit,
            ]),
            'date' => Carbon::parse($latestDate)->startOfDay(),
            'timestamp' => now('UTC')->toISOString(),
        ])->header('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=7200');
    }

    public function goldSilver(): JsonResponse
    {
        $latest = GoldSilverPrice::query()->orderByDesc('date')->first();
        if (! $latest) {
            return ApiResponse::error('No data available', 404);
        }
        $tolaToGram = 11.664;

        return response()->json([
            'success' => true,
            'data' => [
                'gold' => [
                    'tola_24k' => $latest->fine_gold,
                    'tola_22k' => $latest->tejabi_gold,
                    'gram_24k' => $latest->fine_gold ? round($latest->fine_gold / $tolaToGram, 1) : null,
                    'currency' => 'NPR',
                    'unit_ne' => 'तोला',
                    'unit_en' => 'Tola',
                ],
                'silver' => [
                    'tola' => $latest->silver,
                    'gram' => $latest->silver ? round($latest->silver / $tolaToGram, 1) : null,
                    'currency' => 'NPR',
                    'unit_ne' => 'तोला',
                    'unit_en' => 'Tola',
                ],
                'date' => $latest->date,
                'source' => $latest->source,
            ],
            'timestamp' => now('UTC')->toISOString(),
        ])->header('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=7200');
    }

    public function forexIndex(): JsonResponse
    {
        return ApiResponse::success(ForexRate::query()->orderByDesc('date')->orderBy('currency')->limit(50)->get());
    }

    public function forexStore(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $data = $this->validatedForex($request);
        $data['currency'] = strtoupper($data['currency']);

        return ApiResponse::success(ForexRate::query()->create($data));
    }

    public function forexUpdate(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $request->validate(['id' => ['required', 'string']]);
        $rate = ForexRate::query()->find($request->string('id')->toString());
        if (! $rate) {
            return ApiResponse::error('Failed to update forex rate', 400);
        }
        $data = $this->validatedForex($request, $rate->id);
        $data['currency'] = strtoupper($data['currency']);
        $rate->update($data);

        return ApiResponse::success($rate->fresh());
    }

    public function forexDestroy(Request $request): JsonResponse
    {
        return $this->destroyEntry($request, ForexRate::class, 'forex rate');
    }

    public function metalsIndex(): JsonResponse
    {
        return ApiResponse::success(GoldSilverPrice::query()->orderByDesc('date')->limit(30)->get());
    }

    public function metalsStore(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        return ApiResponse::success(GoldSilverPrice::query()->create($this->validatedMetals($request)));
    }

    public function metalsUpdate(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $request->validate(['id' => ['required', 'string']]);
        $price = GoldSilverPrice::query()->find($request->string('id')->toString());
        if (! $price) {
            return ApiResponse::error('Failed to update price entry', 400);
        }
        $price->update($this->validatedMetals($request, $price->id));

        return ApiResponse::success($price->fresh());
    }

    public function metalsDestroy(Request $request): JsonResponse
    {
        return $this->destroyEntry($request, GoldSilverPrice::class, 'price entry');
    }

    /** @return array<string, mixed> */
    private function validatedForex(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'currency' => ['required', 'string', 'max:255', Rule::unique('forex_rates')->where(fn ($query) => $query
                ->where('date', $request->string('date')->toString()))->ignore($ignoreId)],
            'currency_name' => ['nullable', 'string', 'max:255'],
            'unit' => ['sometimes', 'integer', 'min:1'],
            'buy' => ['nullable', 'numeric'],
            'sell' => ['nullable', 'numeric'],
        ]);
    }

    /** @return array<string, mixed> */
    private function validatedMetals(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'date' => ['required', 'date', Rule::unique('gold_silver_prices')->ignore($ignoreId)],
            'fine_gold' => ['nullable', 'numeric', 'gt:0'],
            'tejabi_gold' => ['nullable', 'numeric', 'gt:0'],
            'silver' => ['nullable', 'numeric', 'gt:0'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /** @param class-string<ForexRate|GoldSilverPrice> $model */
    private function destroyEntry(Request $request, string $model, string $name): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $entry = $model::query()->find($request->string('id')->toString());
        if (! $entry) {
            return ApiResponse::error("Failed to delete {$name}", 404);
        }
        $entry->delete();

        return ApiResponse::success();
    }
}

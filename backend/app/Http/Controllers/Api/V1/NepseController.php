<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class NepseController extends Controller
{
    private const BASE_URL = 'https://nepalstock.com.np/api/nots';

    public function index(): JsonResponse
    {
        $headers = [
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Referer' => 'https://nepalstock.com.np/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
        ];
        $index = Http::withHeaders($headers)->timeout(8)->get(self::BASE_URL.'/nepse-data/index');
        $trades = Http::withHeaders($headers)->timeout(8)->get(self::BASE_URL.'/top-ten-trade/share');

        if (! $index->successful()) {
            return response()->json([
                'live' => false,
                'indices' => [],
                'stocks' => [],
                'error' => 'NEPSE data is temporarily unavailable from nepalstock.com.np',
                'lastUpdated' => now('UTC')->toISOString(),
            ])->header('Cache-Control', 'public, s-maxage=30, stale-while-revalidate=15');
        }

        $indexData = $index->json();
        $stocks = collect($trades->json('content', []))->map(fn (array $stock): array => [
            'symbol' => $stock['symbol'] ?? '',
            'name' => $stock['securityName'] ?? $stock['symbol'] ?? '',
            'price' => $this->number($stock['lastTradedPrice'] ?? null),
            'change' => $this->number($stock['priceChange'] ?? null),
            'pct' => $this->number($stock['percentageChange'] ?? null),
            'volume' => $this->number($stock['totalTradeQuantity'] ?? null),
            'high' => $this->number($stock['highPrice'] ?? null),
            'low' => $this->number($stock['lowPrice'] ?? null),
        ])->values();

        return response()->json([
            'live' => true,
            'indices' => [
                ['name' => 'NEPSE', 'nameNe' => 'नेप्से', 'value' => $this->number($indexData['index'] ?? null), 'change' => $this->number($indexData['absoluteChange'] ?? null), 'pct' => $this->number($indexData['percentageChange'] ?? null)],
                ['name' => 'Sensitive', 'nameNe' => 'सेन्सेटिभ', 'value' => $this->number($indexData['sensitiveIndex'] ?? null), 'change' => $this->number($indexData['sensitiveIndexChange'] ?? null), 'pct' => 0],
                ['name' => 'Float', 'nameNe' => 'फ्लोट', 'value' => $this->number($indexData['floatIndex'] ?? null), 'change' => $this->number($indexData['floatIndexChange'] ?? null), 'pct' => 0],
                ['name' => 'Sensitive Float', 'nameNe' => 'सेन्सेटिभ फ्लोट', 'value' => $this->number($indexData['sensitiveFloatIndex'] ?? null), 'change' => $this->number($indexData['sensitiveFloatIndexChange'] ?? null), 'pct' => 0],
            ],
            'stocks' => $stocks,
            'lastUpdated' => now('UTC')->toISOString(),
        ])->header('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=30');
    }

    private function number(mixed $value): int|float
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $number = (float) $value;

        return fmod($number, 1.0) === 0.0 ? (int) $number : $number;
    }
}

<?php

namespace Tests\Feature\Api\V1\Utility;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NepseApiTest extends TestCase
{
    public function test_nepse_normalizes_live_indices_and_top_trades(): void
    {
        Http::fake([
            'https://nepalstock.com.np/api/nots/nepse-data/index' => Http::response([
                'index' => '2150.25',
                'absoluteChange' => '12.5',
                'percentageChange' => '0.58',
                'sensitiveIndex' => '400.1',
                'sensitiveIndexChange' => '2.1',
                'floatIndex' => '150.2',
                'floatIndexChange' => '1.2',
                'sensitiveFloatIndex' => '130.3',
                'sensitiveFloatIndexChange' => '0.8',
            ]),
            'https://nepalstock.com.np/api/nots/top-ten-trade/share' => Http::response([
                'content' => [[
                    'symbol' => 'NABIL',
                    'securityName' => 'Nabil Bank',
                    'lastTradedPrice' => '600.5',
                    'priceChange' => '5.5',
                    'percentageChange' => '0.92',
                    'totalTradeQuantity' => '12000',
                    'highPrice' => '605',
                    'lowPrice' => '590',
                ]],
            ]),
        ]);

        $this->getJson('/api/v1/nepse')
            ->assertOk()
            ->assertJsonPath('live', true)
            ->assertJsonPath('indices.0.name', 'NEPSE')
            ->assertJsonPath('indices.0.value', 2150.25)
            ->assertJsonPath('stocks.0.symbol', 'NABIL')
            ->assertJsonPath('stocks.0.volume', 12000)
            ->assertHeader('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=30');
    }

    public function test_nepse_returns_cacheable_unavailable_payload_when_upstream_fails(): void
    {
        Http::fake(fn () => Http::response([], 503));

        $this->getJson('/api/v1/nepse')
            ->assertOk()
            ->assertJsonPath('live', false)
            ->assertJsonCount(0, 'indices')
            ->assertJsonCount(0, 'stocks')
            ->assertJsonPath('error', 'NEPSE data is temporarily unavailable from nepalstock.com.np')
            ->assertHeader('Cache-Control', 'public, s-maxage=30, stale-while-revalidate=15');
    }
}

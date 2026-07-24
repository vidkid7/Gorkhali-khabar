<?php

namespace Tests\Feature\Api\V1\Utility;

use App\Models\ForexRate;
use App\Models\GoldSilverPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_finance_endpoints_return_latest_shaped_data(): void
    {
        ForexRate::query()->create(['id' => 'old-usd', 'date' => '2026-07-22', 'currency' => 'USD', 'buy' => 130, 'sell' => 131]);
        ForexRate::query()->create(['id' => 'latest-usd', 'date' => '2026-07-23', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'buy' => 136.5, 'sell' => 137.1, 'unit' => 1]);
        ForexRate::query()->create(['id' => 'latest-eur', 'date' => '2026-07-23', 'currency' => 'EUR', 'currency_name' => 'Euro', 'buy' => 145, 'sell' => 146, 'unit' => 1]);
        GoldSilverPrice::query()->create(['id' => 'old-metal', 'date' => '2026-07-22', 'fine_gold' => 100000, 'silver' => 1000]);
        GoldSilverPrice::query()->create(['id' => 'latest-metal', 'date' => '2026-07-23', 'fine_gold' => 116640, 'tejabi_gold' => 110000, 'silver' => 1166.4, 'source' => 'association']);

        $this->getJson('/api/v1/finance/exchange-rates')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.code', 'EUR')
            ->assertJsonPath('data.1.name_ne', 'अमेरिकी डलर')
            ->assertJsonPath('date', '2026-07-23T00:00:00.000000Z')
            ->assertHeader('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=7200');
        $this->getJson('/api/v1/finance/gold-silver')
            ->assertOk()
            ->assertJsonPath('data.gold.gram_24k', 10000)
            ->assertJsonPath('data.silver.gram', 100)
            ->assertJsonPath('data.source', 'association');
    }

    public function test_editor_can_manage_finance_entries_but_only_admin_can_delete(): void
    {
        $editor = $this->user('finance-editor', 'EDITOR');
        $admin = $this->user('finance-admin', 'ADMIN');

        $forex = $this->actingAs($editor)->postJson('/api/v1/admin/forex', [
            'date' => '2026-07-23',
            'currency' => 'usd',
            'currency_name' => 'US Dollar',
            'unit' => 1,
            'buy' => 136.5,
            'sell' => 137.1,
        ])->assertOk()->assertJsonPath('data.currency', 'USD');
        $forexId = $forex->json('data.id');
        $this->actingAs($editor)->putJson('/api/v1/admin/forex', [
            'id' => $forexId,
            'date' => '2026-07-23',
            'currency' => 'usd',
            'unit' => 1,
            'buy' => 137,
            'sell' => 138,
        ])->assertOk()->assertJsonPath('data.buy', 137);
        $this->actingAs($editor)->deleteJson('/api/v1/admin/forex?id='.$forexId)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/forex?id='.$forexId)->assertOk();

        $metal = $this->actingAs($editor)->postJson('/api/v1/admin/gold-silver', [
            'date' => '2026-07-23',
            'fine_gold' => 116640,
            'tejabi_gold' => 110000,
            'silver' => 1166.4,
            'source' => 'association',
        ])->assertOk();
        $metalId = $metal->json('data.id');
        $this->actingAs($editor)->getJson('/api/v1/admin/gold-silver')->assertOk()->assertJsonPath('data.0.id', $metalId);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/gold-silver?id='.$metalId)->assertOk();
    }

    public function test_finance_writes_require_json_and_valid_values(): void
    {
        $editor = $this->user('finance-validation-editor', 'EDITOR');

        $this->actingAs($editor)->call('POST', '/api/v1/admin/forex', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
        $this->actingAs($editor)->postJson('/api/v1/admin/forex', ['date' => 'invalid', 'currency' => '', 'unit' => 0])->assertStatus(400);
        $this->actingAs($editor)->postJson('/api/v1/admin/gold-silver', ['date' => 'invalid', 'fine_gold' => -1])->assertStatus(400);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->create([
            'id' => $id,
            'name' => $id,
            'email' => $id.'@example.com',
            'role' => $role,
            'is_active' => true,
        ]);
    }
}

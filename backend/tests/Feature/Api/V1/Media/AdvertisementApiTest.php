<?php

namespace Tests\Feature\Api\V1\Media;

use App\Models\AdPosition;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_ads_are_active_and_date_bounded(): void
    {
        $position = AdPosition::query()->create(['id' => 'header-position', 'name' => 'header-test', 'type' => 'HEADER']);
        Advertisement::query()->create(['id' => 'active-ad', 'title' => 'Active', 'target_url' => '/active', 'position_id' => $position->id, 'is_active' => true, 'start_date' => now()->subDay(), 'end_date' => now()->addDay()]);
        Advertisement::query()->create(['id' => 'expired-ad', 'title' => 'Expired', 'target_url' => '/expired', 'position_id' => $position->id, 'is_active' => true, 'end_date' => now()->subMinute()]);
        Advertisement::query()->create(['id' => 'disabled-ad', 'title' => 'Disabled', 'target_url' => '/disabled', 'position_id' => $position->id, 'is_active' => false]);

        $this->getJson('/api/v1/ads')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', 'active-ad')->assertJsonPath('data.0.position.name', 'header-test');
        $this->postJson('/api/v1/ads/active-ad/impression', [])->assertOk()->assertJsonPath('data.impressions', 1);
        $this->postJson('/api/v1/ads/active-ad/click', [])->assertOk()->assertJsonPath('data.clicks', 1);
    }

    public function test_admin_can_create_positions_and_ads_but_duplicate_positions_fail(): void
    {
        $admin = User::query()->create(['id' => 'ads-admin', 'name' => 'Ads Admin', 'email' => 'ads-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);
        $this->actingAs($admin)->postJson('/api/v1/ads/positions', ['name' => 'sidebar-test', 'type' => 'SIDEBAR'])->assertCreated();
        $this->actingAs($admin)->postJson('/api/v1/ads/positions', ['name' => 'sidebar-test', 'type' => 'SIDEBAR'])->assertStatus(409);
        $position = AdPosition::query()->where('name', 'sidebar-test')->firstOrFail();
        $this->actingAs($admin)->postJson('/api/v1/ads', ['title' => 'New ad', 'target_url' => '/new', 'position_id' => $position->id])->assertCreated();
    }

    public function test_tracking_requires_json_content_type(): void
    {
        $position = AdPosition::query()->create(['id' => 'tracking-position', 'name' => 'tracking-test', 'type' => 'HEADER']);
        Advertisement::query()->create(['id' => 'tracking-ad', 'title' => 'Tracking', 'target_url' => '/tracking', 'position_id' => $position->id]);

        $this->call('POST', '/api/v1/ads/tracking-ad/click', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(415);
        $this->assertDatabaseHas('advertisements', ['id' => 'tracking-ad', 'clicks' => 0]);
    }
}

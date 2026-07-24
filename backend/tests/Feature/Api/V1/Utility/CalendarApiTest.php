<?php

namespace Tests\Feature\Api\V1\Utility;

use App\Models\Holiday;
use App\Models\PanchangData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_calendar_filters_and_orders_holidays_and_panchang(): void
    {
        Holiday::query()->create(['id' => 'holiday-later', 'title' => 'Later', 'bs_year' => 2083, 'bs_month' => 5, 'bs_day' => 1, 'ad_date' => '2026-08-18']);
        Holiday::query()->create(['id' => 'holiday-first', 'title' => 'First', 'bs_year' => 2083, 'bs_month' => 4, 'bs_day' => 8, 'ad_date' => '2026-07-23']);
        PanchangData::query()->create(['id' => 'panchang-first', 'bs_year' => 2083, 'bs_month' => 4, 'bs_day' => 8, 'ad_date' => '2026-07-23', 'tithi' => 'Ashtami']);
        PanchangData::query()->create(['id' => 'panchang-second', 'bs_year' => 2083, 'bs_month' => 4, 'bs_day' => 9, 'ad_date' => '2026-07-24', 'tithi' => 'Navami']);

        $this->getJson('/api/v1/calendar/holidays?year=2083&month=4')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('data.0.id', 'holiday-first')
            ->assertHeader('Cache-Control', 'public, s-maxage=86400, stale-while-revalidate=172800');
        $this->getJson('/api/v1/calendar/panchang?year=2083&month=4&day=9')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('data.0.id', 'panchang-second');
    }

    public function test_editor_can_manage_holidays_but_only_admin_can_delete(): void
    {
        $editor = $this->user('holiday-editor', 'EDITOR');
        $admin = $this->user('holiday-admin', 'ADMIN');
        $payload = [
            'title' => 'Public Holiday',
            'title_en' => 'Public Holiday',
            'bs_year' => 2083,
            'bs_month' => 4,
            'bs_day' => 8,
            'ad_date' => '2026-07-23',
            'type' => 'public',
            'is_public' => true,
        ];

        $created = $this->actingAs($editor)->postJson('/api/v1/admin/holidays', $payload)
            ->assertOk()
            ->assertJsonPath('data.title', 'Public Holiday');
        $id = $created->json('data.id');

        $this->actingAs($editor)->getJson('/api/v1/admin/holidays')->assertOk()->assertJsonPath('data.0.id', $id);
        $this->actingAs($editor)->putJson('/api/v1/admin/holidays', [...$payload, 'id' => $id, 'title' => 'Updated Holiday'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Holiday');
        $this->actingAs($editor)->deleteJson('/api/v1/admin/holidays?id='.$id)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/holidays?id='.$id)->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('holidays', ['id' => $id]);
    }

    public function test_holiday_writes_require_json_and_valid_bikram_sambat_dates(): void
    {
        $editor = $this->user('holiday-validation-editor', 'EDITOR');

        $this->actingAs($editor)->call('POST', '/api/v1/admin/holidays', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(415);
        $this->actingAs($editor)->postJson('/api/v1/admin/holidays', [
            'title' => '',
            'bs_year' => 1999,
            'bs_month' => 13,
            'bs_day' => 33,
            'ad_date' => 'invalid',
        ])->assertStatus(400);
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

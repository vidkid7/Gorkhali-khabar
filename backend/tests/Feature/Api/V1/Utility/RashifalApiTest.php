<?php

namespace Tests\Feature\Api\V1\Utility;

use App\Models\Rashifal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RashifalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_rashifal_returns_latest_date_and_filters_by_sign(): void
    {
        $this->rashifal('old-mesh', 'mesh', '2083-01-01', '2026-04-14');
        $this->rashifal('latest-mesh', 'mesh', '2083-04-08', '2026-07-23');
        $this->rashifal('latest-brish', 'brish', '2083-04-08', '2026-07-23');

        $this->getJson('/api/v1/rashifal')
            ->assertOk()
            ->assertJsonPath('source', 'db')
            ->assertJsonPath('date', '2026-07-23T00:00:00.000000Z')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.sign', 'brish');

        $this->getJson('/api/v1/rashifal?sign=mesh')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 'latest-mesh')
            ->assertHeader('Cache-Control', 'public, s-maxage=3600, stale-while-revalidate=1800');
    }

    public function test_public_rashifal_generates_all_signs_when_database_is_empty(): void
    {
        $this->getJson('/api/v1/rashifal')
            ->assertOk()
            ->assertJsonPath('source', 'generated')
            ->assertJsonCount(12, 'data')
            ->assertJsonStructure(['date', 'data' => [['sign', 'prediction', 'prediction_en', 'rating']]]);
    }

    public function test_editor_can_manage_rashifal_but_only_admin_can_delete(): void
    {
        $reader = $this->user('rashifal-reader', 'READER');
        $editor = $this->user('rashifal-editor', 'EDITOR');
        $admin = $this->user('rashifal-admin', 'ADMIN');
        $payload = [
            'sign' => 'mesh',
            'sign_ne' => 'मेष',
            'bs_year' => 2083,
            'bs_month' => 4,
            'bs_day' => 8,
            'ad_date' => '2026-07-23',
            'prediction' => 'आज राम्रो दिन हुनेछ।',
            'prediction_en' => 'A good day.',
            'rating' => 4,
        ];

        $this->actingAs($reader)->getJson('/api/v1/admin/rashifal')->assertStatus(403);
        $created = $this->actingAs($editor)->postJson('/api/v1/admin/rashifal', $payload)
            ->assertOk()
            ->assertJsonPath('data.sign', 'mesh');
        $id = $created->json('data.id');

        $this->actingAs($editor)->getJson('/api/v1/admin/rashifal')->assertOk()->assertJsonPath('data.0.id', $id);
        $this->actingAs($editor)->putJson('/api/v1/admin/rashifal', [...$payload, 'id' => $id, 'rating' => 5])
            ->assertOk()
            ->assertJsonPath('data.rating', 5);
        $this->actingAs($editor)->deleteJson('/api/v1/admin/rashifal?id='.$id)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/rashifal?id='.$id)->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('rashifal', ['id' => $id]);
    }

    public function test_rashifal_writes_require_json_and_valid_dates(): void
    {
        $editor = $this->user('rashifal-validation-editor', 'EDITOR');

        $this->actingAs($editor)->call('POST', '/api/v1/admin/rashifal', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(415);
        $this->actingAs($editor)->postJson('/api/v1/admin/rashifal', [
            'sign' => 'mesh',
            'bs_year' => 1999,
            'bs_month' => 13,
            'bs_day' => 33,
            'ad_date' => 'not-a-date',
            'prediction' => '',
            'rating' => 6,
        ])->assertStatus(400);
    }

    private function rashifal(string $id, string $sign, string $bsDate, string $adDate): Rashifal
    {
        [$year, $month, $day] = array_map('intval', explode('-', $bsDate));

        return Rashifal::query()->create([
            'id' => $id,
            'sign' => $sign,
            'bs_year' => $year,
            'bs_month' => $month,
            'bs_day' => $day,
            'ad_date' => $adDate,
            'prediction' => $id,
            'rating' => 3,
        ]);
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

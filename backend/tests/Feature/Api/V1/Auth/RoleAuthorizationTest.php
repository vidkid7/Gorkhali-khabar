<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['api', 'auth:sanctum', 'active.session', 'role:ADMIN,EDITOR'])
            ->get('/api/v1/testing/editorial', static fn () => ['success' => true]);
    }

    public function test_reader_is_forbidden_and_editor_is_allowed(): void
    {
        $reader = User::query()->create([
            'id' => 'role-reader',
            'email' => 'role-reader@example.com',
            'role' => 'READER',
            'is_active' => true,
        ]);
        $editor = User::query()->create([
            'id' => 'role-editor',
            'email' => 'role-editor@example.com',
            'role' => 'EDITOR',
            'is_active' => true,
        ]);

        $this->actingAs($reader)->getJson('/api/v1/testing/editorial')->assertStatus(403);
        $this->actingAs($editor)->getJson('/api/v1/testing/editorial')->assertOk();
    }
}
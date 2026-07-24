<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['api', 'auth:sanctum', 'active.session', 'role:ADMIN'])
            ->get('/api/v1/testing/admin-only', static fn () => ['success' => true]);
    }

    public function test_unauthenticated_requests_receive_401_envelope(): void
    {
        $this->getJson('/api/v1/testing/admin-only')
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'error' => 'Authentication required']);
    }

    public function test_authenticated_users_without_the_required_role_receive_403(): void
    {
        $user = User::query()->create([
            'id' => 'reader-user',
            'email' => 'reader@example.com',
            'role' => 'READER',
            'is_active' => true,
            'session_version' => 0,
        ]);

        $this->actingAs($user)->getJson('/api/v1/testing/admin-only')
            ->assertStatus(403)
            ->assertExactJson(['success' => false, 'error' => 'Forbidden']);
    }

    public function test_inactive_users_are_rejected_even_with_a_staff_role(): void
    {
        $user = User::query()->create([
            'id' => 'inactive-admin',
            'email' => 'inactive-admin@example.com',
            'role' => 'ADMIN',
            'is_active' => false,
            'session_version' => 0,
        ]);

        $this->actingAs($user)->getJson('/api/v1/testing/admin-only')
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'error' => 'Authentication required']);
    }
}
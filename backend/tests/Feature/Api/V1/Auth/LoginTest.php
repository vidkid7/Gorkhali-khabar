<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_bcryptjs_hash_can_login_and_load_session(): void
    {
        $phpHash = password_hash('Valid@123', PASSWORD_BCRYPT);
        User::query()->create([
            'id' => 'existing-reader',
            'name' => 'Existing Reader',
            'email' => 'reader@example.com',
            'image' => '/reader.jpg',
            'password_hash' => '$2b$'.substr($phpHash, 4),
            'role' => 'READER',
            'is_active' => true,
            'session_version' => 3,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => '  READER@EXAMPLE.COM ',
            'password' => 'Valid@123',
        ], $this->spaHeaders())
            ->assertOk()
            ->assertJsonPath('data.user.id', 'existing-reader')
            ->assertJsonPath('data.user.image', '/reader.jpg')
            ->assertJsonPath('data.user.session_version', 3);

        $this->getJson('/api/v1/auth/session', $this->spaHeaders())
            ->assertOk()
            ->assertJsonPath('data.user.email', 'reader@example.com');
    }

    public function test_five_recent_failures_lock_the_account(): void
    {
        $user = User::query()->create([
            'id' => 'lockable-reader',
            'email' => 'lockable@example.com',
            'password_hash' => password_hash('Valid@123', PASSWORD_BCRYPT),
            'role' => 'READER',
            'is_active' => true,
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'lockable@example.com',
                'password' => 'Wrong@123',
            ], $this->spaHeaders())->assertStatus(401);
        }

        $user->refresh();
        $this->assertSame(5, $user->failed_login_count);
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());
    }

    public function test_inactive_accounts_cannot_login(): void
    {
        User::query()->create([
            'id' => 'inactive-reader',
            'email' => 'inactive@example.com',
            'password_hash' => password_hash('Valid@123', PASSWORD_BCRYPT),
            'role' => 'READER',
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'Valid@123',
        ], $this->spaHeaders())->assertStatus(401);
    }

    public function test_unknown_accounts_receive_the_same_credential_failure(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'unknown@example.com',
            'password' => 'Valid@123',
        ])->assertStatus(401)->assertJsonPath('error', 'Invalid credentials');
    }

    public function test_staff_seed_password_is_rejected_in_production(): void
    {
        $this->app->instance('env', 'production');
        putenv('SEED_ADMIN_PASSWORD=Admin@12345');
        $_ENV['SEED_ADMIN_PASSWORD'] = 'Admin@12345';

        User::query()->create([
            'id' => 'seed-admin',
            'email' => 'admin@example.com',
            'password_hash' => password_hash('Admin@12345', PASSWORD_BCRYPT),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'Admin@12345',
        ])->assertStatus(401);
    }

    public function test_stale_session_version_is_rejected(): void
    {
        $user = User::query()->create([
            'id' => 'stale-reader',
            'email' => 'stale@example.com',
            'role' => 'READER',
            'is_active' => true,
            'session_version' => 2,
        ]);

        $this->actingAs($user)
            ->withSession(['session_version' => 1])
            ->getJson('/api/v1/auth/session', $this->spaHeaders())
            ->assertStatus(401);
    }

    public function test_stateful_writes_require_csrf_outside_testing(): void
    {
        $this->app->instance('env', 'local');

        $this->postJson('/api/v1/auth/login', [
            'email' => 'unknown@example.com',
            'password' => 'Valid@123',
        ], $this->spaHeaders())->assertStatus(419);
    }

    public function test_logout_invalidates_the_session(): void
    {
        $user = User::query()->create([
            'id' => 'logout-reader',
            'email' => 'logout@example.com',
            'role' => 'READER',
            'is_active' => true,
        ]);

        $this->actingAs($user)->postJson('/api/v1/auth/logout', [], $this->spaHeaders())->assertOk();
        $this->getJson('/api/v1/auth/session', $this->spaHeaders())->assertStatus(401);
    }

    private function spaHeaders(): array
    {
        return ['Origin' => 'http://localhost:8080', 'Referer' => 'http://localhost:8080/'];
    }
}
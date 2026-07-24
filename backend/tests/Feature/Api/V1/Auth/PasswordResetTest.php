<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_token_resets_password_once_and_invalidates_sessions(): void
    {
        $user = User::query()->create([
            'id' => 'reset-reader',
            'email' => 'reset@example.com',
            'password_hash' => password_hash('Old@1234', PASSWORD_BCRYPT),
            'session_version' => 2,
        ]);
        $token = str_repeat('a', 64);
        PasswordResetToken::query()->create([
            'id' => 'reset-token',
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHour(),
            'used' => false,
        ]);

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'password' => 'NewStrong@123',
        ])->assertOk();

        $user->refresh();
        $this->assertSame(3, $user->session_version);
        $this->assertTrue(password_verify('NewStrong@123', $user->password_hash));
        $this->assertTrue(PasswordResetToken::query()->findOrFail('reset-token')->used);

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'password' => 'Another@123',
        ])->assertStatus(400);
    }
}
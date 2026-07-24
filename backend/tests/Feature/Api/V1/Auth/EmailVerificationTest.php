<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_token_verifies_email_once(): void
    {
        $user = User::query()->create(['id' => 'verify-reader', 'email' => 'verify@example.com']);
        $token = str_repeat('b', 64);
        EmailVerificationToken::query()->create([
            'id' => 'verification-token',
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHour(),
            'used' => false,
        ]);

        $this->postJson('/api/v1/auth/verify-email', ['token' => $token])->assertOk();
        $this->assertNotNull($user->fresh()->email_verified);
        $this->assertTrue(EmailVerificationToken::query()->findOrFail('verification-token')->used);
        $this->postJson('/api/v1/auth/verify-email', ['token' => $token])->assertStatus(400);
    }
}
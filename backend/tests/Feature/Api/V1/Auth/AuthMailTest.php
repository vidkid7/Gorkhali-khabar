<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Mail\PasswordResetMail;
use App\Mail\VerifyEmailMail;
use App\Models\EmailVerificationToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_queues_mail_without_exposing_unknown_addresses(): void
    {
        Mail::fake();
        $user = User::query()->create([
            'id' => 'forgot-reader',
            'name' => 'Forgot Reader',
            'email' => 'forgot@example.com',
            'language' => 'en',
        ]);
        PasswordResetToken::query()->create([
            'id' => 'old-reset-token',
            'user_id' => $user->id,
            'token_hash' => hash('sha256', 'old-token'),
            'expires_at' => now()->addHour(),
            'used' => false,
        ]);

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => '  FORGOT@EXAMPLE.COM ',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertTrue(PasswordResetToken::query()->findOrFail('old-reset-token')->used);
        $this->assertSame(1, PasswordResetToken::query()->where('user_id', $user->id)->where('used', false)->count());
        Mail::assertQueued(PasswordResetMail::class, fn (PasswordResetMail $mail) => $mail->hasTo($user->email));

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'unknown@example.com',
        ])->assertOk()->assertJsonPath('success', true);
        Mail::assertQueuedCount(1);
    }

    public function test_send_verification_requires_an_unverified_authenticated_user(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/auth/send-verification')->assertStatus(401);

        $user = User::query()->create([
            'id' => 'verification-reader',
            'email' => 'verification@example.com',
            'is_active' => true,
        ]);

        $this->actingAs($user)->postJson('/api/v1/auth/send-verification')->assertOk();
        $this->assertSame(1, EmailVerificationToken::query()->where('user_id', $user->id)->where('used', false)->count());
        Mail::assertQueued(VerifyEmailMail::class, fn (VerifyEmailMail $mail) => $mail->hasTo($user->email));

        $user->update(['email_verified' => now()]);
        $this->actingAs($user)->postJson('/api/v1/auth/send-verification')->assertStatus(400);
    }
}
<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_auth_is_unavailable_without_both_credentials(): void
    {
        config()->set('services.google.client_id');
        config()->set('services.google.client_secret');

        $this->getJson('/api/v1/auth/google/redirect')
            ->assertStatus(404)
            ->assertJsonPath('error', 'Google authentication is not configured');
    }

    public function test_google_callback_links_an_existing_user_by_verified_email(): void
    {
        config()->set('services.google.client_id', 'client-id');
        config()->set('services.google.client_secret', 'client-secret');
        $user = User::query()->create([
            'id' => 'google-reader',
            'email' => 'google@example.com',
            'role' => 'READER',
            'is_active' => true,
        ]);
        $providerUser = new SocialiteUser;
        $providerUser->id = 'google-account';
        $providerUser->name = 'Google Reader';
        $providerUser->email = 'GOOGLE@EXAMPLE.COM';
        $providerUser->avatar = '/google.jpg';
        $providerUser->token = 'access-token';
        $providerUser->refreshToken = 'refresh-token';
        $providerUser->expiresIn = 3600;
        $providerUser->user = ['email_verified' => true];

        $provider = Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn($providerUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get('/api/v1/auth/google/callback')->assertRedirect();

        $this->assertNotNull($user->fresh()->email_verified);
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-account',
        ]);
    }
}
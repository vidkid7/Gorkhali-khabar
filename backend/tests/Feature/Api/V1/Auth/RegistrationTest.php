<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_registration_normalizes_email_and_hashes_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'New Reader',
            'email' => '  NEW@EXAMPLE.COM ',
            'password' => 'Strong@123',
        ])->assertStatus(201)->assertJsonPath('data.email', 'new@example.com');

        $user = User::query()->where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('READER', $user->role);
        $this->assertTrue(password_verify('Strong@123', '$2y$'.substr($user->password_hash, 4)));
    }

    public function test_duplicate_email_returns_conflict(): void
    {
        User::query()->create(['id' => 'duplicate', 'email' => 'duplicate@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Duplicate Reader',
            'email' => 'duplicate@example.com',
            'password' => 'Strong@123',
        ])->assertStatus(409);
    }

    public function test_password_complexity_is_enforced(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Weak Password',
            'email' => 'weak@example.com',
            'password' => 'password',
        ])->assertStatus(400)->assertJsonPath('success', false);
    }
}
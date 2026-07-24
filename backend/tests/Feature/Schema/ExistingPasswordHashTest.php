<?php

namespace Tests\Feature\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExistingPasswordHashTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_preserves_and_verifies_an_existing_bcrypt_hash(): void
    {
        $phpHash = password_hash('legacy-password', PASSWORD_BCRYPT);
        $hash = '$2b$'.substr($phpHash, 4);

        DB::table('users')->insert([
            'id' => 'legacy-user-id',
            'email' => 'legacy@example.com',
            'password_hash' => $hash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $storedHash = DB::table('users')
            ->where('id', 'legacy-user-id')
            ->value('password_hash');

        $this->assertSame($hash, $storedHash);
        $this->assertTrue(password_verify('legacy-password', '$2y$'.substr($storedHash, 4)));
    }
}
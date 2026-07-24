<?php

namespace Tests\Feature\Schema;

use Database\Seeders\LegacyCompatibleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyCompatibleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_idempotent_and_preserves_existing_values(): void
    {
        DB::table('users')->insert([
            'id' => 'existing-admin',
            'name' => 'Existing Admin',
            'email' => 'admin@gorkhali.com',
            'password_hash' => 'existing-hash',
            'role' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'id' => 'existing-category',
            'name' => 'Existing Editorial Name',
            'slug' => 'samachar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seed(LegacyCompatibleSeeder::class);
        $countsAfterFirstRun = [
            'users' => DB::table('users')->count(),
            'categories' => DB::table('categories')->count(),
            'tags' => DB::table('tags')->count(),
            'quick_links' => DB::table('quick_links')->count(),
            'ad_positions' => DB::table('ad_positions')->count(),
        ];

        $this->seed(LegacyCompatibleSeeder::class);

        foreach ($countsAfterFirstRun as $table => $count) {
            $this->assertSame($count, DB::table($table)->count(), "{$table} row count changed");
        }

        $admin = DB::table('users')->where('email', 'admin@gorkhali.com')->first();
        $category = DB::table('categories')->where('slug', 'samachar')->first();

        $this->assertSame('existing-hash', $admin->password_hash);
        $this->assertSame('ADMIN', $admin->role);
        $this->assertSame('Existing Editorial Name', $category->name);
    }
}
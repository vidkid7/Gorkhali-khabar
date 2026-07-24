<?php

namespace Tests\Feature\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VerifyLegacySchemaCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_passes_for_the_compatible_schema_without_modifying_data(): void
    {
        DB::table('users')->insert([
            'id' => 'schema-check-user',
            'email' => 'schema-check@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:verify-legacy-schema')
            ->expectsOutputToContain('Legacy schema is compatible')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => 'schema-check-user']);
    }

    public function test_it_reports_missing_tables_without_creating_them(): void
    {
        Schema::drop('quick_links');

        $this->artisan('app:verify-legacy-schema')
            ->expectsOutputToContain('Missing table: quick_links')
            ->assertFailed();

        $this->assertFalse(Schema::hasTable('quick_links'));
    }
}
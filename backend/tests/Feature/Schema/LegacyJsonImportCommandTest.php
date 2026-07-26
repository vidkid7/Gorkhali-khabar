<?php

namespace Tests\Feature\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacyJsonImportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_preserves_records_and_settings_and_is_idempotent(): void
    {
        $path = $this->writeFixture([
            'users' => [[
                'id' => 'user-1',
                'name' => 'Existing Editor',
                'email' => 'editor@example.test',
                'password_hash' => '$2b$12$example-hash-preserved-verbatim',
                'role' => 'EDITOR',
                'is_active' => true,
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
            'categories' => [[
                'id' => 'category-1',
                'name' => 'समाचार',
                'slug' => 'samachar',
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
            'articles' => [[
                'id' => 'article-1',
                'title' => 'Preserved title',
                'slug' => 'preserved-title',
                'content' => '<p>Preserved body</p>',
                'status' => 'PUBLISHED',
                'category_id' => 'category-1',
                'author_id' => 'user-1',
                'published_at' => '2025-01-04T03:04:05+00:00',
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
            'site_settings' => [[
                'id' => 'setting-1',
                'key' => 'homepage_section_order',
                'value' => ['featured', 'latest', 'province'],
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
        ]);

        $this->artisan('legacy:import-json', ['path' => $path])->assertSuccessful();
        $this->artisan('legacy:import-json', ['path' => $path])->assertSuccessful();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('articles', 1);
        $this->assertDatabaseCount('site_settings', 1);
        $this->assertSame(
            '$2b$12$example-hash-preserved-verbatim',
            DB::table('users')->where('id', 'user-1')->value('password_hash')
        );
        $this->assertSame(
            ['featured', 'latest', 'province'],
            json_decode(DB::table('site_settings')->where('key', 'homepage_section_order')->value('value'), true)
        );
    }

    public function test_dry_run_validates_without_writing(): void
    {
        $path = $this->writeFixture([
            'site_settings' => [[
                'id' => 'setting-1',
                'key' => 'site_name',
                'value' => 'Gorkhali Khabar',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
        ]);

        $this->artisan('legacy:import-json', ['path' => $path, '--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_malformed_later_row_rolls_back_the_complete_import(): void
    {
        $path = $this->writeFixture([
            'categories' => [[
                'id' => 'category-1',
                'name' => 'News',
                'slug' => 'news',
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
            'articles' => [[
                'id' => 'article-without-slug',
                'title' => 'Invalid article',
                'content' => 'Body',
                'category_id' => 'category-1',
                'author_id' => 'missing-user',
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
        ]);

        $this->artisan('legacy:import-json', ['path' => $path])->assertFailed();

        $this->assertDatabaseCount('categories', 0);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_unknown_tables_and_columns_are_rejected(): void
    {
        $unknownTable = $this->writeFixture(['unknown_table' => [['id' => '1']]]);
        $this->artisan('legacy:import-json', ['path' => $unknownTable])->assertFailed();

        $unknownColumn = $this->writeFixture([
            'site_settings' => [[
                'id' => 'setting-1',
                'key' => 'site_name',
                'value' => 'Gorkhali',
                'updated_at' => '2025-01-03T03:04:05+00:00',
                'secret_extra_column' => 'must-not-be-silently-dropped',
            ]],
        ]);
        $this->artisan('legacy:import-json', ['path' => $unknownColumn])->assertFailed();
    }

    public function test_ephemeral_auth_is_skipped_and_oauth_tokens_are_cleared(): void
    {
        $path = $this->writeFixture([
            'users' => [[
                'id' => 'user-1',
                'email' => 'reader@example.test',
                'role' => 'READER',
                'created_at' => '2025-01-02T03:04:05+00:00',
                'updated_at' => '2025-01-03T03:04:05+00:00',
            ]],
            'accounts' => [[
                'id' => 'account-1',
                'user_id' => 'user-1',
                'type' => 'oauth',
                'provider' => 'google',
                'provider_account_id' => 'google-1',
                'access_token' => 'secret-access',
                'refresh_token' => 'secret-refresh',
                'id_token' => 'secret-id',
            ]],
            'sessions' => [[
                'id' => 'session-1',
                'session_token' => 'secret-session',
                'user_id' => 'user-1',
                'expires' => '2030-01-01T00:00:00+00:00',
            ]],
        ]);

        $this->artisan('legacy:import-json', ['path' => $path])->assertSuccessful();

        $account = DB::table('accounts')->where('id', 'account-1')->first();
        $this->assertNotNull($account);
        $this->assertNull($account->access_token);
        $this->assertNull($account->refresh_token);
        $this->assertNull($account->id_token);
        $this->assertDatabaseCount('sessions', 0);
    }

    /** @param array<string, list<array<string, mixed>>> $tables */
    private function writeFixture(array $tables): string
    {
        Storage::fake('local');
        $path = storage_path('framework/testing/legacy-import-'.uniqid().'.json');
        file_put_contents($path, json_encode([
            'format' => 'gorkhali-legacy-export-v1',
            'exported_at' => '2026-07-26T00:00:00+00:00',
            'tables' => $tables,
        ], JSON_THROW_ON_ERROR));

        return $path;
    }
}

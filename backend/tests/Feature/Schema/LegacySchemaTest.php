<?php

namespace Tests\Feature\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacySchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_contains_every_legacy_table(): void
    {
        $tables = [
            'users', 'accounts', 'sessions', 'verification_tokens',
            'password_reset_tokens', 'email_verification_tokens', 'categories',
            'tags', 'articles', 'article_tags', 'comments', 'comment_votes',
            'bookmarks', 'media_files', 'tournaments', 'teams', 'matches',
            'reels', 'galleries', 'gallery_images', 'ad_positions',
            'advertisements', 'breaking_news', 'web_stories', 'page_views',
            'site_settings', 'newsletter_subscriptions', 'quick_links',
            'audit_logs', 'holidays', 'panchang_data', 'gold_silver_prices',
            'forex_rates', 'rashifal',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing {$table}");
        }
    }

    public function test_it_contains_representative_legacy_columns(): void
    {
        $columns = [
            'users' => [
                'id', 'email', 'email_verified', 'password_hash', 'role',
                'is_active', 'failed_login_count', 'locked_until', 'session_version',
            ],
            'articles' => [
                'id', 'slug', 'content', 'status', 'category_id', 'author_id',
                'published_at', 'view_count', 'comment_count',
            ],
            'comments' => [
                'id', 'content', 'status', 'article_id', 'user_id', 'parent_id',
                'like_count', 'dislike_count',
            ],
            'media_files' => [
                'id', 'filename', 'original_name', 'mime_type', 'size', 'url',
                'variants', 'uploaded_by', 'created_at',
            ],
        ];

        foreach ($columns as $table => $requiredColumns) {
            $this->assertTrue(
                Schema::hasColumns($table, $requiredColumns),
                "{$table} is missing one or more legacy columns",
            );
        }
    }
}
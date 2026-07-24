<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class VerifyLegacySchema extends Command
{
    protected $signature = 'app:verify-legacy-schema';
    protected $description = 'Verify the legacy news schema without modifying it';

    private const TABLES = [
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

    private const COLUMNS = [
        'users' => ['id', 'email', 'password_hash', 'role', 'is_active', 'session_version'],
        'articles' => ['id', 'slug', 'content', 'status', 'category_id', 'author_id'],
        'comments' => ['id', 'content', 'status', 'article_id', 'user_id'],
        'media_files' => ['id', 'filename', 'mime_type', 'url', 'uploaded_by'],
    ];

    public function handle(): int
    {
        $valid = true;

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing table: {$table}");
                $valid = false;
            }
        }

        foreach (self::COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $this->error("Missing column: {$table}.{$column}");
                    $valid = false;
                }
            }
        }

        if (! $valid) {
            return self::FAILURE;
        }

        $this->info('Legacy schema is compatible.');

        return self::SUCCESS;
    }
}
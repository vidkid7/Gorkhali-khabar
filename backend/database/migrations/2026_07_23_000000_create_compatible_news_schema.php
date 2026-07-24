<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $create = static function (string $table, callable $definition): void {
            if (! Schema::hasTable($table)) {
                Schema::create($table, $definition);
            }
        };

        $id = static function (Blueprint $table): void {
            $table->string('id')->primary();
        };
        $created = static function (Blueprint $table): void {
            $table->timestampTz('created_at')->useCurrent();
        };
        $updated = static function (Blueprint $table): void {
            $table->timestampTz('updated_at')->useCurrent();
        };

        $addColumn = static function (string $table, string $column, callable $definition): void {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, $column)) {
                Schema::table($table, $definition);
            }
        };

        $create('users', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestampTz('email_verified')->nullable();
            $table->string('password_hash')->nullable();
            $table->string('image')->nullable();
            $table->string('role')->default('READER');
            $table->string('theme')->default('light');
            $table->string('admin_theme')->default('light');
            $table->string('language')->default('ne');
            $table->boolean('is_active')->default(true);
            $table->integer('failed_login_count')->default(0);
            $table->timestampTz('locked_until')->nullable();
            $table->timestampTz('last_failed_login_at')->nullable();
            $table->integer('session_version')->default(0);
            $table->text('totp_secret')->nullable();
            $table->boolean('totp_enabled')->default(false);
            $created($table);
            $updated($table);
        });

        $create('accounts', static function (Blueprint $table) use ($id): void {
            $id($table);
            $table->string('user_id');
            $table->string('type');
            $table->string('provider');
            $table->string('provider_account_id');
            $table->text('refresh_token')->nullable();
            $table->text('access_token')->nullable();
            $table->integer('expires_at')->nullable();
            $table->string('token_type')->nullable();
            $table->string('scope')->nullable();
            $table->text('id_token')->nullable();
            $table->string('session_state')->nullable();
            $table->unique(['provider', 'provider_account_id']);
            $table->index('user_id');
        });

        $create('sessions', static function (Blueprint $table) use ($id): void {
            $id($table);
            $table->string('session_token')->unique();
            $table->string('user_id');
            $table->timestampTz('expires');
            $table->index('user_id');
        });
        $create('verification_tokens', static function (Blueprint $table): void {
            $table->string('identifier');
            $table->string('token')->unique();
            $table->timestampTz('expires');
            $table->unique(['identifier', 'token']);
        });
        $create('password_reset_tokens', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('user_id');
            $table->string('token_hash');
            $table->timestampTz('expires_at');
            $table->boolean('used')->default(false);
            $created($table);
            $table->index('user_id');
        });
        $create('email_verification_tokens', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('user_id');
            $table->string('token_hash');
            $table->timestampTz('expires_at');
            $table->boolean('used')->default(false);
            $created($table);
            $table->index('user_id');
        });

        // The Laravel scaffold may already have created these tables on a local volume.
        $addColumn('users', 'password_hash', static fn (Blueprint $table) => $table->string('password_hash')->nullable());
        $addColumn('users', 'email_verified', static fn (Blueprint $table) => $table->timestampTz('email_verified')->nullable());
        $addColumn('users', 'image', static fn (Blueprint $table) => $table->string('image')->nullable());
        $addColumn('users', 'role', static fn (Blueprint $table) => $table->string('role')->default('READER'));
        $addColumn('users', 'theme', static fn (Blueprint $table) => $table->string('theme')->default('light'));
        $addColumn('users', 'admin_theme', static fn (Blueprint $table) => $table->string('admin_theme')->default('light'));
        $addColumn('users', 'language', static fn (Blueprint $table) => $table->string('language')->default('ne'));
        $addColumn('users', 'is_active', static fn (Blueprint $table) => $table->boolean('is_active')->default(true));
        $addColumn('users', 'failed_login_count', static fn (Blueprint $table) => $table->integer('failed_login_count')->default(0));
        $addColumn('users', 'locked_until', static fn (Blueprint $table) => $table->timestampTz('locked_until')->nullable());
        $addColumn('users', 'last_failed_login_at', static fn (Blueprint $table) => $table->timestampTz('last_failed_login_at')->nullable());
        $addColumn('users', 'session_version', static fn (Blueprint $table) => $table->integer('session_version')->default(0));
        $addColumn('users', 'totp_secret', static fn (Blueprint $table) => $table->text('totp_secret')->nullable());
        $addColumn('users', 'totp_enabled', static fn (Blueprint $table) => $table->boolean('totp_enabled')->default(false));
        $addColumn('users', 'email_verified', static fn (Blueprint $table) => $table->timestampTz('email_verified')->nullable());

        $addColumn('sessions', 'session_token', static fn (Blueprint $table) => $table->string('session_token')->nullable());
        $addColumn('sessions', 'expires', static fn (Blueprint $table) => $table->timestampTz('expires')->nullable());
        $addColumn('password_reset_tokens', 'id', static fn (Blueprint $table) => $table->string('id')->nullable());
        $addColumn('password_reset_tokens', 'user_id', static fn (Blueprint $table) => $table->string('user_id')->nullable());
        $addColumn('password_reset_tokens', 'token_hash', static fn (Blueprint $table) => $table->string('token_hash')->nullable());
        $addColumn('password_reset_tokens', 'expires_at', static fn (Blueprint $table) => $table->timestampTz('expires_at')->nullable());
        $addColumn('password_reset_tokens', 'used', static fn (Blueprint $table) => $table->boolean('used')->default(false));

        $create('categories', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#c62828');
            $table->integer('sort_order')->default(0);
            $table->string('parent_id')->nullable();
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
            $table->index('parent_id');
        });
        $create('tags', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $created($table);
        });
        $create('articles', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->text('content');
            $table->text('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('status')->default('DRAFT');
            $table->boolean('is_featured')->default(false);
            $table->integer('reading_time')->nullable();
            $table->integer('word_count')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->timestampTz('published_at')->nullable();
            $table->string('category_id');
            $table->string('author_id');
            $created($table);
            $updated($table);
            $table->index(['category_id', 'status', 'published_at']);
            $table->index('author_id');
            $table->index('is_featured');
        });
        $create('article_tags', static function (Blueprint $table): void {
            $table->string('article_id');
            $table->string('tag_id');
            $table->primary(['article_id', 'tag_id']);
        });
        $create('comments', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->text('content');
            $table->string('status')->default('PENDING');
            $table->string('article_id');
            $table->string('user_id');
            $table->string('parent_id')->nullable();
            $table->integer('like_count')->default(0);
            $table->integer('dislike_count')->default(0);
            $created($table);
            $updated($table);
            $table->index(['article_id', 'status']);
            $table->index('user_id');
        });
        $create('comment_votes', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('comment_id');
            $table->string('user_id');
            $table->boolean('is_like');
            $created($table);
            $table->unique(['comment_id', 'user_id']);
        });
        $create('bookmarks', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('user_id');
            $table->string('article_id');
            $created($table);
            $table->unique(['user_id', 'article_id']);
        });
        $create('media_files', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('url');
            $table->text('alt_text')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('variants')->nullable();
            $table->string('uploaded_by');
            $created($table);
            $table->index(['uploaded_by', 'created_at']);
        });

        $create('tournaments', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->string('sport_type');
            $table->boolean('is_active')->default(true);
            $table->timestampTz('start_date')->nullable();
            $table->timestampTz('end_date')->nullable();
            $created($table);
        });
        $create('teams', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('logo')->nullable();
            $created($table);
        });
        $create('matches', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('tournament_id');
            $table->string('home_team_id');
            $table->string('away_team_id');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->string('status')->default('UPCOMING');
            $table->timestampTz('match_date');
            $table->string('venue')->nullable();
            $created($table);
            $updated($table);
            $table->index(['tournament_id', 'match_date']);
        });
        $create('reels', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->string('video_url');
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
        });
        $create('galleries', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
        });
        $create('gallery_images', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('gallery_id');
            $table->string('url');
            $table->text('caption')->nullable();
            $table->text('caption_en')->nullable();
            $table->integer('sort_order')->default(0);
            $created($table);
            $table->index(['gallery_id', 'sort_order']);
        });
        $create('ad_positions', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('name')->unique();
            $table->string('type');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('is_active')->default(true);
            $created($table);
        });
        $create('advertisements', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->string('target_url');
            $table->string('position_id');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestampTz('start_date')->nullable();
            $table->timestampTz('end_date')->nullable();
            $created($table);
            $updated($table);
            $table->index(['position_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
        $create('breaking_news', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('article_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('expires_at')->nullable();
            $created($table);
            $table->index('is_active');
        });
        $create('web_stories', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('slides');
            $table->string('category_id')->nullable();
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
        });
        $create('page_views', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('page_url');
            $table->string('article_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $created($table);
            $table->index(['article_id', 'user_id', 'created_at']);
            $table->index('page_url');
        });

        $create('site_settings', static function (Blueprint $table) use ($id, $updated): void {
            $id($table);
            $table->string('key')->unique();
            $table->json('value');
            $updated($table);
        });
        $create('newsletter_subscriptions', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('email')->unique();
            $table->string('language')->default('ne');
            $table->string('source')->nullable();
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
            $table->index(['is_active', 'created_at']);
        });
        $create('quick_links', static function (Blueprint $table) use ($id, $created, $updated): void {
            $id($table);
            $table->string('slug')->unique();
            $table->string('href');
            $table->string('title_ne');
            $table->string('title_en');
            $table->string('description_ne');
            $table->string('description_en');
            $table->string('icon_key');
            $table->string('accent_color')->default('#c62828');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $created($table);
            $updated($table);
            $table->index(['is_active', 'sort_order']);
        });
        $create('audit_logs', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('admin_id');
            $table->string('action');
            $table->string('entity');
            $table->string('entity_id')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $created($table);
            $table->index(['admin_id', 'entity', 'created_at']);
        });

        $create('holidays', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->integer('bs_year');
            $table->integer('bs_month');
            $table->integer('bs_day');
            $table->date('ad_date');
            $table->string('type')->default('public');
            $table->boolean('is_public')->default(true);
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $created($table);
            $table->unique(['bs_year', 'bs_month', 'bs_day', 'title']);
            $table->index(['bs_year', 'bs_month']);
            $table->index(['ad_date', 'type']);
        });
        $create('panchang_data', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->integer('bs_year');
            $table->integer('bs_month');
            $table->integer('bs_day');
            $table->date('ad_date');
            $table->string('tithi')->nullable();
            $table->string('nakshatra')->nullable();
            $table->string('yoga')->nullable();
            $table->string('karana')->nullable();
            $table->string('sunrise')->nullable();
            $table->string('sunset')->nullable();
            $table->string('moonrise')->nullable();
            $table->string('rahukaal')->nullable();
            $created($table);
            $table->unique(['bs_year', 'bs_month', 'bs_day']);
            $table->index('ad_date');
        });
        $create('gold_silver_prices', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->date('date')->unique();
            $table->float('fine_gold')->nullable();
            $table->float('tejabi_gold')->nullable();
            $table->float('silver')->nullable();
            $table->string('source')->nullable();
            $created($table);
            $table->index('date');
        });
        $create('forex_rates', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->date('date');
            $table->string('currency');
            $table->string('currency_name')->nullable();
            $table->float('buy')->nullable();
            $table->float('sell')->nullable();
            $table->integer('unit')->default(1);
            $table->string('source')->nullable();
            $created($table);
            $table->unique(['date', 'currency']);
            $table->index(['date', 'currency']);
        });
        $create('rashifal', static function (Blueprint $table) use ($id, $created): void {
            $id($table);
            $table->integer('bs_year');
            $table->integer('bs_month');
            $table->integer('bs_day');
            $table->date('ad_date');
            $table->string('sign');
            $table->string('sign_ne')->nullable();
            $table->text('prediction');
            $table->text('prediction_en')->nullable();
            $table->integer('rating')->nullable()->default(3);
            $created($table);
            $table->unique(['bs_year', 'bs_month', 'bs_day', 'sign']);
            $table->index(['ad_date', 'sign']);
        });
    }

    public function down(): void
    {
        // This migration is intentionally non-destructive for legacy data.
    }
};
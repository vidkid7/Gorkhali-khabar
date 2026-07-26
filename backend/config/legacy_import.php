<?php

return [
    'format' => 'gorkhali-legacy-export-v1',

    /*
    |--------------------------------------------------------------------------
    | Import order
    |--------------------------------------------------------------------------
    |
    | Rows are imported in dependency order. Each entry lists the stable key
    | used for idempotent updates and any JSON columns that must be encoded for
    | MySQL. Tables not listed here are rejected.
    |
    */
    'tables' => [
        'users' => ['unique' => ['id']],
        'accounts' => [
            'unique' => ['id'],
            'redact' => ['refresh_token', 'access_token', 'id_token'],
        ],
        'categories' => ['unique' => ['id']],
        'tags' => ['unique' => ['id']],
        'articles' => ['unique' => ['id']],
        'article_tags' => ['unique' => ['article_id', 'tag_id']],
        'comments' => ['unique' => ['id']],
        'comment_votes' => ['unique' => ['id']],
        'bookmarks' => ['unique' => ['id']],
        'media_files' => ['unique' => ['id'], 'json' => ['variants']],
        'tournaments' => ['unique' => ['id']],
        'teams' => ['unique' => ['id']],
        'matches' => ['unique' => ['id']],
        'reels' => ['unique' => ['id']],
        'galleries' => ['unique' => ['id']],
        'gallery_images' => ['unique' => ['id']],
        'ad_positions' => ['unique' => ['id']],
        'advertisements' => ['unique' => ['id']],
        'breaking_news' => ['unique' => ['id']],
        'web_stories' => ['unique' => ['id'], 'json' => ['slides']],
        'page_views' => ['unique' => ['id']],
        'site_settings' => ['unique' => ['id'], 'json' => ['value']],
        'newsletter_subscriptions' => ['unique' => ['id']],
        'quick_links' => ['unique' => ['id']],
        'audit_logs' => [
            'unique' => ['id'],
            'json' => ['old_value', 'new_value'],
        ],
        'holidays' => ['unique' => ['id']],
        'panchang_data' => ['unique' => ['id']],
        'gold_silver_prices' => ['unique' => ['id']],
        'forex_rates' => ['unique' => ['id']],
        'rashifal' => ['unique' => ['id']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ephemeral authentication data
    |--------------------------------------------------------------------------
    |
    | These records must be recreated by Laravel. Carrying them across a
    | platform/database migration would preserve active credentials.
    |
    */
    'skip_tables' => [
        'sessions',
        'verification_tokens',
        'password_reset_tokens',
        'email_verification_tokens',
    ],
];

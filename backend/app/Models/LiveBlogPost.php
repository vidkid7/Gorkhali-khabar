<?php

namespace App\Models;

class LiveBlogPost extends LegacyModel
{
    protected $table = 'live_blog_posts';

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}

<?php

namespace App\Models;

class LiveBlog extends LegacyModel
{
    protected $table = 'live_blogs';

    public function posts()
    {
        return $this->hasMany(LiveBlogPost::class)->orderByDesc('published_at');
    }

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }
}

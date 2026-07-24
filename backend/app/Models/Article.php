<?php

namespace App\Models;

class Article extends LegacyModel
{
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function articleTags() { return $this->hasMany(ArticleTag::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function bookmarks() { return $this->hasMany(Bookmark::class); }
}
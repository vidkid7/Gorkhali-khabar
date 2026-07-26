<?php

namespace App\Models;

class ContentPage extends LegacyModel
{
    protected $table = 'content_pages';

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }
}

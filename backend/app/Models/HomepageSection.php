<?php

namespace App\Models;

class HomepageSection extends LegacyModel
{
    protected $table = 'homepage_sections';

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }
}

<?php

namespace App\Models;

class EditorialMenu extends LegacyModel
{
    protected $table = 'menus';

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }
}

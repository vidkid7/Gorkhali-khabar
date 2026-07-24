<?php

namespace App\Models;

class Rashifal extends LegacyModel
{
    public const UPDATED_AT = null;

    protected $table = 'rashifal';

    protected function casts(): array
    {
        return ['ad_date' => 'date'];
    }
}

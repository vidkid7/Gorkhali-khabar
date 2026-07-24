<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class SiteSetting extends LegacyModel
{
    protected $table = 'site_settings';

    public const UPDATED_AT = 'updated_at';

    public const CREATED_AT = null;

    protected function value(): Attribute
    {
        return Attribute::make(
            get: static fn (string $value): mixed => json_decode($value, true),
            set: static fn (mixed $value): string => json_encode($value, JSON_THROW_ON_ERROR),
        );
    }

    protected function casts(): array
    {
        return ['updated_at' => 'datetime'];
    }
}

<?php

namespace App\Services;

use Mews\Purifier\Facades\Purifier;

class ContentSanitizer
{
    public function html(?string $value): ?string
    {
        return $value === null ? null : Purifier::clean($value);
    }

    public function plainText(?string $value): ?string
    {
        return $value === null ? null : trim(strip_tags($value));
    }
}
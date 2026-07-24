<?php

namespace App\Models;

class MediaFile extends LegacyModel
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['variants' => 'array'];
    }

    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}
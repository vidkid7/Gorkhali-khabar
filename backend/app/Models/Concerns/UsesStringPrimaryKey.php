<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UsesStringPrimaryKey
{
    public function initializeUsesStringPrimaryKey(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    protected static function bootUsesStringPrimaryKey(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getKeyName() && ! $model->getKey()) {
                $model->setAttribute($model->getKeyName(), (string) Str::ulid());
            }
        });
    }
}
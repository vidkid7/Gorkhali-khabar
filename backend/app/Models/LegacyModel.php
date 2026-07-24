<?php

namespace App\Models;

use App\Models\Concerns\UsesStringPrimaryKey;
use Illuminate\Database\Eloquent\Model;

abstract class LegacyModel extends Model
{
    use UsesStringPrimaryKey;

    protected $guarded = [];
}
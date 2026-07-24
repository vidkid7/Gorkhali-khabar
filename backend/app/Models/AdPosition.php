<?php
namespace App\Models;
class AdPosition extends LegacyModel { public const UPDATED_AT = null; public function advertisements() { return $this->hasMany(Advertisement::class, 'position_id'); } }
<?php
namespace App\Models;
class Advertisement extends LegacyModel { protected function casts(): array { return ['start_date' => 'datetime', 'end_date' => 'datetime', 'is_active' => 'boolean']; } public function position() { return $this->belongsTo(AdPosition::class, 'position_id'); } }
<?php
namespace App\Models;
class Tournament extends LegacyModel { public const UPDATED_AT = null; protected function casts(): array { return ['start_date' => 'datetime', 'end_date' => 'datetime']; } public function matches() { return $this->hasMany(MatchRecord::class, 'tournament_id'); } }
<?php
namespace App\Models;
class Team extends LegacyModel { public const UPDATED_AT = null; public function homeMatches() { return $this->hasMany(MatchRecord::class, 'home_team_id'); } public function awayMatches() { return $this->hasMany(MatchRecord::class, 'away_team_id'); } }
<?php
namespace App\Models;
class GoldSilverPrice extends LegacyModel { public const UPDATED_AT = null; protected function casts(): array { return ['date' => 'date']; } }
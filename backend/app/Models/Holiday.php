<?php
namespace App\Models;
class Holiday extends LegacyModel { public const UPDATED_AT = null; protected function casts(): array { return ['ad_date' => 'date', 'is_public' => 'boolean']; } }
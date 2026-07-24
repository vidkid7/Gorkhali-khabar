<?php
namespace App\Models;
class PanchangData extends LegacyModel { public const UPDATED_AT = null; protected function casts(): array { return ['ad_date' => 'date']; } }
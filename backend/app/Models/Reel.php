<?php
namespace App\Models;
class Reel extends LegacyModel { protected function casts(): array { return ['is_active' => 'boolean']; } }
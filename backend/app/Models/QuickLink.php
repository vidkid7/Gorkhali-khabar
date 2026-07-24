<?php
namespace App\Models;
class QuickLink extends LegacyModel { protected function casts(): array { return ['is_active' => 'boolean']; } }
<?php
namespace App\Models;
class BreakingNews extends LegacyModel { public const UPDATED_AT = null; protected function casts(): array { return ['expires_at' => 'datetime', 'is_active' => 'boolean']; } public function article() { return $this->belongsTo(Article::class); } }
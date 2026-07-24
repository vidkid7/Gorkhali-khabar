<?php
namespace App\Models;
class AuditLog extends LegacyModel { public const UPDATED_AT = null; public function admin() { return $this->belongsTo(User::class, 'admin_id'); } protected function casts(): array { return ['old_value' => 'array', 'new_value' => 'array']; } }
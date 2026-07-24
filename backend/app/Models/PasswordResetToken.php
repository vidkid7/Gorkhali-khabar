<?php
namespace App\Models;
class PasswordResetToken extends LegacyModel { public const UPDATED_AT = null; public function user() { return $this->belongsTo(User::class); } protected function casts(): array { return ['expires_at' => 'datetime', 'used' => 'boolean']; } }
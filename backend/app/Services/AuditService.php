<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    private const SENSITIVE_KEYS = [
        'password', 'password_hash', 'token', 'token_hash', 'access_token',
        'refresh_token', 'authorization', 'cookie', 'totp_secret',
    ];

    public function record(
        User $actor,
        string $action,
        string $entity,
        ?string $entityId = null,
        mixed $oldValue = null,
        mixed $newValue = null,
    ): void {
        AuditLog::query()->create([
            'admin_id' => $actor->getKey(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'old_value' => $this->sanitize($oldValue),
            'new_value' => $this->sanitize($newValue),
            'ip_address' => request()->ip(),
        ]);
    }

    private function sanitize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $sanitized = [];
        foreach ($value as $key => $item) {
            $normalizedKey = strtolower((string) $key);
            $sanitized[$key] = in_array($normalizedKey, self::SENSITIVE_KEYS, true)
                ? '[REDACTED]'
                : $this->sanitize($item);
        }

        return $sanitized;
    }
}
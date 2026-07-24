<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Services\AuditService;
use App\Services\ContentSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InfrastructureServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_service_redacts_sensitive_values_recursively(): void
    {
        $user = User::query()->create([
            'id' => 'audit-admin',
            'email' => 'audit-admin@example.com',
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        app(AuditService::class)->record(
            $user,
            'CREATE',
            'Article',
            'article-1',
            null,
            ['password_hash' => 'secret', 'nested' => ['token' => 'secret-token', 'title' => 'Visible']],
        );

        $audit = DB::table('audit_logs')->first();
        $newValue = json_decode($audit->new_value, true);

        $this->assertSame('[REDACTED]', $newValue['password_hash']);
        $this->assertSame('[REDACTED]', $newValue['nested']['token']);
        $this->assertSame('Visible', $newValue['nested']['title']);
    }

    public function test_content_sanitizer_removes_scripts_and_event_handlers(): void
    {
        $clean = app(ContentSanitizer::class)->html('<p>Hello</p><script>alert(1)</script><img src="x" onerror="alert(2)">');

        $this->assertStringContainsString('<p>Hello</p>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
    }
}
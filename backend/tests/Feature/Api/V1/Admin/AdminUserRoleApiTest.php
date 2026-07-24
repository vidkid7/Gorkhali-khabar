<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserRoleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_another_users_role_and_invalidate_their_sessions(): void
    {
        $admin = $this->user('role-admin', 'ADMIN');
        $target = $this->user('role-target', 'EDITOR');

        $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$target->id.'/role', ['role' => 'AUTHOR'])
            ->assertOk()
            ->assertJsonPath('data.id', $target->id)
            ->assertJsonPath('data.role', 'AUTHOR');

        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'AUTHOR', 'session_version' => 1]);
        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $admin->id,
            'action' => 'UPDATE',
            'entity' => 'User',
            'entity_id' => $target->id,
        ]);
    }

    public function test_role_changes_require_admin_json_and_a_valid_target(): void
    {
        $admin = $this->user('role-validation-admin', 'ADMIN');
        $editor = $this->user('role-validation-editor', 'EDITOR');
        $target = $this->user('role-validation-target', 'READER');

        $this->actingAs($editor)->patchJson('/api/v1/admin/users/'.$target->id.'/role', ['role' => 'AUTHOR'])->assertStatus(403);
        $this->actingAs($admin)->call('PATCH', '/api/v1/admin/users/'.$target->id.'/role', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
        $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$target->id.'/role', ['role' => 'OWNER'])->assertStatus(400);
        $this->actingAs($admin)->patchJson('/api/v1/admin/users/missing-user/role', ['role' => 'AUTHOR'])->assertStatus(404);
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = $this->user('self-role-admin', 'ADMIN');

        $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$admin->id.'/role', ['role' => 'EDITOR'])
            ->assertStatus(400)
            ->assertJsonPath('error', 'Cannot change your own role');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'ADMIN']);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->create([
            'id' => $id,
            'name' => $id,
            'email' => $id.'@example.com',
            'role' => $role,
            'is_active' => true,
            'session_version' => 0,
        ]);
    }
}

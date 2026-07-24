<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrimitivesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_manage_tags_with_generated_slugs(): void
    {
        $editor = $this->user('primitive-editor', 'EDITOR');
        $reader = $this->user('primitive-reader', 'READER');

        $this->actingAs($reader)->postJson('/api/v1/admin/tags', ['name' => 'News'])->assertStatus(403);
        $created = $this->actingAs($editor)->postJson('/api/v1/admin/tags', ['name' => '  World News  ', 'name_en' => 'World News'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'world-news');
        $id = $created->json('data.id');
        $this->actingAs($editor)->postJson('/api/v1/admin/tags', ['name' => 'World News'])->assertStatus(409);
        $this->actingAs($editor)->putJson('/api/v1/admin/tags', ['id' => $id, 'name' => 'Local News'])->assertOk()->assertJsonPath('data.slug', 'local-news');
        $this->actingAs($editor)->deleteJson('/api/v1/admin/tags?id='.$id)->assertOk();
        $this->assertDatabaseMissing('tags', ['id' => $id]);
    }

    public function test_only_admin_can_create_update_and_delete_quick_links(): void
    {
        $editor = $this->user('quick-link-editor', 'EDITOR');
        $admin = $this->user('quick-link-admin', 'ADMIN');
        $payload = [
            'slug' => 'weather',
            'href' => '/weather',
            'title_ne' => 'मौसम',
            'title_en' => 'Weather',
            'description_ne' => 'आजको मौसम',
            'description_en' => 'Today weather',
            'icon_key' => 'cloud',
            'accent_color' => '#123456',
            'sort_order' => 1,
            'is_active' => true,
        ];

        $this->actingAs($editor)->getJson('/api/v1/admin/quick-links')->assertOk();
        $this->actingAs($editor)->postJson('/api/v1/admin/quick-links', $payload)->assertStatus(403);
        $created = $this->actingAs($admin)->postJson('/api/v1/admin/quick-links', $payload)->assertOk()->assertJsonPath('data.slug', 'weather');
        $id = $created->json('data.id');
        $this->actingAs($editor)->patchJson('/api/v1/admin/quick-links/'.$id, ['is_active' => false])->assertStatus(403);
        $this->actingAs($admin)->patchJson('/api/v1/admin/quick-links/'.$id, ['is_active' => false])->assertOk()->assertJsonPath('data.is_active', false);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/quick-links/'.$id)->assertOk();
        $this->assertDatabaseMissing('quick_links', ['id' => $id]);
    }

    public function test_admin_primitive_writes_require_json_and_valid_data(): void
    {
        $admin = $this->user('primitive-validation-admin', 'ADMIN');

        $this->actingAs($admin)->call('POST', '/api/v1/admin/tags', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
        $this->actingAs($admin)->postJson('/api/v1/admin/tags', ['name' => '!!!'])->assertStatus(400);
        $this->actingAs($admin)->postJson('/api/v1/admin/quick-links', ['slug' => 'bad value'])->assertStatus(400);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->create(['id' => $id, 'name' => $id, 'email' => $id.'@example.com', 'role' => $role, 'is_active' => true]);
    }
}

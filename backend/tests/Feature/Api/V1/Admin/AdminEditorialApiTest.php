<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEditorialApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_read_and_upsert_settings(): void
    {
        $editor = $this->user('settings-editor', 'EDITOR');
        $admin = $this->user('settings-admin', 'ADMIN');
        SiteSetting::query()->create(['id' => 'setting-existing', 'key' => 'features_comments', 'value' => true]);

        $this->actingAs($editor)->getJson('/api/v1/admin/settings')->assertStatus(403);
        $this->actingAs($admin)->putJson('/api/v1/admin/settings', [
            'features_comments' => false,
            'social_links' => ['facebook' => 'https://example.com/page'],
        ])->assertOk()
            ->assertJsonPath('data.features_comments', false)
            ->assertJsonPath('data.social_links.facebook', 'https://example.com/page');
        $this->actingAs($admin)->getJson('/api/v1/admin/settings')->assertOk()->assertJsonPath('data.features_comments', false);
        $this->assertDatabaseHas('audit_logs', ['admin_id' => $admin->id, 'action' => 'SETTINGS_CHANGE', 'entity' => 'SiteSettings']);
    }

    public function test_editor_can_manage_breaking_news_but_only_admin_can_delete(): void
    {
        $editor = $this->user('breaking-editor', 'EDITOR');
        $admin = $this->user('breaking-admin', 'ADMIN');
        $published = $this->article('published-breaking-article', 'PUBLISHED');
        $draft = $this->article('draft-breaking-article', 'DRAFT');

        $this->actingAs($editor)->postJson('/api/v1/admin/breaking-news', ['title' => 'Draft', 'article_id' => $draft->id])->assertStatus(400);
        $created = $this->actingAs($editor)->postJson('/api/v1/admin/breaking-news', [
            'title' => 'Breaking',
            'article_id' => $published->id,
            'expires_at' => '2026-07-24T00:00:00Z',
        ])->assertCreated()->assertJsonPath('data.is_active', true);
        $id = $created->json('data.id');

        $this->actingAs($editor)->patchJson('/api/v1/admin/breaking-news/'.$id, ['title' => 'Updated', 'is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated')
            ->assertJsonPath('data.is_active', false);
        $this->actingAs($editor)->deleteJson('/api/v1/admin/breaking-news/'.$id)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/admin/breaking-news/'.$id)->assertOk()->assertJsonPath('data.id', $id);
    }

    public function test_settings_and_breaking_news_validate_request_shapes(): void
    {
        $admin = $this->user('editorial-validation-admin', 'ADMIN');

        $this->actingAs($admin)->call('PUT', '/api/v1/admin/settings', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
        $this->actingAs($admin)->putJson('/api/v1/admin/settings', [])->assertStatus(400);
        $this->actingAs($admin)->postJson('/api/v1/admin/breaking-news', ['title' => ''])->assertStatus(400);
    }

    private function article(string $id, string $status): Article
    {
        $author = $this->user($id.'-author', 'AUTHOR');
        $category = Category::query()->create(['id' => $id.'-category', 'name' => 'News', 'slug' => $id.'-category']);

        return Article::query()->create([
            'id' => $id,
            'title' => $id,
            'slug' => $id,
            'content' => $id,
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? now() : null,
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->firstOrCreate(['id' => $id], ['name' => $id, 'email' => $id.'@example.com', 'role' => $role, 'is_active' => true]);
    }
}

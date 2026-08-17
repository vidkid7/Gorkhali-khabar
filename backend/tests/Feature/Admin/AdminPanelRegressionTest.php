<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_member_can_log_in_without_a_remember_token_column(): void
    {
        $admin = $this->user('login-admin', 'ADMIN', [
            'email' => 'admin@example.com',
            'password_hash' => password_hash('AdminPass123!', PASSWORD_BCRYPT),
        ]);

        $this->post('/gorkhali-admin/login', [
            'email' => $admin->email,
            'password' => 'AdminPass123!',
        ])->assertRedirect('/gorkhali-admin');

        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_author_cannot_manage_another_authors_article_and_only_admin_can_delete(): void
    {
        $owner = $this->user('article-owner', 'AUTHOR');
        $otherAuthor = $this->user('other-author', 'AUTHOR');
        $editor = $this->user('article-editor', 'EDITOR');
        $admin = $this->user('article-admin', 'ADMIN');
        $article = $this->article($owner);

        $this->actingAs($otherAuthor)->get("/gorkhali-admin/articles/{$article->id}/edit")->assertForbidden();
        $this->actingAs($otherAuthor)->post("/gorkhali-admin/articles/{$article->id}/publish")->assertForbidden();
        $this->actingAs($editor)->delete("/gorkhali-admin/articles/{$article->id}")->assertForbidden();
        $this->assertDatabaseHas('articles', ['id' => $article->id]);

        $this->actingAs($admin)->delete("/gorkhali-admin/articles/{$article->id}")->assertRedirect('/gorkhali-admin/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_editing_an_article_does_not_replace_its_original_author(): void
    {
        $owner = $this->user('preserved-owner', 'AUTHOR');
        $editor = $this->user('preserving-editor', 'EDITOR');
        $article = $this->article($owner);

        $this->actingAs($editor)->put("/gorkhali-admin/articles/{$article->id}", [
            'title' => 'Updated title',
            'slug' => $article->slug,
            'content' => 'Updated content',
            'category_id' => $article->category_id,
            'status' => 'DRAFT',
        ])->assertRedirect("/gorkhali-admin/articles/{$article->id}/edit");

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'author_id' => $owner->id,
            'title' => 'Updated title',
        ]);
    }

    public function test_admin_can_upload_a_featured_image_without_replacing_it_on_a_later_text_only_edit(): void
    {
        Storage::fake('public');
        config()->set('filesystems.default', 'public');
        $admin = $this->user('featured-image-admin', 'ADMIN');
        $article = $this->article($admin);

        $this->actingAs($admin)->put("/gorkhali-admin/articles/{$article->id}", [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'category_id' => $article->category_id,
            'status' => 'DRAFT',
            'featured_image' => UploadedFile::fake()->image('featured.png', 1200, 675),
        ])->assertRedirect("/gorkhali-admin/articles/{$article->id}/edit");

        $uploadedUrl = Article::query()->findOrFail($article->id)->featured_image;
        $this->assertNotNull($uploadedUrl);
        $this->assertStringContainsString('/storage/articles/', $uploadedUrl);

        $this->actingAs($admin)->put("/gorkhali-admin/articles/{$article->id}", [
            'title' => 'Text-only update',
            'slug' => $article->slug,
            'content' => 'Updated content',
            'category_id' => $article->category_id,
            'status' => 'DRAFT',
        ])->assertRedirect("/gorkhali-admin/articles/{$article->id}/edit");

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'featured_image' => $uploadedUrl,
        ]);
        Storage::disk('public')->assertExists(
            str_replace('/storage/', '', (string) parse_url($uploadedUrl, PHP_URL_PATH))
        );
    }

    public function test_admin_can_update_another_user_without_a_server_error(): void
    {
        $admin = $this->user('users-admin', 'ADMIN');
        $editor = $this->user('users-editor', 'EDITOR');

        $this->actingAs($admin)->put("/gorkhali-admin/users/{$editor->id}", [
            'name' => 'Updated Editor',
            'email' => $editor->email,
            'role' => 'AUTHOR',
            'is_active' => '1',
            'language' => 'ne',
        ])->assertRedirect('/gorkhali-admin/users');

        $this->assertDatabaseHas('users', [
            'id' => $editor->id,
            'name' => 'Updated Editor',
            'role' => 'AUTHOR',
        ]);
    }

    public function test_admin_can_open_every_editorial_management_area(): void
    {
        $admin = $this->user('editorial-admin', 'ADMIN');

        foreach (['pages', 'menus', 'homepage-sections', 'live-blogs'] as $path) {
            $this->actingAs($admin)->get("/gorkhali-admin/{$path}")->assertOk();
        }
    }

    public function test_admin_can_open_dashboard_and_analytics_on_the_configured_database(): void
    {
        $admin = $this->user('dashboard-admin', 'ADMIN');

        $this->actingAs($admin)->get('/gorkhali-admin')->assertOk();
        $this->actingAs($admin)->get('/gorkhali-admin/analytics')->assertOk();
    }

    public function test_blade_permissions_match_the_api_role_boundaries(): void
    {
        $author = $this->user('media-author', 'AUTHOR');
        $editor = $this->user('restricted-editor', 'EDITOR');
        $category = Category::query()->create([
            'id' => 'protected-category',
            'name' => 'Protected',
            'slug' => 'protected',
        ]);

        $this->actingAs($author)->get('/gorkhali-admin/media')->assertOk();
        $this->actingAs($editor)->delete("/gorkhali-admin/categories/{$category->id}")->assertForbidden();
        $this->actingAs($editor)->get('/gorkhali-admin/galleries/create')->assertForbidden();
        $this->actingAs($editor)->get('/gorkhali-admin/reels/create')->assertForbidden();
        $this->actingAs($editor)->get('/gorkhali-admin/quick-links/create')->assertForbidden();
        $this->actingAs($editor)->get('/gorkhali-admin/sports/create')->assertForbidden();
    }

    private function user(string $id, string $role, array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'id' => $id,
            'name' => $id,
            'email' => "{$id}@example.com",
            'role' => $role,
            'is_active' => true,
            'session_version' => 1,
        ], $attributes));
    }

    private function article(User $owner): Article
    {
        $category = Category::query()->firstOrCreate(
            ['id' => 'admin-test-category'],
            ['name' => 'Admin Test', 'slug' => 'admin-test']
        );

        return Article::query()->create([
            'id' => 'admin-test-article',
            'title' => 'Original title',
            'slug' => 'admin-test-article',
            'content' => 'Original content',
            'status' => 'DRAFT',
            'category_id' => $category->id,
            'author_id' => $owner->id,
        ]);
    }
}

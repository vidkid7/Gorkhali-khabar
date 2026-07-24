<?php

namespace Tests\Feature\Api\V1\Content;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_categories_include_active_children_and_article_counts(): void
    {
        $author = User::query()->create(['id' => 'category-author', 'email' => 'category-author@example.com']);
        $parent = Category::query()->create(['id' => 'parent-category', 'name' => 'Parent', 'slug' => 'parent', 'sort_order' => 2, 'is_active' => true]);
        Category::query()->create(['id' => 'child-category', 'name' => 'Child', 'slug' => 'child', 'parent_id' => $parent->id, 'sort_order' => 1, 'is_active' => true]);
        Category::query()->create(['id' => 'hidden-category', 'name' => 'Hidden', 'slug' => 'hidden', 'is_active' => false]);
        Article::query()->create([
            'id' => 'category-article',
            'title' => 'Category article',
            'slug' => 'category-article',
            'content' => 'Content',
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'category_id' => $parent->id,
            'author_id' => $author->id,
        ]);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.1.id', $parent->id)
            ->assertJsonPath('data.1._count.articles', 1)
            ->assertJsonPath('data.1.children.0.slug', 'child');
    }

    public function test_editor_can_create_but_duplicate_slug_conflicts(): void
    {
        $editor = User::query()->create(['id' => 'category-editor', 'email' => 'category-editor@example.com', 'role' => 'EDITOR', 'is_active' => true]);
        $payload = ['name' => 'Politics', 'slug' => 'politics', 'sort_order' => 3];

        $this->actingAs($editor)->postJson('/api/v1/categories', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.slug', 'politics');
        $this->actingAs($editor)->postJson('/api/v1/categories', $payload)->assertStatus(409);
    }

    public function test_category_with_children_cannot_be_deleted(): void
    {
        $admin = User::query()->create(['id' => 'category-admin', 'email' => 'category-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);
        $parent = Category::query()->create(['id' => 'delete-parent', 'name' => 'Delete Parent', 'slug' => 'delete-parent']);
        Category::query()->create(['id' => 'delete-child', 'name' => 'Delete Child', 'slug' => 'delete-child', 'parent_id' => $parent->id]);

        $this->actingAs($admin)->deleteJson('/api/v1/categories?id='.$parent->id)->assertStatus(409);
    }

    public function test_editor_can_update_a_category_without_reusing_another_slug(): void
    {
        $editor = User::query()->create(['id' => 'update-editor', 'email' => 'update-editor@example.com', 'role' => 'EDITOR', 'is_active' => true]);
        $category = Category::query()->create(['id' => 'update-category', 'name' => 'Before', 'slug' => 'before']);
        Category::query()->create(['id' => 'taken-category', 'name' => 'Taken', 'slug' => 'taken']);

        $this->actingAs($editor)->putJson('/api/v1/categories', [
            'id' => $category->id,
            'name' => 'After',
            'slug' => 'taken',
        ])->assertStatus(409);

        $this->actingAs($editor)->putJson('/api/v1/categories', [
            'id' => $category->id,
            'name' => 'After',
            'slug' => 'after',
            'is_active' => false,
        ])->assertOk()->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'After', 'slug' => 'after', 'is_active' => false]);
    }
}

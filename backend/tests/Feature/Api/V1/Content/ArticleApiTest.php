<?php

namespace Tests\Feature\Api\V1\Content;

use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_list_filters_published_content_and_preserves_relation_shapes(): void
    {
        $author = User::query()->create(['id' => 'article-author', 'name' => 'Author', 'email' => 'author@example.com']);
        $category = Category::query()->create(['id' => 'news-category', 'name' => 'News', 'slug' => 'news', 'is_active' => true]);
        $tag = Tag::query()->create(['id' => 'local-tag', 'name' => 'Local', 'slug' => 'local']);

        $published = Article::query()->create([
            'id' => 'published-article',
            'title' => 'Published headline',
            'slug' => 'published-headline',
            'content' => 'Published content',
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);
        ArticleTag::query()->create(['article_id' => $published->id, 'tag_id' => $tag->id]);
        Article::query()->create([
            'id' => 'draft-article',
            'title' => 'Draft headline',
            'slug' => 'draft-headline',
            'content' => 'Draft content',
            'status' => 'DRAFT',
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $this->getJson('/api/v1/articles?page=1&pageSize=1&search=HEADLINE')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.pageSize', 1)
            ->assertJsonPath('data.data.0.id', 'published-article')
            ->assertJsonPath('data.data.0.category.slug', 'news')
            ->assertJsonPath('data.data.0.author.name', 'Author')
            ->assertJsonPath('data.data.0.tags.0.tag.slug', 'local');
    }

    public function test_author_can_create_a_sanitized_article_but_reader_cannot(): void
    {
        $category = Category::query()->create(['id' => 'create-category', 'name' => 'Create', 'slug' => 'create', 'is_active' => true]);
        $tag = Tag::query()->create(['id' => 'create-tag', 'name' => 'Create Tag', 'slug' => 'create-tag']);
        $reader = User::query()->create(['id' => 'create-reader', 'email' => 'reader-create@example.com', 'role' => 'READER', 'is_active' => true]);
        $author = User::query()->create(['id' => 'create-author', 'email' => 'author-create@example.com', 'role' => 'AUTHOR', 'is_active' => true]);
        $payload = [
            'title' => 'Created headline',
            'slug' => 'created-headline',
            'content' => '<p>one two three</p><script>alert(1)</script>',
            'category_id' => $category->id,
            'status' => 'PUBLISHED',
            'tag_ids' => [$tag->id],
        ];

        $this->actingAs($reader)->postJson('/api/v1/articles', $payload)->assertStatus(403);

        $this->actingAs($author)->postJson('/api/v1/articles', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.author_id', $author->id)
            ->assertJsonPath('data.tags.0.tag.id', $tag->id);

        $article = Article::query()->where('slug', 'created-headline')->firstOrFail();
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertSame(3, $article->word_count);
        $this->assertSame(1, $article->reading_time);
        $this->assertNotNull($article->published_at);
    }

    public function test_slug_detail_and_view_recording_are_published_only(): void
    {
        $author = User::query()->create(['id' => 'detail-author', 'email' => 'detail-author@example.com']);
        $category = Category::query()->create(['id' => 'detail-category', 'name' => 'Detail', 'slug' => 'detail', 'is_active' => true]);
        $article = Article::query()->create([
            'id' => 'detail-article',
            'title' => 'Detail headline',
            'slug' => 'detail-headline',
            'content' => 'Detail content',
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $this->getJson('/api/v1/articles/slug/detail-headline')
            ->assertOk()
            ->assertJsonPath('data.id', $article->id);
        $this->getJson('/api/v1/articles/'.$article->id)
            ->assertOk()
            ->assertJsonPath('data.slug', $article->slug);
        $this->postJson('/api/v1/articles/'.$article->id.'/view')
            ->assertOk()
            ->assertJsonPath('data.view_count', 1);
        $this->assertDatabaseHas('page_views', ['article_id' => $article->id, 'page_url' => '/articles/detail-headline']);

        $article->update(['status' => 'DRAFT']);
        $this->getJson('/api/v1/articles/slug/detail-headline')->assertStatus(404);
        $this->getJson('/api/v1/articles/'.$article->id)->assertStatus(404);
        $this->postJson('/api/v1/articles/'.$article->id.'/view')->assertStatus(404);
    }

    public function test_author_can_update_only_owned_articles_and_unpublishing_clears_public_state(): void
    {
        $category = Category::query()->create(['id' => 'update-category', 'name' => 'Update', 'slug' => 'update']);
        $owner = User::query()->create(['id' => 'update-owner', 'email' => 'update-owner@example.com', 'role' => 'AUTHOR', 'is_active' => true]);
        $other = User::query()->create(['id' => 'update-other', 'email' => 'update-other@example.com', 'role' => 'AUTHOR', 'is_active' => true]);
        $oldTag = Tag::query()->create(['id' => 'old-tag', 'name' => 'Old', 'slug' => 'old-tag']);
        $newTag = Tag::query()->create(['id' => 'new-tag', 'name' => 'New', 'slug' => 'new-tag']);
        $article = Article::query()->create([
            'id' => 'update-article',
            'title' => 'Before',
            'slug' => 'before-update',
            'content' => 'Before content',
            'status' => 'PUBLISHED',
            'is_featured' => true,
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $owner->id,
        ]);
        ArticleTag::query()->create(['article_id' => $article->id, 'tag_id' => $oldTag->id]);
        BreakingNews::query()->create(['id' => 'update-breaking', 'title' => 'Breaking', 'article_id' => $article->id, 'is_active' => true]);
        $payload = [
            'title' => 'After',
            'content' => '<p>one two</p><script>alert(1)</script>',
            'status' => 'DRAFT',
            'tag_ids' => [$newTag->id],
        ];

        $this->actingAs($other)->putJson('/api/v1/articles/'.$article->id, $payload)->assertStatus(403);
        $this->actingAs($owner)->putJson('/api/v1/articles/'.$article->id, $payload)
            ->assertOk()
            ->assertJsonPath('data.title', 'After')
            ->assertJsonPath('data.tags.0.tag.id', $newTag->id);

        $article->refresh();
        $this->assertSame('DRAFT', $article->status);
        $this->assertFalse($article->is_featured);
        $this->assertNull($article->published_at);
        $this->assertSame(2, $article->word_count);
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertDatabaseMissing('article_tags', ['article_id' => $article->id, 'tag_id' => $oldTag->id]);
        $this->assertDatabaseHas('breaking_news', ['id' => 'update-breaking', 'is_active' => false]);
    }

    public function test_only_admin_can_delete_an_article(): void
    {
        $category = Category::query()->create(['id' => 'destroy-category', 'name' => 'Destroy', 'slug' => 'destroy']);
        $author = User::query()->create(['id' => 'destroy-author', 'email' => 'destroy-author@example.com', 'role' => 'AUTHOR', 'is_active' => true]);
        $admin = User::query()->create(['id' => 'destroy-admin', 'email' => 'destroy-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);
        $article = Article::query()->create([
            'id' => 'destroy-article',
            'title' => 'Destroy',
            'slug' => 'destroy-article',
            'content' => 'Destroy',
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $this->actingAs($author)->deleteJson('/api/v1/articles/'.$article->id)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/articles/'.$article->id)
            ->assertOk()
            ->assertJsonPath('data.id', $article->id);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}

<?php

namespace Tests\Feature\Api\V1\Content;

use App\Models\Article;
use App\Models\Category;
use App\Models\PageView;
use App\Models\QuickLink;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPrimitivesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_and_quick_links_are_sorted_and_filtered(): void
    {
        Tag::query()->create(['id' => 'z-tag', 'name' => 'Zulu', 'slug' => 'zulu']);
        Tag::query()->create(['id' => 'a-tag', 'name' => 'Alpha', 'slug' => 'alpha']);
        QuickLink::query()->create(['id' => 'later-link', 'slug' => 'later', 'href' => '/later', 'title_ne' => 'Later', 'title_en' => 'Later', 'description_ne' => 'Later link', 'description_en' => 'Later link', 'icon_key' => 'clock', 'sort_order' => 2, 'is_active' => true]);
        QuickLink::query()->create(['id' => 'first-link', 'slug' => 'first', 'href' => '/first', 'title_ne' => 'First', 'title_en' => 'First', 'description_ne' => 'First link', 'description_en' => 'First link', 'icon_key' => 'star', 'sort_order' => 1, 'is_active' => true]);
        QuickLink::query()->create(['id' => 'hidden-link', 'slug' => 'hidden', 'href' => '/hidden', 'title_ne' => 'Hidden', 'title_en' => 'Hidden', 'description_ne' => 'Hidden link', 'description_en' => 'Hidden link', 'icon_key' => 'eye', 'sort_order' => 0, 'is_active' => false]);

        $this->getJson('/api/v1/tags')->assertOk()->assertJsonPath('data.0.slug', 'alpha');
        $this->getJson('/api/v1/quick-links')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', 'first')
            ->assertJsonMissingPath('data.0.id');
    }

    public function test_search_and_trending_return_only_published_articles(): void
    {
        $author = User::query()->create(['id' => 'primitive-author', 'name' => 'Author', 'email' => 'primitive-author@example.com']);
        $category = Category::query()->create(['id' => 'primitive-category', 'name' => 'News', 'slug' => 'primitive-news']);
        $first = $this->article('primitive-first', 'Climate first', 'PUBLISHED', $author, $category);
        $second = $this->article('primitive-second', 'Climate second', 'PUBLISHED', $author, $category);
        $draft = $this->article('primitive-draft', 'Climate draft', 'DRAFT', $author, $category);
        PageView::query()->create(['page_url' => '/articles/'.$first->slug, 'article_id' => $first->id]);
        PageView::query()->create(['page_url' => '/articles/'.$second->slug, 'article_id' => $second->id]);
        PageView::query()->create(['page_url' => '/articles/'.$second->slug, 'article_id' => $second->id]);
        PageView::query()->create(['page_url' => '/articles/'.$draft->slug, 'article_id' => $draft->id]);

        $this->getJson('/api/v1/search?q=x')->assertStatus(400);
        $this->getJson('/api/v1/search?q=CLIMATE')
            ->assertOk()
            ->assertJsonPath('data.total', 2);
        $this->getJson('/api/v1/trending')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $second->id);
    }

    public function test_public_settings_preserve_approved_brand_defaults(): void
    {
        SiteSetting::query()->create(['id' => 'stored-name', 'key' => 'site_name', 'value' => ['en' => 'Wrong Name']]);
        SiteSetting::query()->create(['id' => 'feature-setting', 'key' => 'features_comments', 'value' => false]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('data.site_name.en', 'Gorkhali Khabar')
            ->assertJsonPath('data.features_comments', false)
            ->assertHeader('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=300');
    }

    private function article(string $id, string $title, string $status, User $author, Category $category): Article
    {
        return Article::query()->create([
            'id' => $id,
            'title' => $title,
            'slug' => $id,
            'content' => $title,
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? now() : null,
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);
    }
}

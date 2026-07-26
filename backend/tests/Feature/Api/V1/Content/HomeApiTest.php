<?php

namespace Tests\Feature\Api\V1\Content;

use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\MatchRecord;
use App\Models\Reel;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_aggregates_only_public_content_in_frontend_order(): void
    {
        $author = User::query()->create(['id' => 'home-author', 'name' => 'Home Author', 'email' => 'home-author@example.com']);
        $news = Category::query()->create(['id' => 'home-news', 'name' => 'News', 'slug' => 'samachar']);
        $opinionCategory = Category::query()->create(['id' => 'home-opinion', 'name' => 'Opinion', 'slug' => 'bichar']);
        $province = Category::query()->create(['id' => 'home-bagmati', 'name' => 'Bagmati', 'slug' => 'bagmati-pradesh']);

        $featured = [];
        foreach (range(1, 6) as $number) {
            $featured[] = $this->article(
                'home-featured-'.$number,
                $news,
                $author,
                ['is_featured' => true, 'published_at' => now()->subMinutes($number)]
            );
        }

        $popular = $this->article('home-popular', $news, $author, ['view_count' => 20, 'comment_count' => 8]);
        $opinionArticle = $this->article('home-opinion-article', $opinionCategory, $author, ['published_at' => now()->subMinutes(2)]);
        $older = $this->article('home-older', $news, $author, ['view_count' => 30, 'published_at' => now()->subHours(13)]);
        $provincial = $this->article('home-provincial', $province, $author);
        $draft = $this->article('home-draft', $news, $author, ['status' => 'DRAFT', 'is_featured' => true, 'view_count' => 999]);

        BreakingNews::query()->create(['id' => 'home-breaking', 'title' => 'Live', 'article_id' => $popular->id, 'is_active' => true]);
        BreakingNews::query()->create(['id' => 'home-breaking-draft', 'title' => 'Draft', 'article_id' => $draft->id, 'is_active' => true]);
        BreakingNews::query()->create(['id' => 'home-breaking-expired', 'title' => 'Expired', 'is_active' => true, 'expires_at' => now()->subMinute()]);

        Reel::query()->create(['id' => 'home-reel', 'title' => 'Active reel', 'slug' => 'active-reel', 'video_url' => '/active.mp4', 'is_active' => true]);
        Reel::query()->create(['id' => 'home-hidden-reel', 'title' => 'Hidden reel', 'slug' => 'hidden-reel', 'video_url' => '/hidden.mp4', 'is_active' => false]);

        $tournament = Tournament::query()->create(['id' => 'home-tournament', 'name' => 'League', 'slug' => 'league', 'sport_type' => 'football']);
        $homeTeam = Team::query()->create(['id' => 'home-team', 'name' => 'Home']);
        $awayTeam = Team::query()->create(['id' => 'away-team', 'name' => 'Away']);
        MatchRecord::query()->create([
            'id' => 'home-match',
            'tournament_id' => $tournament->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'LIVE',
            'match_date' => now(),
        ]);

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonCount(1, 'data.breakingNews')
            ->assertJsonPath('data.breakingNews.0.article.slug', $popular->slug)
            ->assertJsonCount(5, 'data.featured')
            ->assertJsonPath('data.featured.0.id', $featured[0]->id)
            ->assertJsonPath('data.categoryGroups.samachar.0.id', $popular->id)
            ->assertJsonPath('data.trending.0.id', $older->id)
            ->assertJsonPath('data.mostCommented.0.id', $popular->id)
            ->assertJsonCount(1, 'data.reels')
            ->assertJsonPath('data.matches.0.home_team.name', 'Home')
            ->assertJsonPath('data.olderArticles.0.id', $older->id)
            ->assertJsonPath('data.editorPicks.0.id', $featured[5]->id)
            ->assertJsonStructure([
                'data' => [
                    'latestUpdates',
                    'opinion',
                    'mediaHighlights' => ['reels', 'galleries'],
                ],
            ])
            ->assertJsonFragment(['id' => $featured[0]->id])
            ->assertJsonPath('data.opinion.0.id', $opinionArticle->id)
            ->assertJsonCount(1, 'data.mediaHighlights.reels')
            ->assertJsonCount(0, 'data.mediaHighlights.galleries')
            ->assertJsonPath('data.provinceGroups.bagmati.0.id', $provincial->id)
            ->assertJsonCount(7, 'data.provinceGroups')
            ->assertJsonMissing(['id' => $draft->id]);
    }

    /** @param array<string, mixed> $overrides */
    private function article(string $id, Category $category, User $author, array $overrides = []): Article
    {
        return Article::query()->create(array_merge([
            'id' => $id,
            'title' => $id,
            'slug' => $id,
            'content' => $id,
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $author->id,
        ], $overrides));
    }
}

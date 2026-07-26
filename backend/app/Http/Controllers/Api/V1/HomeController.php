<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\Gallery;
use App\Models\MatchRecord;
use App\Models\Reel;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    private const CATEGORY_LIMITS = [
        'samachar' => 5,
        'feature' => 5,
        'cover-story' => 4,
        'samaj' => 4,
        'manoranjan' => 4,
        'world' => 4,
        'swasthya' => 4,
        'shiksha' => 4,
        'bichar' => 4,
        'prabidhi' => 5,
        'antarvaarta' => 5,
        'khelkud' => 5,
        'antarrashtriya' => 4,
        'bichitra' => 4,
        'sahitya' => 4,
        'saptaahanta' => 4,
        'rajniti' => 5,
        'arthatantra' => 5,
        'swasthya' => 4,
        'video' => 5,
        'photo-gallery' => 4,
        'bichar' => 5,
        'shiksha' => 4,
    ];

    private const PROVINCES = [
        'bagmati-pradesh' => 'bagmati',
        'koshi-pradesh' => 'koshi',
        'madhesh-pradesh' => 'madhesh',
        'gandaki-pradesh' => 'gandaki',
        'lumbini-pradesh' => 'lumbini',
        'karnali-pradesh' => 'karnali',
        'sudurpaschim-pradesh' => 'sudurpaschim',
    ];

    public function index(): JsonResponse
    {
        $now = now();
        $categoryGroups = [];
        foreach (self::CATEGORY_LIMITS as $slug => $limit) {
            $categoryGroups[$slug] = $this->articles()
                ->whereHas('category', static fn (Builder $query): Builder => $query->where('slug', $slug))
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();
        }

        $featured = $this->articles()
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $recentPublic = $this->articles()->where('published_at', '>=', $now->copy()->subDays(7));
        $trending = (clone $recentPublic)->orderByDesc('view_count')->limit(5)->get();
        $mostCommented = (clone $recentPublic)->orderByDesc('comment_count')->limit(5)->get();
        $olderArticles = $this->articles()
            ->where('published_at', '<=', $now->copy()->subHours(12))
            ->orderByDesc('view_count')
            ->limit(6)
            ->get();
        $editorPicks = $this->articles()
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->skip(5)
            ->limit(3)
            ->get();
        $latestUpdates = $this->articles()
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();
        $opinion = $this->articles()
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('slug', 'bichar'))
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $provinceArticles = $this->articles()
            ->whereHas('category', static fn (Builder $query): Builder => $query->whereIn('slug', array_keys(self::PROVINCES)))
            ->orderByDesc('published_at')
            ->limit(35)
            ->get();
        $provinceGroups = array_fill_keys(array_values(self::PROVINCES), []);
        foreach ($provinceArticles as $article) {
            $key = self::PROVINCES[$article->category->slug] ?? null;
            if ($key !== null) {
                $provinceGroups[$key][] = $article;
            }
        }

        $breakingNews = BreakingNews::query()
            ->where('is_active', true)
            ->where(static function (Builder $query) use ($now): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            })
            ->where(static function (Builder $query): void {
                $query->whereNull('article_id')->orWhereHas('article', static fn (Builder $article): Builder => $article->where('status', 'PUBLISHED'));
            })
            ->with('article:id,slug')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $matches = MatchRecord::query()
            ->whereIn('status', ['LIVE', 'COMPLETED', 'UPCOMING'])
            ->with([
                'homeTeam:id,name,name_en',
                'awayTeam:id,name,name_en',
                'tournament:id,name,name_en',
            ])
            ->orderByDesc('match_date')
            ->limit(4)
            ->get();

        return ApiResponse::success([
            'breakingNews' => $breakingNews,
            'featured' => $featured,
            'categoryGroups' => $categoryGroups,
            'trending' => $trending,
            'mostCommented' => $mostCommented,
            'reels' => Reel::query()->where('is_active', true)->orderByDesc('created_at')->limit(10)->get(),
            'matches' => $matches,
            'olderArticles' => $olderArticles,
            'editorPicks' => $editorPicks,
            'latestUpdates' => $latestUpdates,
            'opinion' => $opinion,
            'mediaHighlights' => [
                'reels' => Reel::query()->where('is_active', true)->orderByDesc('created_at')->limit(6)->get(),
                'galleries' => Gallery::query()->where('is_active', true)->with('images')->orderByDesc('created_at')->limit(4)->get(),
            ],
            'provinceGroups' => $provinceGroups,
        ]);
    }

    private function articles(): Builder
    {
        return Article::query()
            ->where('status', 'PUBLISHED')
            ->with([
                'category:id,name,name_en,slug,color',
                'author:id,name',
            ])
            ->select([
                'id',
                'slug',
                'title',
                'title_en',
                'excerpt',
                'excerpt_en',
                'featured_image',
                'reading_time',
                'published_at',
                'view_count',
                'comment_count',
                'category_id',
                'author_id',
            ]);
    }
}

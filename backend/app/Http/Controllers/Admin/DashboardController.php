<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\PageView;
use App\Models\Reel;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonthStart = $thisMonth->copy()->subMonth();
        $lastMonthEnd = $thisMonth->copy()->subSecond();
        $last7days = $now->copy()->subDays(7);
        $last14days = $now->copy()->subDays(14);
        $last30days = $now->copy()->subDays(30);

        // ---------- Core counters ----------
        $stats = [
            'total_articles' => Article::query()->count(),
            'published_articles' => Article::query()->where('status', 'PUBLISHED')->count(),
            'draft_articles' => Article::query()->where('status', 'DRAFT')->count(),
            'pending_articles' => Article::query()->where('status', 'PENDING')->count(),
            'archived_articles' => Article::query()->where('status', 'ARCHIVED')->count(),
            'featured_articles' => Article::query()->where('is_featured', true)->count(),

            'total_views' => (int) Article::query()->sum('view_count'),
            'total_comments' => Comment::query()->count(),
            'pending_comments' => Comment::query()->where('status', 'PENDING')->count(),
            'approved_comments' => Comment::query()->where('status', 'APPROVED')->count(),

            'total_users' => User::query()->count(),
            'active_users' => User::query()->where('is_active', true)->count(),
            'staff_users' => User::query()->whereIn('role', ['ADMIN', 'EDITOR', 'AUTHOR'])->count(),
            'new_users_today' => User::query()->where('created_at', '>=', $today)->count(),
            'recent_users' => User::query()->where('created_at', '>=', $last7days)->count(),

            'total_tags' => Tag::query()->count(),
            'total_categories' => Category::query()->count(),
            'total_reels' => Reel::query()->count(),
            'total_bookmarks' => Bookmark::query()->count(),
            'total_page_views' => (int) PageView::query()->count(),

            // Period counters
            'published_today' => Article::query()
                ->where('status', 'PUBLISHED')
                ->where('published_at', '>=', $today)
                ->count(),
            'published_this_month' => Article::query()
                ->where('status', 'PUBLISHED')
                ->where('published_at', '>=', $thisMonth)
                ->count(),
            'published_last_month' => Article::query()
                ->where('status', 'PUBLISHED')
                ->whereBetween('published_at', [$lastMonthStart, $lastMonthEnd])
                ->count(),

            'page_views_today' => PageView::query()->where('created_at', '>=', $today)->count(),
            'page_views_yesterday' => PageView::query()
                ->whereBetween('created_at', [$yesterday, $today])
                ->count(),
            'page_views_week' => PageView::query()->where('created_at', '>=', $last7days)->count(),
            'page_views_month' => PageView::query()->where('created_at', '>=', $last30days)->count(),
        ];

        // ---------- Charts ----------
        // Daily views (last 14 days)
        $dailyViews = PageView::query()
            ->where('created_at', '>=', $last14days)
            ->selectRaw("date_trunc('day', created_at) as day, count(*) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($r) => ['day' => \Carbon\Carbon::parse($r->day), 'total' => (int) $r->total])
            ->all();
        $hourlyViews = PageView::query()
            ->where('created_at', '>=', $today)
            ->selectRaw("extract(hour from created_at) as hour, count(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy(fn ($r) => (int) $r->hour)
            ->map(fn ($r) => (int) $r->total)
            ->all();

        $hourlyBuckets = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyBuckets[] = $hourlyViews[$h] ?? 0;
        }

        // Last 7 days article publishes
        $publishesByDay = Article::query()
            ->where('created_at', '>=', $last7days)
            ->selectRaw("date_trunc('day', created_at) as day, count(*) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy(fn ($r) => \Carbon\Carbon::parse($r->day)->format('Y-m-d'))
            ->map(fn ($r) => (int) $r->total)
            ->all();

        $publishBuckets = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $publishBuckets[$date] = $publishesByDay[$date] ?? 0;
        }

        // ---------- Top performers ----------
        $topArticles = Article::query()
            ->where('status', 'PUBLISHED')
            ->with(['category:id,name,color', 'author:id,name'])
            ->orderByDesc('view_count')
            ->limit(8)
            ->get(['id', 'title', 'slug', 'view_count', 'category_id', 'author_id', 'created_at']);

        $topCategories = Category::query()
            ->withCount(['articles' => fn ($q) => $q->whereIn('status', ['PUBLISHED'])])
            ->withCount(['articles as drafts_count' => fn ($q) => $q->whereIn('status', ['DRAFT'])])
            ->orderByDesc('articles_count')
            ->limit(8)
            ->get();

        // Note: User.id is bigint, but Comment.user_id/Bookmark.user_id are
        // varchar in the legacy schema. We work around the type mismatch by
        // using direct DB queries instead of ORM relations.
        $topCommenters = collect(DB::table('comments')
            ->select('user_id', DB::raw('count(*) as total'))
            ->where('status', 'APPROVED')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get())
            ->map(fn ($r) => [
                'name' => User::where('id', $r->user_id)->value('name') ?? '—',
                'comments_count' => (int) $r->total,
            ]);

        $topBookmarked = collect(DB::table('bookmarks as b')
            ->select('a.id', 'a.title', DB::raw('count(*) as bookmarks_count'))
            ->join('articles as a', 'a.id', '=', 'b.article_id')
            ->where('a.status', 'PUBLISHED')
            ->groupBy('a.id', 'a.title')
            ->orderByDesc('bookmarks_count')
            ->limit(5)
            ->get())
            ->map(fn ($r) => (object) [
                'id' => $r->id,
                'title' => $r->title,
                'bookmarks_count' => (int) $r->bookmarks_count,
            ]);

        // ---------- Pending action queue ----------
        $pendingQueue = [
            'pending_comments' => $stats['pending_comments'],
            'draft_articles' => $stats['draft_articles'],
            'pending_articles' => $stats['pending_articles'],
            'inactive_users' => User::query()->where('is_active', false)->count(),
            'unverified_emails' => User::query()->whereNull('email_verified')->count(),
        ];

        // ---------- Recent activity (audit feed) ----------
        $recentActivity = AuditLog::query()
            ->with('admin:id,name')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        // ---------- Recent articles for list ----------
        $recentArticles = Article::query()
            ->with(['category:id,name,color', 'author:id,name'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // ---------- System health ----------
        $health = $this->systemHealth();

        // ---------- Derived deltas ----------
        $monthDelta = $stats['published_last_month'] > 0
            ? round((($stats['published_this_month'] - $stats['published_last_month']) / max($stats['published_last_month'], 1)) * 100)
            : ($stats['published_this_month'] > 0 ? 100 : 0);

        $dayDelta = $stats['page_views_yesterday'] > 0
            ? round((($stats['page_views_today'] - $stats['page_views_yesterday']) / max($stats['page_views_yesterday'], 1)) * 100)
            : ($stats['page_views_today'] > 0 ? 100 : 0);

        return view('admin.dashboard', compact(
            'stats',
            'dailyViews',
            'hourlyBuckets',
            'publishBuckets',
            'topArticles',
            'topCategories',
            'topCommenters',
            'topBookmarked',
            'pendingQueue',
            'recentActivity',
            'recentArticles',
            'health',
            'monthDelta',
            'dayDelta'
        ));
    }

    /**
     * Quick system status check: DB, cache, queue.
     */
    private function systemHealth(): array
    {
        $checks = [];

        // DB
        try {
            $startedAt = microtime(true);
            DB::select('select 1');
            $checks['database'] = [
                'ok' => true,
                'label' => 'MySQL',
                'ms' => round((microtime(true) - $startedAt) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            $checks['database'] = ['ok' => false, 'label' => 'MySQL', 'error' => $e->getMessage()];
        }

        // Cache (Redis)
        try {
            $startedAt = microtime(true);
            Cache::put('__health_probe', 'ok', 5);
            $val = Cache::get('__health_probe');
            $checks['cache'] = [
                'ok' => $val === 'ok',
                'label' => 'Redis Cache',
                'ms' => round((microtime(true) - $startedAt) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            $checks['cache'] = ['ok' => false, 'label' => 'Redis Cache', 'error' => $e->getMessage()];
        }

        // Storage writability
        try {
            $checks['storage'] = [
                'ok' => is_writable(storage_path('app')),
                'label' => 'Storage',
            ];
        } catch (\Throwable $e) {
            $checks['storage'] = ['ok' => false, 'label' => 'Storage', 'error' => $e->getMessage()];
        }

        // App debug status
        $checks['debug'] = [
            'ok' => ! config('app.debug'),
            'label' => config('app.debug') ? 'Debug ON' : 'Production',
        ];

        return $checks;
    }
}

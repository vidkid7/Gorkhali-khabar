<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PageView;
use App\Support\DateGrouping;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $now = now();
        $last7days = $now->copy()->subDays(7);
        $last30days = $now->copy()->subDays(30);

        $stats = [
            'views_today' => PageView::query()->where('created_at', '>=', $now->copy()->startOfDay())->count(),
            'views_7d' => PageView::query()->where('created_at', '>=', $last7days)->count(),
            'views_30d' => PageView::query()->where('created_at', '>=', $last30days)->count(),
            'total_views' => (int) Article::query()->sum('view_count'),
            'top_articles' => Article::query()
                ->where('status', 'PUBLISHED')
                ->orderByDesc('view_count')
                ->limit(10)
                ->get(['id', 'title', 'slug', 'view_count']),
        ];

        // Daily view counts (last 14 days)
        $daily = PageView::query()
            ->where('created_at', '>=', $now->copy()->subDays(14))
            ->selectRaw(DateGrouping::day().' as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.analytics.index', compact('stats', 'daily'));
    }
}

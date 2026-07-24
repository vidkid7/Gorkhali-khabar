<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Bookmark;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-mostly viewer for user bookmarks. Useful for content / editorial
 * decisions: see which articles readers save the most.
 */
class BookmarkController extends Controller
{
    public function index(Request $request): View
    {
        $query = Bookmark::query()
            ->with(['user:id,name,email', 'article:id,title,slug']);

        if ($search = trim((string) $request->query('search'))) {
            $query->whereHas('article', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $bookmarks = $query->orderByDesc('created_at')->paginate(50)->withQueryString();

        $topArticles = Bookmark::query()
            ->selectRaw('article_id, count(*) as total')
            ->groupBy('article_id')
            ->orderByDesc('total')
            ->with('article:id,title,slug')
            ->limit(10)
            ->get();

        $topUsers = Bookmark::query()
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,name')
            ->limit(10)
            ->get();

        return view('admin.bookmarks.index', compact('bookmarks', 'topArticles', 'topUsers'));
    }

    public function destroy(Request $request, AuditService $audit, Bookmark $bookmark): RedirectResponse
    {
        $old = $bookmark->only(['user_id', 'article_id']);
        $bookmark->delete();
        $audit->record($request->user(), 'DELETE', 'Bookmark', $bookmark->id, oldValue: $old);

        return back()->with('status', 'बुकमार्क हटाइयो।');
    }
}
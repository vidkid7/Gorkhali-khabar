<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $query = NewsletterSubscription::query();
        if ($search = trim((string) $request->query('search'))) {
            $query->where('email', 'like', "%{$search}%");
        }
        $subs = $query->orderByDesc('created_at')->paginate(50)->withQueryString();

        return view('admin.newsletter.index', compact('subs'));
    }

    public function destroy(Request $request, AuditService $audit, NewsletterSubscription $newsletter): RedirectResponse
    {
        $old = $newsletter->only('email');
        $newsletter->delete();
        $audit->record($request->user(), 'DELETE', 'NewsletterSubscription', $newsletter->id, oldValue: $old);

        return back()->with('status', 'सदस्यता हटाइयो।');
    }
}
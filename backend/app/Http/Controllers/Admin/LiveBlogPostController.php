<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveBlog;
use App\Models\LiveBlogPost;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LiveBlogPostController extends Controller
{
    public function store(Request $request, AuditService $audit, LiveBlog $liveBlog): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'body_en' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);
        $data['live_blog_id'] = $liveBlog->id;
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $data['published_at'] ?? now();
        $post = LiveBlogPost::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'LiveBlogPost', $post->id, newValue: $post->toArray());

        return back()->with('status', 'लाइभ अपडेट थपियो।');
    }

    public function destroy(Request $request, AuditService $audit, LiveBlog $liveBlog, LiveBlogPost $post): RedirectResponse
    {
        abort_unless($post->live_blog_id === $liveBlog->id, 404);
        $old = $post->toArray();
        $post->delete();
        $audit->record($request->user(), 'DELETE', 'LiveBlogPost', $post->id, oldValue: $old);

        return back()->with('status', 'लाइभ अपडेट मेटाइयो।');
    }
}

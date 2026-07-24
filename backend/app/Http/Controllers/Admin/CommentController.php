<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::query()->with(['article:id,title', 'user:id,name']);
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        $comments = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function update(Request $request, AuditService $audit, Comment $comment): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:PENDING,APPROVED,REJECTED,SPAM']]);
        $old = $comment->only('status');
        $comment->update($data);
        $audit->record($request->user(), 'UPDATE', 'Comment', $comment->id, oldValue: $old, newValue: ['status' => $comment->status]);

        return back()->with('status', 'टिप्पणी अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Comment $comment): RedirectResponse
    {
        $old = $comment->toArray();
        $comment->delete();
        $audit->record($request->user(), 'DELETE', 'Comment', $comment->id, oldValue: $old);

        return back()->with('status', 'टिप्पणी मेटाइयो।');
    }
}
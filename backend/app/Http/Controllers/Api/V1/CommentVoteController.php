<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentVoteController extends Controller
{
    public function store(Request $request, string $id): JsonResponse
    {
        $comment = Comment::query()->find($id);
        if (! $comment) {
            return ApiResponse::error('टिप्पणी फेला परेन', 404);
        }
        $isLike = $request->boolean('is_like');
        $result = DB::transaction(function () use ($comment, $request, $isLike): array {
            $lockedComment = Comment::query()->whereKey($comment->id)->lockForUpdate()->firstOrFail();
            $vote = CommentVote::query()->where('comment_id', $lockedComment->id)->where('user_id', $request->user()->id)->lockForUpdate()->first();
            if ($vote && (bool) $vote->is_like === $isLike) {
                $vote->delete();
                $column = $isLike ? 'like_count' : 'dislike_count';
                $lockedComment->decrement($column);

                return ['status' => 200, 'action' => 'removed'];
            }
            if ($vote) {
                $oldColumn = $vote->is_like ? 'like_count' : 'dislike_count';
                $newColumn = $isLike ? 'like_count' : 'dislike_count';
                $lockedComment->decrement($oldColumn);
                $lockedComment->increment($newColumn);
                $vote->update(['is_like' => $isLike]);

                return ['status' => 200, 'action' => 'switched'];
            }

            CommentVote::query()->create(['comment_id' => $lockedComment->id, 'user_id' => $request->user()->id, 'is_like' => $isLike]);
            $lockedComment->increment($isLike ? 'like_count' : 'dislike_count');

            return ['status' => 201, 'action' => 'created'];
        });

        return ApiResponse::success(['action' => $result['action']], $result['status']);
    }
}

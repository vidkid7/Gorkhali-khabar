<?php
namespace App\Models;
class CommentVote extends LegacyModel { public const UPDATED_AT = null; public function comment() { return $this->belongsTo(Comment::class); } public function user() { return $this->belongsTo(User::class); } }
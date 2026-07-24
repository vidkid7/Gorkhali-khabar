<?php
namespace App\Models;
class Bookmark extends LegacyModel { public const UPDATED_AT = null; public function user() { return $this->belongsTo(User::class); } public function article() { return $this->belongsTo(Article::class); } }
<?php
namespace App\Models;
class PageView extends LegacyModel { public const UPDATED_AT = null; public function article() { return $this->belongsTo(Article::class); } public function user() { return $this->belongsTo(User::class); } }
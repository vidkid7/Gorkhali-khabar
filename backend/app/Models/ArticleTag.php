<?php
namespace App\Models;
class ArticleTag extends LegacyModel { protected $table = 'article_tags'; public $incrementing = false; public $timestamps = false; protected $primaryKey = null; public function article() { return $this->belongsTo(Article::class); } public function tag() { return $this->belongsTo(Tag::class); } }
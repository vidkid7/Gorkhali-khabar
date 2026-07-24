<?php
namespace App\Models;
class Tag extends LegacyModel { public const UPDATED_AT = null; public function articleTags() { return $this->hasMany(ArticleTag::class); } }
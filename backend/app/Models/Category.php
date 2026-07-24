<?php
namespace App\Models;
class Category extends LegacyModel { public function parent() { return $this->belongsTo(self::class, 'parent_id'); } public function children() { return $this->hasMany(self::class, 'parent_id'); } public function articles() { return $this->hasMany(Article::class); } }
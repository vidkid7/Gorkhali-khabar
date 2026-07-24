<?php
namespace App\Models;
class WebStory extends LegacyModel { protected function casts(): array { return ['slides' => 'array', 'is_active' => 'boolean']; } public function category() { return $this->belongsTo(Category::class); } }
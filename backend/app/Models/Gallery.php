<?php
namespace App\Models;
class Gallery extends LegacyModel { public function images() { return $this->hasMany(GalleryImage::class)->orderBy('sort_order'); } }
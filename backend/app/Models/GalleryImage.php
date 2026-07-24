<?php
namespace App\Models;
class GalleryImage extends LegacyModel { public const UPDATED_AT = null; public function gallery() { return $this->belongsTo(Gallery::class); } }
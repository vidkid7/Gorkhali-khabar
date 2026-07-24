<?php
namespace App\Models;
class Session extends LegacyModel { protected $table = 'sessions'; public $timestamps = false; public function user() { return $this->belongsTo(User::class); } }
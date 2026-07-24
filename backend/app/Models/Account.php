<?php
namespace App\Models;
class Account extends LegacyModel { public $timestamps = false; public function user() { return $this->belongsTo(User::class); } }
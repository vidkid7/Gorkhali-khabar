<?php
namespace App\Models;
class VerificationToken extends LegacyModel { protected $table = 'verification_tokens'; public $incrementing = false; public $timestamps = false; protected $primaryKey = 'token'; }
<?php
namespace App\Models;
class NewsletterSubscription extends LegacyModel { protected function casts(): array { return ['is_active' => 'boolean']; } }
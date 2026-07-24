<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$articles = \App\Models\Article::whereHas('category', function($q) {
    $q->where('slug', 'like', '%pradesh%');
})->get();

foreach ($articles as $a) {
    echo $a->id . ' => ' . $a->title . ' | category_id: ' . $a->category_id . ' | category: ' . $a->category->slug . ' | status: ' . $a->status . PHP_EOL;
}

echo "Total: " . count($articles) . PHP_EOL;
<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Article;
use App\Models\Category;

foreach (Category::where('slug', 'like', '%pradesh%')->get() as $c) {
    $articles = Article::where('category_id', $c->id)->where('status', 'PUBLISHED')->get();
    echo $c->slug . ' => ' . count($articles) . ' articles' . PHP_EOL;
    foreach ($articles as $a) {
        echo '  - ' . $a->title . ' (status: ' . $a->status . ')' . PHP_EOL;
    }
}
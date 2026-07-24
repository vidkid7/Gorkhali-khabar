<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$prisma = new \Prisma\PrismaClient();

$articles = $prisma->article()->findMany([
    'where' => [
        'status' => 'PUBLISHED',
        'category' => [
            'slug' => 'bagmati-pradesh'
        ]
    ],
    'include' => [
        'category' => ['select' => ['id', 'name', 'slug']],
        'author' => ['select' => ['id', 'name']],
    ],
    'orderBy' => ['published_at' => 'desc'],
    'take' => 5,
]);

echo "Found " . count($articles) . " articles\n";
foreach ($articles as $a) {
    echo $a->id . ' => ' . $a->title . ' | category: ' . $a->category->slug . ' | status: ' . $a->status . "\n";
}
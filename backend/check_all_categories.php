<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (\App\Models\Category::all() as $c) {
    echo $c->slug . ' => ' . $c->name . ' => ' . $c->color . PHP_EOL;
}
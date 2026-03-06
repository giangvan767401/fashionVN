<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = \App\Models\Category::all();
foreach($categories as $c) {
    echo $c->id . ': ' . $c->name . ' (parent: ' . $c->parent_id . '), products: ' . $c->products()->count() . PHP_EOL;
}

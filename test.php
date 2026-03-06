<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = \App\Models\Category::all();
foreach ($categories as $cat) {
    echo "Category: {$cat->name} (ID: {$cat->id})\n";
    $productsCount = $cat->products()->count();
    echo "  Products: $productsCount\n";
    
    // Check whereHas
    $queryCount = \App\Models\Product::whereHas('categories', function($q) use ($cat) {
        $q->where('categories.id', $cat->id);
    })->count();
    echo "  whereHas Products: $queryCount\n";
}

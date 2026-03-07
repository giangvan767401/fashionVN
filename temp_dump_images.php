<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Print out products
$product = App\Models\Product::where('name', 'like', '%Zai%')->first();
if ($product) {
    echo "Images for product: " . $product->name . "\n";
    print_r($product->images->pluck('url')->toArray());
} else {
    echo "Product not found.\n";
}

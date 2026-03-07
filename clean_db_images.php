<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

$images = ProductImage::all();
$fixedCount = 0;

foreach ($images as $img) {
    $originalUrl = $img->url;
    $newUrl = $originalUrl;
    
    // 1. Remove hardcoded http://localhost:8000/
    if (Str::startsWith($newUrl, 'http')) {
        $parsed = parse_url($newUrl);
        if (isset($parsed['path'])) {
            // path usually starts with /images/products or /storage
            $newUrl = ltrim($parsed['path'], '/'); 
        }
    }
    
    // 2. Check if the file exists. If it doesn't, try to find a matching file without timestamp
    if (Str::startsWith($newUrl, 'images/products/')) {
        $pathOnDisk = public_path($newUrl);
        if (!File::exists($pathOnDisk)) {
            // maybe it has a timestamp prefix like 'images/products/1234567890_filename.jpg'
            $filename = basename($newUrl);
            if (preg_match('/^\d+_(.+)$/', $filename, $matches)) {
                $withoutTimestamp = $matches[1];
                $tryPath = public_path('images/products/' . $withoutTimestamp);
                if (File::exists($tryPath)) {
                    $newUrl = 'images/products/' . $withoutTimestamp;
                }
            }
        }
    }
    
    // Save if changed
    if ($newUrl !== $originalUrl) {
        $img->url = $newUrl;
        $img->save();
        echo "Fixed: $originalUrl -> $newUrl\n";
        $fixedCount++;
    }
}

echo "Total images fixed in DB: $fixedCount\n";

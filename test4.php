<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = \App\Models\Category::find(2)->products->first();
if ($p) {
    echo $p->name . ' - is_active: ' . $p->is_active . "\n";
} else {
    echo "No product found\n";
}

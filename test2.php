<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new \Illuminate\Http\Request(['category' => 2]);
$controller = new \App\Http\Controllers\CollectionController();
$view = $controller->index($request);
$data = $view->getData();
echo "Products count for category 2: " . count($data['products']) . "\n";
print_r($data['products']);

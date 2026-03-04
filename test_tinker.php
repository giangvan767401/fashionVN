<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = \App\Models\CartItem::with('variant.attributeValues.group')->latest()->first();

echo json_encode([
    'variant_id' => $item->variant_id,
    'label' => $item->variant_label,
    'attrs' => $item->variant->attributeValues->map(function($a) {
        return [
            'group' => optional($a->group)->name,
            'value' => $a->value
        ];
    })
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

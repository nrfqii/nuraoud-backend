<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$correctPrices = [
    'Esensi Oud Royal' => 4500000,
    'Romansa Rose Oud' => 3750000,
    'Prestise Amber Oud' => 5250000,
    'Mistik Sandalwood Oud' => 4200000,
    'Elegansi Musk Oud' => 3450000,
    'Royale Saffron Oud' => 6000000,
];

$updated = 0;
foreach ($correctPrices as $name => $price) {
    $product = Product::where('name', $name)->first();
    if ($product && $product->price != $price) {
        $product->update(['price' => $price]);
        $updated++;
        echo "Updated {$name}: Rp " . number_format($price, 0, ',', '.') . "\n";
    }
}

echo "Updated {$updated} products\n";

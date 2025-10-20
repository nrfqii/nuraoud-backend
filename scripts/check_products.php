<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::take(5)->get();
echo "Product prices:\n";
foreach ($products as $product) {
    echo "{$product->name}: Rp " . number_format($product->price, 0, ',', '.') . "\n";
}

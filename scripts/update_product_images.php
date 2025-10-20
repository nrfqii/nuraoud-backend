<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$map = [
    'product-1.jpg' => 'products/product-1.jpg',
    'product-2.jpg' => 'products/product-2.jpg',
    'product-3.jpg' => 'products/product-3.jpg',
    'product-4.jpg' => 'products/product-4.jpg',
];

$updated = 0;
foreach (Product::all() as $p) {
    if ($p->image && isset($map[$p->image])) {
        $p->image = $map[$p->image];
        $p->save();
        $updated++;
        echo "Updated product {$p->id} -> {$p->image}\n";
    }
}

echo "Done. Updated: $updated\n";

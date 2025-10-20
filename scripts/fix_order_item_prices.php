<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OrderItem;

$orderItems = OrderItem::with('product')->get();
$updated = 0;

foreach ($orderItems as $item) {
    if ($item->price != $item->product->price) {
        $item->update(['price' => $item->product->price]);
        $updated++;
        echo "Updated OrderItem #{$item->id}: {$item->product->name} @ Rp " . number_format($item->product->price, 0, ',', '.') . "\n";
    }
}

echo "Updated {$updated} order item prices\n";

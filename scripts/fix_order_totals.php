<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

$orders = Order::with('orderItems.product')->get();
$updated = 0;

foreach ($orders as $order) {
    $correctTotal = $order->orderItems->sum(function ($item) {
        return $item->quantity * $item->product->price;
    });

    if ($order->total_price != $correctTotal) {
        $order->update(['total_price' => $correctTotal]);
        $updated++;
        echo "Updated Order #{$order->id}: Rp " . number_format($correctTotal, 0, ',', '.') . "\n";
    }
}

echo "Updated {$updated} order totals\n";

<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

$orderCount = Order::count();
echo "Total orders: $orderCount\n";

if ($orderCount > 0) {
    $orders = Order::with('orderItems.product')->take(3)->get();
    foreach ($orders as $order) {
        echo "Order #{$order->id}: Total = {$order->total_price}\n";
        foreach ($order->orderItems as $item) {
            echo "  - {$item->product->name} x{$item->quantity} @ Rp " . number_format($item->price, 0, ',', '.') . "\n";
        }
        echo "\n";
    }
}

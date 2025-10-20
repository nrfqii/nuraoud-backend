<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::select('id','name','image')->get();
foreach ($products as $p) {
    echo $p->id . ' | ' . $p->name . ' | ' . ($p->image ?? 'NULL') . PHP_EOL;
}

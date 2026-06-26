<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$transaction = App\Models\Transaction::with(['user', 'property.rules', 'property.amenities'])->first();
if (!$transaction) {
    exit("No transaction\n");
}

try {
    $path = (new App\Services\RequestDetailsPdfExporter())->export($transaction);
    $head = file_get_contents($path, false, null, 0, 8);
    echo "OK path=$path size=" . filesize($path) . " header=$head\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

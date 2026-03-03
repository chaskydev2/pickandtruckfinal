<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "public_path(): " . public_path() . PHP_EOL;
echo "public_path('documents/89'): " . public_path('documents/89') . PHP_EOL;
echo "base_path(): " . base_path() . PHP_EOL;
echo PHP_EOL;

// Check logs for recent document upload attempts
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $relevant = array_filter($lines, fn($l) => str_contains($l, 'documento') || str_contains($l, 'Documento') || str_contains($l, 'documents/89'));
    $last = array_slice($relevant, -20);
    echo "=== Últimas líneas del log relacionadas a documentos ===" . PHP_EOL;
    foreach ($last as $line) echo $line;
}

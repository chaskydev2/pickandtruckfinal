<?php
// Quick diagnostic - check DB paths and file existence
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$memberships = \App\Models\Membership::whereNotNull('payment_proof_path')->get(['id','payment_proof_path']);

foreach ($memberships as $m) {
    $path = $m->payment_proof_path;
    $fullPath = __DIR__ . '/storage/app/public/' . $path;
    $exists = file_exists($fullPath) ? 'YES' : 'NO';
    $size = $exists === 'YES' ? filesize($fullPath) : 0;
    $url = 'https://app.pickntruck.com/serve_file.php?path=' . urlencode($path);
    echo "ID:{$m->id} | PATH:{$path} | FILE_EXISTS:{$exists} | SIZE:{$size} | URL:{$url}\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$notifs = DB::table('notifications')
    ->where('type', 'like', '%BidStatus%')
    ->latest()
    ->take(5)
    ->get(['id','type','data']);

foreach ($notifs as $n) {
    echo "TYPE: " . $n->type . PHP_EOL;
    echo "DATA: " . $n->data . PHP_EOL;
    echo "---" . PHP_EOL;
}

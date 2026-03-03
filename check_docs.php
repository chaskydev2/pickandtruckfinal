<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get recent documents
$docs = DB::table('user_documents')->latest()->take(3)->get(['id','user_id','file_path']);
foreach ($docs as $d) {
    echo "ID: {$d->id} | user_id: {$d->user_id} | file_path: {$d->file_path}" . PHP_EOL;
}

// Check if storage symlink exists
echo PHP_EOL . "Storage symlink: " . (is_link(__DIR__.'/public/storage') ? 'EXISTS -> '.readlink(__DIR__.'/public/storage') : 'MISSING') . PHP_EOL;

// Check if documents folder exists in public
echo "public/documents exists: " . (is_dir(__DIR__.'/public/documents') ? 'YES' : 'NO') . PHP_EOL;
echo "storage/app/public/documents exists: " . (is_dir(__DIR__.'/storage/app/public/documents') ? 'YES' : 'NO') . PHP_EOL;

// List a sample file
$files = glob(__DIR__.'/storage/app/public/documents/*/*') ?: [];
if ($files) echo "Sample file: " . $files[0] . PHP_EOL;
$files2 = glob(__DIR__.'/public/documents/*/*') ?: [];
if ($files2) echo "Sample public/documents file: " . $files2[0] . PHP_EOL;

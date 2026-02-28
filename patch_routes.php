<?php
$file = '/home/u556487000/domains/app.pickntruck.com/public_html/routes/web.php';
$content = file_get_contents($file);

$newRoute = '
// Serve storage files (Hostinger no permite symlinks)
Route::get(\'/storage/{path}\', function($path) {
    $fullPath = storage_path(\'app/public/\' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath);
})->where(\'path\', \'.*\');

';

$content = str_replace('Route::fallback', $newRoute . 'Route::fallback', $content);
file_put_contents($file, $content);
echo "Done\n";

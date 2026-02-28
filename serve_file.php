<?php
$allowed_dirs = ['membership_proofs', 'documents', 'empresas'];
$path = isset($_GET['path']) ? $_GET['path'] : '';
$path = ltrim(str_replace('..', '', $path), '/');
$topDir = explode('/', $path)[0];
if (!in_array($topDir, $allowed_dirs)) { http_response_code(403); exit('Forbidden'); }
// Try parent of public_html (standard Laravel: project_root/storage/app/public)
// then fall back to public_html/storage/app/public (if project root = public_html)
$base1 = dirname(__DIR__) . '/storage/app/public/';
$base2 = __DIR__ . '/storage/app/public/';
$fullPath = file_exists($base1 . $path) ? ($base1 . $path) : ($base2 . $path);
if (!file_exists($fullPath) || !is_file($fullPath)) { 
    http_response_code(404); 
    exit('Not found. Tried: ' . $base1 . $path . ' | and: ' . $base2 . $path); 
}
$mime = mime_content_type($fullPath) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=86400');
readfile($fullPath);
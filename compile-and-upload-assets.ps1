# Script para compilar assets localmente y subirlos al servidor
# Ejecutar desde PowerShell LOCAL (no SSH)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "COMPILANDO ASSETS PARA PRODUCCION" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Compilar assets localmente
Write-Host "Paso 1: Compilando assets con Vite..." -ForegroundColor Yellow
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR al compilar assets" -ForegroundColor Red
    exit 1
}

Write-Host "OK - Assets compilados en public/build/" -ForegroundColor Green
Write-Host ""

# 2. Subir la carpeta build al servidor
Write-Host "Paso 2: Subiendo assets compilados al servidor..." -ForegroundColor Yellow

$SSH_USER = "u556487000"
$SSH_HOST = "195.35.33.170"
$SSH_PORT = "65002"
$REMOTE_PATH = "domains/app.pickntruck.com/public_html/public"

# Verificar que existe la carpeta build
if (-not (Test-Path "public/build")) {
    Write-Host "ERROR: No se encontro la carpeta public/build/" -ForegroundColor Red
    exit 1
}

# Subir la carpeta build
$scpCommand = "scp -r -P $SSH_PORT `"public/build`" ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"

Write-Host "Ejecutando: scp -r public/build ..." -ForegroundColor Gray
Invoke-Expression $scpCommand

if ($LASTEXITCODE -eq 0) {
    Write-Host "OK - Assets subidos correctamente" -ForegroundColor Green
} else {
    Write-Host "ERROR al subir assets" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "ASSETS COMPILADOS Y SUBIDOS" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Ahora ve al servidor SSH y ejecuta:" -ForegroundColor Yellow
Write-Host "php artisan view:clear" -ForegroundColor Cyan
Write-Host "php artisan config:cache" -ForegroundColor Cyan
Write-Host ""

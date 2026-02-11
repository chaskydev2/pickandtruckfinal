# Script de despliegue COMPLETO a producción
# Sube TODOS los archivos del proyecto (excepto .env, vendor, node_modules, etc.)

$SSH_USER = "u556487000"
$SSH_HOST = "195.35.33.170"
$SSH_PORT = "65002"
$REMOTE_PATH = "domains/app.pickntruck.com/public_html"

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "     DESPLIEGUE COMPLETO A PRODUCCION - app.pickntruck.com" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "IMPORTANTE: Este script subira TODO el codigo del proyecto" -ForegroundColor Yellow
Write-Host "NO subira: .env, vendor/, node_modules/, .git/" -ForegroundColor Yellow
Write-Host ""

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "artisan")) {
    Write-Host "ERROR: No estas en el directorio correcto del proyecto Laravel" -ForegroundColor Red
    exit 1
}

Write-Host "Directorio del proyecto verificado OK" -ForegroundColor Green
Write-Host ""

# Directorios principales a subir
$directories = @(
    "app",
    "bootstrap",
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "storage"
)

# Archivos raiz a subir
$rootFiles = @(
    "artisan",
    "composer.json",
    "composer.lock",
    "package.json",
    "package-lock.json",
    "vite.config.js",
    "tailwind.config.js",
    "postcss.config.js",
    "phpunit.xml"
)

$confirm = Read-Host "Deseas continuar con la subida COMPLETA del proyecto? (escribe SI en mayusculas)"
if ($confirm -ne "SI") {
    Write-Host "Despliegue cancelado" -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "PASO 1: Subiendo directorios completos" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

foreach ($dir in $directories) {
    Write-Host "Subiendo directorio: $dir/" -ForegroundColor White
    
    if (-not (Test-Path $dir)) {
        Write-Host "  Directorio no encontrado, saltando..." -ForegroundColor Yellow
        continue
    }
    
    $scpCommand = "scp -r -P $SSH_PORT `"$dir`" ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"
    
    try {
        Write-Host "  Ejecutando: scp -r -P $SSH_PORT $dir ..." -ForegroundColor Gray
        Invoke-Expression $scpCommand 2>&1 | Out-Null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  OK - $dir/ subido correctamente" -ForegroundColor Green
        } else {
            Write-Host "  ERROR al subir $dir/" -ForegroundColor Red
        }
    } catch {
        Write-Host "  ERROR: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    Write-Host ""
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "PASO 2: Subiendo archivos raiz" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

foreach ($file in $rootFiles) {
    if (Test-Path $file) {
        Write-Host "Subiendo: $file" -ForegroundColor White
        
        $scpCommand = "scp -P $SSH_PORT `"$file`" ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"
        
        try {
            Invoke-Expression $scpCommand 2>&1 | Out-Null
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "  OK - Subido" -ForegroundColor Green
            } else {
                Write-Host "  ERROR" -ForegroundColor Red
            }
        } catch {
            Write-Host "  ERROR: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "$file no encontrado, saltando..." -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "PASO 3: Comandos a ejecutar en el servidor" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Ahora debes conectarte al servidor y ejecutar estos comandos:" -ForegroundColor Yellow
Write-Host ""
Write-Host "# 1. Conectar al servidor:" -ForegroundColor White
Write-Host "ssh ${SSH_USER}@${SSH_HOST} -p ${SSH_PORT}" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 2. Ir al directorio de la aplicacion:" -ForegroundColor White
Write-Host "cd $REMOTE_PATH" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 3. Instalar dependencias de Composer:" -ForegroundColor White
Write-Host "composer install --no-dev --optimize-autoloader" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 4. Dar permisos a storage y bootstrap/cache:" -ForegroundColor White
Write-Host "chmod -R 775 storage" -ForegroundColor Cyan
Write-Host "chmod -R 775 bootstrap/cache" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 5. Limpiar caches:" -ForegroundColor White
Write-Host "php artisan route:clear" -ForegroundColor Cyan
Write-Host "php artisan config:clear" -ForegroundColor Cyan
Write-Host "php artisan cache:clear" -ForegroundColor Cyan
Write-Host "php artisan view:clear" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 6. Optimizar para produccion:" -ForegroundColor White
Write-Host "php artisan config:cache" -ForegroundColor Cyan
Write-Host "php artisan route:cache" -ForegroundColor Cyan
Write-Host "php artisan view:cache" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 7. SI HAY NUEVAS MIGRACIONES (verificar primero):" -ForegroundColor White
Write-Host "php artisan migrate --force" -ForegroundColor Cyan
Write-Host ""
Write-Host "# 8. Instalar dependencias de NPM y compilar assets:" -ForegroundColor White
Write-Host "npm install" -ForegroundColor Cyan
Write-Host "npm run build" -ForegroundColor Cyan
Write-Host ""
Write-Host "================================================================" -ForegroundColor Green
Write-Host "DESPLIEGUE COMPLETO FINALIZADO" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""

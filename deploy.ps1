# deploy.ps1 - Script de despliegue para Windows

# Configuración de colores
$Green = "\033[0;32m"
$Yellow = "\033[1;33m"
$Red = "\033[0;31m"
$NC = "\033[0m" # No Color

# Función para mostrar mensajes
function Show-Message {
    param (
        [string]$Message,
        [string]$Type = "info"
    )
    
    $timestamp = Get-Date -Format "HH:mm:ss"
    switch ($Type) {
        "success" { Write-Host "[$timestamp] ✅ $Message" -ForegroundColor Green }
        "warning" { Write-Host "[$timestamp] ⚠️ $Message" -ForegroundColor Yellow }
        "error" { Write-Host "[$timestamp] ❌ $Message" -ForegroundColor Red }
        default { Write-Host "[$timestamp] ℹ️ $Message" -ForegroundColor Cyan }
    }
}

# Iniciar despliegue
Show-Message "🚀 Iniciando despliegue de PicknTruck..." "warning"

# 1. Actualizar código del repositorio
Show-Message "1. Actualizando código del repositorio..."
try {
    git pull origin main
    Show-Message "Código actualizado correctamente" "success"
} catch {
    Show-Message "Error al actualizar el código: $_" "error"
    exit 1
}

# 2. Instalar dependencias de Composer
Show-Message "2. Instalando dependencias de Composer..."
try {
    composer install --no-interaction --prefer-dist --optimize-autoloader
    Show-Message "Dependencias de Composer instaladas" "success"
} catch {
    Show-Message "Error al instalar dependencias de Composer: $_" "error"
    exit 1
}

# 3. Instalar dependencias NPM y compilar assets
Show-Message "3. Instalando dependencias NPM y compilando assets..."
try {
    npm install
    npm run build
    Show-Message "Assets compilados correctamente" "success"
} catch {
    Show-Message "Error al compilar los assets: $_" "error"
    exit 1
}

# 4. Limpiar cachés
Show-Message "4. Limpiando cachés..."
try {
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    php artisan clear-compiled
    Show-Message "Cachés limpiadas correctamente" "success"
} catch {
    Show-Message "Error al limpiar las cachés: $_" "error"
    exit 1
}

# 5. Optimizar para producción
Show-Message "5. Optimizando la aplicación para producción..."
try {
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    Show-Message "Aplicación optimizada" "success"
} catch {
    Show-Message "Error al optimizar la aplicación: $_" "error"
    exit 1
}

# 6. Ejecutar migraciones
Show-Message "6. Ejecutando migraciones..."
try {
    php artisan migrate --force
    Show-Message "Migraciones ejecutadas" "success"
} catch {
    Show-Message "Error al ejecutar migraciones: $_" "error"
    exit 1
}

# 7. Establecer permisos (solo para Linux/Unix)
Show-Message "7. Configuración de permisos (omitido en Windows)" "warning"
# En Windows, los permisos se manejan de manera diferente

# 8. Reiniciar colas
Show-Message "8. Reiniciando colas..."
try {
    php artisan queue:restart
    Show-Message "Colas reiniciadas" "success"
} catch {
    Show-Message "Error al reiniciar las colas: $_" "error"
    exit 1
}

# 9. Crear enlace simbólico de almacenamiento
Show-Message "9. Creando enlace simbólico de almacenamiento..."
try {
    php artisan storage:link
    Show-Message "Enlace de almacenamiento creado" "success"
} catch {
    Show-Message "Error al crear el enlace de almacenamiento: $_" "error"
    exit 1
}

# Mensaje final
Show-Message "✨ ¡Despliegue completado con éxito! ✨" "success"
Show-Message "Verifica que todo funcione correctamente en tu navegador." "info"

# Mostrar versión de PHP y extensiones
Show-Message "Información del sistema:" "info"
php -v
php -m | Select-String -Pattern 'pdo_mysql|mbstring|tokenizer|xml|ctype|json|openssl'

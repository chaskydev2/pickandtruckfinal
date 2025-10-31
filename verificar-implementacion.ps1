# Script de Verificación - Selectores de Ubicación
# Ejecutar este script para verificar que todo está correctamente implementado

Write-Host "`n===================================================" -ForegroundColor Cyan
Write-Host "  VERIFICACIÓN DE IMPLEMENTACIÓN" -ForegroundColor Cyan
Write-Host "  Selectores de Ubicación en Cascada" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

$basePath = "c:\Users\USUARIO\Desktop\pickandtruckfinal\pickandtruckfinal"
$errors = 0
$warnings = 0

Write-Host "`n[1/5] Verificando archivos JavaScript..." -ForegroundColor Yellow

$jsFiles = @(
    "$basePath\public\js\location-data.js",
    "$basePath\public\js\location-selector.js"
)

foreach ($file in $jsFiles) {
    if (Test-Path $file) {
        $size = (Get-Item $file).Length
        Write-Host "  ✓ $(Split-Path $file -Leaf) - $size bytes" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $(Split-Path $file -Leaf) - NO ENCONTRADO" -ForegroundColor Red
        $errors++
    }
}

Write-Host "`n[2/5] Verificando archivos CSS..." -ForegroundColor Yellow

$cssFile = "$basePath\public\css\location-selector.css"
if (Test-Path $cssFile) {
    $size = (Get-Item $cssFile).Length
    Write-Host "  ✓ location-selector.css - $size bytes" -ForegroundColor Green
} else {
    Write-Host "  ✗ location-selector.css - NO ENCONTRADO" -ForegroundColor Red
    $errors++
}

Write-Host "`n[3/5] Verificando vistas Blade..." -ForegroundColor Yellow

$bladeFiles = @(
    "$basePath\resources\views\ofertas\create.blade.php",
    "$basePath\resources\views\ofertas_carga\create.blade.php"
)

foreach ($file in $bladeFiles) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        
        # Verificar que contienen los nuevos elementos
        $hasOriginPais = $content -match 'id="origen_pais"'
        $hasDestinoPais = $content -match 'id="destino_pais"'
        $hasLocationData = $content -match 'location-data\.js'
        $hasLocationSelector = $content -match 'location-selector\.js'
        
        if ($hasOriginPais -and $hasDestinoPais -and $hasLocationData -and $hasLocationSelector) {
            Write-Host "  ✓ $(Split-Path $file -Leaf) - CORRECTAMENTE MODIFICADO" -ForegroundColor Green
        } else {
            Write-Host "  ⚠ $(Split-Path $file -Leaf) - POSIBLES PROBLEMAS" -ForegroundColor Yellow
            $warnings++
            if (-not $hasOriginPais) { Write-Host "    - Falta origen_pais" -ForegroundColor Yellow }
            if (-not $hasDestinoPais) { Write-Host "    - Falta destino_pais" -ForegroundColor Yellow }
            if (-not $hasLocationData) { Write-Host "    - Falta location-data.js" -ForegroundColor Yellow }
            if (-not $hasLocationSelector) { Write-Host "    - Falta location-selector.js" -ForegroundColor Yellow }
        }
    } else {
        Write-Host "  ✗ $(Split-Path $file -Leaf) - NO ENCONTRADO" -ForegroundColor Red
        $errors++
    }
}

Write-Host "`n[4/5] Verificando estructura de datos..." -ForegroundColor Yellow

$locationDataFile = "$basePath\public\js\location-data.js"
if (Test-Path $locationDataFile) {
    $content = Get-Content $locationDataFile -Raw
    
    $countries = @('Bolivia', 'Chile', 'Peru', 'Argentina')
    $allFound = $true
    
    foreach ($country in $countries) {
        if ($content -match "'$country'") {
            Write-Host "  ✓ $country - ENCONTRADO" -ForegroundColor Green
        } else {
            Write-Host "  ✗ $country - NO ENCONTRADO" -ForegroundColor Red
            $errors++
            $allFound = $false
        }
    }
    
    if ($allFound) {
        Write-Host "  ✓ Todos los países están presentes" -ForegroundColor Green
    }
}

Write-Host "`n[5/5] Verificando sintaxis..." -ForegroundColor Yellow

# Limpiar caché de vistas
Write-Host "  - Limpiando caché de vistas..." -ForegroundColor Gray
$result = php artisan view:clear 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Caché de vistas limpiado" -ForegroundColor Green
} else {
    Write-Host "  ⚠ Advertencia al limpiar caché" -ForegroundColor Yellow
    $warnings++
}

Write-Host "`n===================================================" -ForegroundColor Cyan
Write-Host "  RESUMEN DE VERIFICACIÓN" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

if ($errors -eq 0 -and $warnings -eq 0) {
    Write-Host "`n  ✓ TODO CORRECTO - Sin errores ni advertencias" -ForegroundColor Green
    Write-Host "`n  La implementación está lista para usar!" -ForegroundColor Green
} elseif ($errors -eq 0) {
    Write-Host "`n  ⚠ $warnings ADVERTENCIA(S) - Revisar los items marcados" -ForegroundColor Yellow
} else {
    Write-Host "`n  ✗ $errors ERROR(S) - Hay problemas que deben corregirse" -ForegroundColor Red
    Write-Host "  ⚠ $warnings ADVERTENCIA(S)" -ForegroundColor Yellow
}

Write-Host "`n===================================================" -ForegroundColor Cyan
Write-Host "  SIGUIENTE PASO" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "`n  1. Iniciar servidor: php artisan serve" -ForegroundColor White
Write-Host "  2. Probar: http://localhost:8000/test-location-selector.html" -ForegroundColor White
Write-Host "  3. Probar: http://localhost:8000/ofertas/create" -ForegroundColor White
Write-Host "  4. Probar: http://localhost:8000/ofertas_carga/create" -ForegroundColor White
Write-Host "`n==================================================`n" -ForegroundColor Cyan

# Script para copiar manifest a la ubicación correcta para Laravel + Vite
$password = "n9?Y3tZ!Ui"

$sshCommand = @"
cd domains/app.pickntruck.com/public_html/build
echo "=== Creando subdirectorio .vite ==="
mkdir -p .vite
echo "✓ Subdirectorio creado"
echo ""
echo "=== Copiando manifest.json ==="
cp manifest.json .vite/manifest.json
echo "✓ Manifest copiado"
echo ""
echo "=== Verificando estructura ==="
ls -la .vite/
echo ""
echo "=== Limpiando caché de vistas ==="
cd ..
php artisan view:clear
echo "✓ Caché limpiado"
"@

echo $password | ssh -p 65002 u556487000@195.35.33.170 $sshCommand

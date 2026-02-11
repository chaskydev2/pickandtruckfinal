# Script para actualizar .env a modo producción
$password = "n9?Y3tZ!Ui"

$sshCommand = @"
cd domains/app.pickntruck.com/public_html
echo "=== Actualizando .env a modo producción ==="
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
echo "✓ .env actualizado"
echo ""
echo "=== Verificando cambios ==="
grep -E 'APP_ENV|APP_DEBUG' .env
echo ""
echo "=== Limpiando cachés ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo ""
echo "✓ Cachés limpiados"
echo ""
echo "=== Verificando directorio build/ ==="
ls -la build/.vite/ 2>&1 | head -5
"@

echo $password | ssh -p 65002 u556487000@195.35.33.170 $sshCommand

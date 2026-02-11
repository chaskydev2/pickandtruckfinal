# Script para subir vistas actualizadas
$password = "n9?Y3tZ!Ui"

# Subir guest.blade.php
Write-Host "Subiendo guest.blade.php..."
echo $password | scp -P 65002 "resources/views/layouts/guest.blade.php" u556487000@195.35.33.170:domains/app.pickntruck.com/public_html/resources/views/layouts/

# Subir app.blade.php
Write-Host "Subiendo app.blade.php..."
echo $password | scp -P 65002 "resources/views/layouts/app.blade.php" u556487000@195.35.33.170:domains/app.pickntruck.com/public_html/resources/views/layouts/

Write-Host "✓ Archivos subidos exitosamente"

# Limpiar caché de vistas en el servidor
Write-Host "Limpiando caché de vistas..."
$sshCmd = "cd domains/app.pickntruck.com/public_html ; php artisan view:clear"
echo $password | ssh -p 65002 u556487000@195.35.33.170 $sshCmd

Write-Host "✓ Todo listo! Recarga el navegador con CTRL+SHIFT+R"

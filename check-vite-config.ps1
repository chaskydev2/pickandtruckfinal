# Script para verificar configuración de Vite en producción
$password = "n9?Y3tZ!Ui"
$sshCommand = @"
cd domains/app.pickntruck.com/public_html
echo "=== Verificando APP_ENV ==="
grep APP_ENV .env
echo ""
echo "=== Verificando directorio build/ ==="
ls -la build/ | head -10
echo ""
echo "=== Verificando manifest.json ==="
cat build/manifest.json 2>&1 | head -30
echo ""
echo "=== Verificando .vite/manifest.json ==="
cat build/.vite/manifest.json 2>&1 | head -30
"@

echo $password | ssh -p 65002 u556487000@195.35.33.170 $sshCommand

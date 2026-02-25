# Script de Despliegue Automático - Backend
# ==========================================
# Este script sube todos los archivos del backend al servidor

$SSH_USER = "u556487000"
$SSH_HOST = "195.35.33.170"
$SSH_PORT = "65002"
$REMOTE_PATH = "domains/app.pickntruck.com/public_html"

Write-Host "====================================" -ForegroundColor Green
Write-Host "DESPLIEGUE BACKEND - PICK & TRUCK" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""

# Verificar que estamos en la carpeta correcta
if (-not (Test-Path "app/Models/User.php")) {
    Write-Host "ERROR: Ejecuta este script desde la carpeta pickandtruckfinal" -ForegroundColor Red
    exit 1
}

Write-Host "[1/8] Subiendo migraciones..." -ForegroundColor Yellow
scp -P $SSH_PORT database/migrations/2026_02_25_120000_create_memberships_table.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/database/migrations/"
scp -P $SSH_PORT database/migrations/2026_02_25_120001_create_demo_requests_table.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/database/migrations/"

Write-Host "[2/8] Subiendo modelos..." -ForegroundColor Yellow
scp -P $SSH_PORT app/Models/Membership.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Models/"
scp -P $SSH_PORT app/Models/DemoRequest.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Models/"
scp -P $SSH_PORT app/Models/User.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Models/"

Write-Host "[3/8] Subiendo controladores API..." -ForegroundColor Yellow
scp -P $SSH_PORT app/Http/Controllers/Api/LandingController.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Http/Controllers/Api/"
scp -P $SSH_PORT app/Http/Controllers/Api/MembershipController.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Http/Controllers/Api/"

Write-Host "[4/8] Subiendo Mail classes..." -ForegroundColor Yellow
scp -P $SSH_PORT app/Mail/DemoRequestReceived.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Mail/"
scp -P $SSH_PORT app/Mail/MembershipCreated.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Mail/"
scp -P $SSH_PORT app/Mail/SupportNotification.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/app/Mail/"

Write-Host "[5/8] Subiendo templates de email..." -ForegroundColor Yellow
scp -P $SSH_PORT resources/views/emails/demo-request-received.blade.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/resources/views/emails/"
scp -P $SSH_PORT resources/views/emails/membership-created.blade.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/resources/views/emails/"
scp -P $SSH_PORT resources/views/emails/support-notification.blade.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/resources/views/emails/"

Write-Host "[6/8] Subiendo rutas..." -ForegroundColor Yellow
scp -P $SSH_PORT routes/api.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/routes/"

Write-Host "[7/8] Subiendo configuración CORS..." -ForegroundColor Yellow
scp -P $SSH_PORT config/cors.php "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/config/"

Write-Host "[8/8] Archivos subidos correctamente!" -ForegroundColor Green
Write-Host ""
Write-Host "====================================" -ForegroundColor Green
Write-Host "SIGUIENTE PASO:" -ForegroundColor Cyan
Write-Host "Conéctate por SSH y ejecuta:" -ForegroundColor White
Write-Host ""
Write-Host "ssh ${SSH_USER}@${SSH_HOST} -p ${SSH_PORT}" -ForegroundColor Yellow
Write-Host ""
Write-Host "Luego ejecuta estos comandos:" -ForegroundColor White
Write-Host "cd ${REMOTE_PATH}" -ForegroundColor Yellow
Write-Host "php artisan migrate --force" -ForegroundColor Yellow
Write-Host "mkdir -p storage/app/public/membership_proofs" -ForegroundColor Yellow
Write-Host "chmod 755 storage/app/public/membership_proofs" -ForegroundColor Yellow
Write-Host "php artisan storage:link" -ForegroundColor Yellow
Write-Host "php artisan config:clear && php artisan cache:clear && php artisan route:clear" -ForegroundColor Yellow
Write-Host "php artisan config:cache && php artisan route:cache" -ForegroundColor Yellow
Write-Host "====================================" -ForegroundColor Green

# Instrucciones de Despliegue COMPLETO a Producción

## 🚀 DESPLIEGUE COMPLETO DEL PROYECTO

### Paso 1: Ejecutar el script de despliegue

Desde PowerShell en tu PC (en el directorio del proyecto):

```powershell
cd C:\Users\Ponce\Desktop\Proyectos\pickandtruckfinal
.\deploy-full.ps1
```

El script subirá automáticamente:
- ✅ `app/` - Todo el código de la aplicación
- ✅ `config/` - Archivos de configuración
- ✅ `database/` - Migraciones y seeders
- ✅ `public/` - Assets públicos (CSS, JS, imágenes)
- ✅ `resources/` - Vistas Blade, JS, CSS
- ✅ `routes/` - Archivos de rutas
- ✅ `storage/` - Estructura de almacenamiento
- ✅ `bootstrap/` - Archivos de arranque
- ✅ Archivos raíz (artisan, composer.json, etc.)

**NO subirá:**
- ❌ `.env` (configuración local)
- ❌ `vendor/` (se instalará en servidor)
- ❌ `node_modules/` (se instalará en servidor)
- ❌ `.git/` (control de versiones)

---

### Paso 2: Conectarse al servidor SSH

```bash
ssh u556487000@195.35.33.170 -p 65002
# Contraseña: (la de tu panel de Hostinger)
```

---

### Paso 3: Configurar en el servidor

Una vez conectado por SSH:

```bash
# 1. Ir al directorio de la aplicación
cd domains/app.pickntruck.com/public_html

# 2. Verificar que el .env existe y está correcto
ls -la .env
# Si no existe, crearlo desde .env.example y configurarlo

# 3. Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader

# 4. Dar permisos correctos a storage y cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R u556487000:u556487000 storage bootstrap/cache

# 5. Limpiar cachés antiguos
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 6. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Ejecutar migraciones (SOLO si hay nuevas)
php artisan migrate --force

# 8. Instalar dependencias de NPM y compilar assets
npm install --production
npm run build

# 9. Crear enlace simbólico de storage (si no existe)
php artisan storage:link
```

---

### Paso 4: Verificar que todo funciona

1. Abrir navegador: https://app.pickntruck.com
2. Probar login
3. Ir a: https://app.pickntruck.com/profile/document-submission
4. Abrir la consola del navegador (F12)
5. Verificar que aparece: "🔄 Iniciando verificación automática de documentos cada 5 segundos"
6. Probar subir un documento
7. Desde admin, aprobar/rechazar un documento
8. La página debe recargarse automáticamente sin F5

---

## 📋 CHECKLIST DE VERIFICACIÓN

- [ ] Proyecto subido completamente
- [ ] Dependencias de Composer instaladas
- [ ] Permisos de storage correctos
- [ ] Cachés limpiadas
- [ ] Cachés de producción generadas
- [ ] Migraciones ejecutadas (si hay nuevas)
- [ ] Assets compilados con npm run build
- [ ] Enlace simbólico de storage creado
- [ ] Página carga correctamente
- [ ] Login funciona
- [ ] Subida de documentos funciona
- [ ] Auto-refresh funciona cuando admin aprueba/rechaza

---

## ⚠️ IMPORTANTE: Backup del .env

Antes de empezar, asegúrate de tener backup del archivo `.env` del servidor:

```bash
# En el servidor
cd domains/app.pickntruck.com/public_html
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### Error 500:
```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### Assets no cargan:
```bash
npm run build
php artisan route:clear
```

### Rutas no funcionan:
```bash
php artisan route:clear
php artisan route:cache
```

### Vistas no se actualizan:
```bash
php artisan view:clear
php artisan view:cache
```

---

## 📞 SOPORTE

Si algo falla, revisa los logs:
```bash
tail -f storage/logs/laravel.log
```

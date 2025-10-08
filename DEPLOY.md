# Guía de Despliegue - PicknTruck

Este documento proporciona instrucciones para desplegar la aplicación PicknTruck en un entorno de producción.

## Requisitos Previos

- PHP 8.1 o superior
- Composer
- Node.js y NPM
- Git
- Base de datos MySQL/MariaDB
- Servidor web (Apache/Nginx)

## Pasos para el Despliegue

### 1. Configuración Inicial

1. Clonar el repositorio:
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd pickn6
   ```

2. Copiar el archivo de entorno de ejemplo y configurarlo:
   ```bash
   cp .env.example-produccion .env
   nano .env  # Editar con tus configuraciones
   ```

3. Generar la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```

### 2. Ejecutar el Script de Despliegue

Hacer ejecutable el script:
```bash
chmod +x deploy.sh
```

Ejecutar el script de despliegue:
```bash
./deploy.sh
```

### 3. Configuración del Servidor Web

#### Para Nginx:
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/a/tu/proyecto/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. Configuración de Tareas Programadas

Configurar el programador de tareas de tu servidor para ejecutar:

```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## Comandos Útiles

- **Reiniciar la caché de configuración**:
  ```bash
  php artisan config:cache
  ```

- **Limpiar caché de rutas**:
  ```bash
  php artisan route:clear
  ```

- **Reiniciar colas**:
  ```bash
  php artisan queue:restart
  ```

## Solución de Problemas

- Si encuentras errores de permisos:
  ```bash
  sudo chown -R www-data:www-data /ruta/a/tu/proyecto
  sudo chmod -R 755 storage
  sudo chmod -R 755 bootstrap/cache
  ```

- Si las rutas no funcionan correctamente:
  ```bash
  php artisan route:cache
  php artisan config:cache
  ```

## Soporte

Para problemas con el despliegue, contacta al equipo de desarrollo.

#!/bin/bash
echo "Extensiones cargadas:"
php -m

# Limpiar caché para evitar problemas de configuración
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ejecutar migraciones de forma segura
php artisan migrate --force

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

#!/bin/bash
echo "Extensiones cargadas:"
php -m

# Limpiar caché para evitar problemas de configuración
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Forzar rollback y re-ejecución de migraciones
php artisan migrate:rollback --force
php artisan migrate --force

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

#!/bin/bash
echo "Extensiones cargadas:"
php -m
# Opcional: limpiar caché solo si es necesario
# php artisan cache:clear
# php artisan config:clear
# php artisan route:clear
# No ejecutamos migraciones para evitar pérdida de datos
# php scripts/run-migrations.php
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

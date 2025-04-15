#!/bin/bash
echo "Extensiones cargadas:"
php -m
php artisan migrate:reset --force
php scripts/run-migrations.php
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

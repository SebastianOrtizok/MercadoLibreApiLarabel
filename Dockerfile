FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd pdo_pgsql pgsql

# Verifica que pdo_pgsql esté instalado
RUN php -m | grep pdo_pgsql || (echo "pdo_pgsql no está instalado" && exit 1)

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /app

# Copia composer.json y composer.lock primero
COPY composer.json composer.lock ./

# Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-dev --no-plugins --no-scripts

# Copia el resto del proyecto
COPY . .

# Configura permisos
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage
RUN chmod -R 775 storage
RUN chown -R www-data:www-data storage

# Da permisos de ejecución al script de entrada
RUN chmod +x entrypoint.sh

# Expone el puerto
EXPOSE 8000

# Usa el script de entrada
CMD ["./entrypoint.sh"]

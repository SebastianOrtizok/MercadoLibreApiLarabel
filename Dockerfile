FROM php:8.1-cli

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /app

# Copia el proyecto
COPY . .

# Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-dev

# Configura permisos
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage

# Expone el puerto
EXPOSE 8000

# Comando para iniciar
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
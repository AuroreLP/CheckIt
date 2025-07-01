FROM php:8.3.20-fpm-alpine

# Installer les dépendances Alpine + extensions PHP
RUN apk add --no-cache \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

# Installer Composer depuis l’image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier tout le code (pas de volumes en prod)
COPY . .

# Exposer le port obligatoire Render
EXPOSE 10000

# Lancer PHP-FPM en foreground
CMD ["php-fpm", "-F", "--nodaemonize", "-R", "--fpm-config", "/usr/local/etc/php-fpm.conf"]
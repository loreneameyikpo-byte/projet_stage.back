FROM php:8.4-cli

# libzip-dev est nécessaire pour compiler l'extension "zip" de PHP juste
# après — sans elle, spatie/laravel-backup ne peut pas créer d'archives.
# libcurl4-openssl-dev : nécessaire pour l'extension "curl" (utilisée par
# le client Google API pour envoyer les sauvegardes vers Google Drive).

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip curl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier le code de l'application
COPY . .

# Installer les dépendances PHP (sans les paquets de dev, pour la prod)
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
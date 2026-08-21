FROM php:8.4-cli


# libzip-dev : nécessaire pour l'extension "zip" (création des archives
# de sauvegarde par spatie/laravel-backup).
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

# ${PORT:-8000} : utilise la variable PORT si la plateforme d'hébergement
# en assigne une dynamiquement (obligatoire sur Railway), sinon retombe
# sur 8000 par défaut (développement local via docker-compose, ou
# plateformes qui laissent choisir un port fixe comme Render).
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
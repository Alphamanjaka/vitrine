# Dockerfile pour la production sur Railway
# Utilise une approche à un seul conteneur avec Nginx et PHP-FPM.

# 1. Image de base
FROM php:8.2-fpm-bullseye

# Définir le répertoire de travail
WORKDIR /var/www/html

# 2. Installation des dépendances système et des extensions PHP
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP requises pour Laravel (MySQL et PostgreSQL)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copie du code de l'application
COPY . .

# 5. Installation des dépendances PHP pour la production
RUN composer install --optimize-autoloader --no-interaction --no-dev

# 6. Copie des configurations pour Nginx et du script de démarrage
COPY docker/prod/nginx.conf /etc/nginx/sites-available/default
COPY docker/prod/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# 7. Définition des permissions pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Exposition du port (Railway utilisera la variable d'environnement PORT)
EXPOSE 8080

# 9. Commande de démarrage du conteneur
CMD ["/usr/local/bin/start.sh"]

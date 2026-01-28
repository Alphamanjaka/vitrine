#!/bin/sh

# Quitter immédiatement si une commande échoue
set -e

# Optimisations Laravel pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations (sécurisé car --force ne s'exécute qu'en production)
php artisan migrate --force

# Démarrer PHP-FPM en arrière-plan
php-fpm &

# Démarrer Nginx au premier plan pour que le conteneur reste actif
nginx -g 'daemon off;'
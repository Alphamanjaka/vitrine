# #!/bin/sh

# # Quitter immédiatement si une commande échoue
# set -e

# # Optimisations Laravel pour la production
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# # Exécuter les migrations (sécurisé car --force ne s'exécute qu'en production)
# php artisan migrate --force

# # Démarrer PHP-FPM en arrière-plan
# php-fpm &

# # Démarrer Nginx au premier plan pour que le conteneur reste actif
# nginx -g 'daemon off;'


#!/bin/sh
set -e

# Remplace le port 8080 par le port dynamique de Railway dans le fichier nginx.conf
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/conf.d/default.conf

# Optimisations Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations
php artisan migrate --force

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# Démarrer Nginx au premier plan
echo "Démarrage de Nginx sur le port ${PORT:-8080}..."
nginx -g 'daemon off;'

FROM php:8.2-apache

# Installation des extensions nécessaires pour Laravel & MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev zlib1g-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql gd zip bcmath

# Configuration Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY . /var/www/html
WORKDIR /var/www/html

# Droits sur les dossiers de stockage
RUN chown -R www-data:www-data storage bootstrap/cache

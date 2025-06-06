FROM php:8.2-apache

# Installer SQLite et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libsqlite3-dev sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
# Copier entrypoint après avoir copié les fichiers du projet
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Configurer Apache pour permettre les .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

ENTRYPOINT ["/entrypoint.sh"]

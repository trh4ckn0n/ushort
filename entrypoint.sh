#!/bin/bash

# Créer la DB uniquement si elle n'existe pas
if [ ! -f /var/www/html/urls.db ]; then
    php /var/www/html/init_db.php
    chown www-data:www-data /var/www/html/urls.db
fi

exec apache2-foreground

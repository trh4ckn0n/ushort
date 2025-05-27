#!/bin/bash
php /var/www/html/init_db.php
exec apache2-foreground

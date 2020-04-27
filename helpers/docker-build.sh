#!/bin/bash


docker-compose up -d --build


echo "Updating file permissions in server..."
docker-compose exec backend chown -R www-data:www-data /var/www/
docker-compose exec backend find /var/www/ -type f -exec chmod 644 {} \;
docker-compose exec backend find /var/www/ -type d -exec chmod 755 {} \;
echo "File permissions updated in server"

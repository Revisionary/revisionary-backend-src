#!/bin/bash


docker-compose up -d --build


echo "Updating file permissions in server..."
docker-compose exec www chown -R www-data:www-data /var/www/
docker-compose exec www find /var/www/ -type f -exec chmod 644 {} \;
docker-compose exec www find /var/www/ -type d -exec chmod 755 {} \;

#docker-compose exec www chmod -R g+rw /var/www/
#docker-compose exec www chmod -R a=rwx /var/www/
echo "File permissions updated in server"

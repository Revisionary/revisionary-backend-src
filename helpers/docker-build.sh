#!/bin/bash


docker-compose up -d --build


echo "Updating file permissions..."
docker-compose exec www chown -R www-data:www-data /var/www/
#docker-compose exec www chmod -R g+rw /var/www/
docker-compose exec www chmod -R a=rwx /var/www/
echo "File permissions updated"

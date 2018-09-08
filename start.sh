#!/bin/bash


# Install Composer
sh composer-installer.sh


# Install Composer requirements in html/
cd html
rm -f composer.lock
rm -rf vendor
composer install
echo "COMPOSER WORKS DONE"


# Install NodeJs requirements in html/app/bgprocess/
cd app/bgprocess
rm -f package-lock.json
rm -rf node_modules
../../vendor/bin/npm install
echo "NPM WORKS DONE"
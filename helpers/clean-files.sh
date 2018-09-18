#!/bin/bash

echo "Removing Composer files..."
cd www
rm -f composer.lock
rm -rf vendor
echo "Composer files REMOVED"


echo "Removing NPM files..."
cd app/bgprocess
rm -f package-lock.json
rm -rf node_modules
echo "NPM files REMOVED"


echo "TO-DO"
echo "Clean the MySQL files /database/mysql/*"
echo "Clean the sessions /sessions/*"
echo "Clean the logs /logs/*"
echo "Clean the user files /assets/cache/user-1/*"
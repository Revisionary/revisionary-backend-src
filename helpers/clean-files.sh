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


# TO-DO
# Clean the MySQL files /database/mysql/*
# Clean the sessions /sessions/*
# Clean the logs /logs/*
# Clean the user files /assets/cache/user-1/*
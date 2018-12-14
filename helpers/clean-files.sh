#!/bin/bash

echo "Removing Composer files..."
cd www
rm -rf vendor
echo "DONE: Composer files REMOVED"


echo "Removing the MySQL files..."
cd ../database/mysql/
find . \! -name '.gitkeep' -delete
echo "DONE: MySQL files REMOVED"


echo "Removing the Session files..."
cd ../../sessions/
find . \! -name '.gitignore' -delete
echo "DONE: Session files REMOVED"


echo "Removing Node files..."
cd ../chrome/
rm -rf node_modules
echo "DONE: Node files REMOVED"


echo "Removing the Log files..."
cd ../logs/
find . \! -name '.gitignore' -delete
echo "DONE: Log files REMOVED"


echo "Removing the User files..."
cd ../www/cache/users/
rm -rf user-
find ./user-*/ \! -iname '*.png' -delete
echo "DONE: User files REMOVED"


echo "Removing the Project files..."
cd ../projects/
rm -rf project-*
echo "DONE: Project files REMOVED"
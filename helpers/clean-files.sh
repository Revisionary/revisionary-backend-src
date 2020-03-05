#!/bin/bash

(
echo "Removing Composer files..."
cd ./www/
rm -rf vendor
echo "DONE: Composer files REMOVED"
)


(
echo "Removing the MySQL files..."
cd ./database/mysql/
find . \! -name '.gitkeep' -delete
echo "DONE: MySQL files REMOVED"
)


(
echo "Removing the Session files..."
cd ./sessions/
find . \! -name '.gitignore' -delete
echo "DONE: Session files REMOVED"
)


(
echo "Removing Node files..."
cd ./chrome/
rm -rf node_modules
echo "DONE: Node files REMOVED"
)


(
echo "Removing the Apache Log files..."
cd ./logs/
find . \! -name '.gitignore' -delete
echo "DONE: Apache Log files REMOVED"
)


(
echo "Removing the Site Log files..."
cd ./www/app/logs/
find . \! -name '.gitignore' -delete
echo "DONE: Site Log files REMOVED"
)


(
echo "Removing the User files..."
cd ./www/cache/users/
rm -rf user-
find ./user-*/ \! -iname '*.png' -delete
echo "DONE: User files REMOVED"
)


(
echo "Removing the Project files..."
cd ./www/cache/projects/
# rm -rf project-*
find . \! -name '.gitignore' -delete
echo "DONE: Project files REMOVED"
)


(
echo "Removing the Phase files..."
cd ./www/cache/phases/
# rm -rf phase-*
find . \! -name '.gitignore' -delete
echo "DONE: Phase files REMOVED"
)
#!/bin/bash

(
echo "Removing the Session files..."
cd ./sessions/
find . \! -name '.gitignore' -delete
echo "DONE: Session files REMOVED"
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
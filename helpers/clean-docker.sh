#!/bin/bash
# https://docs.docker.com/config/pruning/


docker ps -a
echo "START STOPPING..."
docker stop $(docker ps -aq)
echo "ALL STOPPED"
docker system prune --volumes


docker image prune -a



# https://docs.docker.com/config/pruning/
docker ps -a
echo "START STOPPING..."
docker stop $(docker ps -aq)
echo "ALL STOPPED"
docker system prune --volumes --force


sh clean-files.sh
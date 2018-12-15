#!/bin/bash

read -ep "Which do you want to do? (rebuild | remove-files | prune-docker | reset-all): " CLEAN

if [[ $CLEAN == rebuild ]]; then


	read -ep "Would you like to delete all the files and data as well? (yes | no): " DELETE


	echo "Rebuilding the project..."
	docker-compose down


	if [[ $DELETE == yes ]]; then

		sh helpers/clean-files.sh

	fi


	docker-compose up -d --build
	echo "Project rebuilt."


elif [[ $CLEAN == remove-files ]]; then


	echo "Removing the files..."
	sh helpers/clean-files.sh
	echo "Files are removed."


elif [[ $CLEAN == prune-docker ]]; then


	echo "Pruning the docker..."
	sh helpers/clean-docker.sh
	echo "Docker reset."


elif [[ $CLEAN == reset-all ]]; then


	echo "Resetting all..."
	sh helpers/clean-docker.sh
	sh helpers/clean-files.sh
	echo "All the project and Docker reset."


fi

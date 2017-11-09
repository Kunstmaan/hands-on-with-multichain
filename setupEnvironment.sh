#!/bin/sh

echo "==>> Setup the Docker services"
docker-compose down
docker-compose pull
docker-compose up -d

echo "==>> Installing the dependencies of this code"
docker exec -it bc.php composer install

echo "==>> Wait for 90 seconds while the Multichain is started and all nodes have finished connecting and syncing"
sleep 90

echo "==>> Create the MySQL database tables"
docker exec -it bc.php bin/doctrine orm:schema-tool:create

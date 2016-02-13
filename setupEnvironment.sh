#!/bin/bash

# Installing composer and the dependencies of this code
curl -sS https://getcomposer.org/installer | php
./composer.phar install

# Setup the Docker services
docker-compose down
docker-compose pull
docker-compose up -d

# Wait for 90 seconds while the Multichain is started and all nodes have finished connecting and syncing
sleep 90

# Create the MySQL database tables
bin/doctrine orm:schema-tool:create

clear

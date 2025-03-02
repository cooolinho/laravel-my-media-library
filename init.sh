#!/bin/bash

CONTAINER_NAME="$1"

### docker dependencies
composer install --ignore-platform-reqs

### build and run docker containers
docker-compose build
docker-compose up -d

## Laravel initialization
docker exec -it $CONTAINER_NAME sh -c "sh init.sh"

### restart container
### php entered FATAL state without vendor files, after a restart it works fine
docker restart $CONTAINER_NAME

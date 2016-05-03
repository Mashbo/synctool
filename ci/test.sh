#!/usr/bin/env bash
set -o errexit -o nounset

# Run from CI folder

# Setup a dummy remote server
docker build -t remote ./remote
docker run -d --name remote --publish 2200:22 --volume $(pwd)/remote/sample_files:/files remote
docker exec remote sudo service mysql start

# Setup a dummy local server
docker build -t local ./local
docker run -d --name local --volume $(pwd)/local/files:/files --volume $(pwd)/..:/sync --link remote local
docker exec local sudo service mysql start

# Accept first connection from local to remote
docker exec local ssh -oStrictHostKeyChecking=no root@remote "echo"

# Build command
docker exec local php -d phar.readonly=0 /sync/build.php

docker exec local mkdir /tmp/files
docker exec local /sync/sync.phar pull \
    --remote.host=remote \
    --remote.port=22 \
    --remote.user=root \
    --remote.db.name=remote \
    --remote.db.user=root \
    --remote.db.name=remote \
    --remote.path=/files \
    --local.path=/tmp/files \
    --local.db.name=local \
    --local.db.user=root

# These should return non-zero if it failed
docker exec local bash /tmp/assert-copy-succeeded.sh
echo "Copy succeeded"

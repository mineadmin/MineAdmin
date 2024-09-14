#!/usr/bin/env bash

CURRENT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

until docker exec postgres pg_isready; do
    echo "Waiting for PostgreSQL to start..."
    sleep 5
done

echo -e "Init PostgreSQL database..."
docker exec postgres psql -d postgres -U postgres -c "create database mineadmin"
echo -e "Done\n"

wait
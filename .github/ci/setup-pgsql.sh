#!/usr/bin/env bash

CURRENT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

echo -e "Init PostgreSQL database..."
docker exec postgres psql -d postgres -U postgres -c "create database mineadmin"
echo -e "Done\n"

wait
#!/usr/bin/env bash

CURRENT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
TRAVIS_BUILD_DIR="${TRAVIS_BUILD_DIR:-$(dirname $(dirname $CURRENT_DIR))}"

echo -e "Create MsSQL database..."

docker cp ./.travis/mssql.sql mssql:/opt/
docker exec mssql /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P 'mineadmin123,./' -i "/opt/mssql.sql"
echo -e "Done\n"

wait
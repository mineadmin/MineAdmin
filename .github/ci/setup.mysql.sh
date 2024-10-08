#!/usr/bin/env bash

CURRENT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

MAX_RETRIES=12
RETRY_COUNT=0

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
  if mysql -h 127.0.0.1 -u root -e ";" &> /dev/null; then
    echo -e "MySQL service is now available."
    break
  else
    echo -e "Waiting for MySQL service to start..."
    sleep 5
    ((RETRY_COUNT++))
  fi
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
  echo -e "MySQL service did not start within the time limit."
  exit 1
fi

echo -e "Create MySQL database..."
mysql -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS mineadmin charset=utf8mb4 collate=utf8mb4_unicode_ci;"
echo -e "Done\n"

wait
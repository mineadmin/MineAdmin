#!/usr/bin/env bash

CURRENT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
TRAVIS_BUILD_DIR="${TRAVIS_BUILD_DIR:-$(dirname $(dirname $CURRENT_DIR))}"

echo -e "Create MySQL database..."
mysql -h 127.0.0.1 -u root -proot -e "CREATE DATABASE IF NOT EXISTS mineadmin charset=utf8mb4 collate=utf8mb4_unicode_ci;"

echo -e "Done\n"

wait
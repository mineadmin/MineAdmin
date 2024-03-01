#!/usr/bin/env sh

set -e

set -x

composer install

php ./.travis/run.replace.php
#!/usr/bin/env sh

composer install -vvv

php ./.travis/run.replace.php
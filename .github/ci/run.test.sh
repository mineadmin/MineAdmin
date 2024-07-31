#!/usr/bin/env bash

set -e

./vendor/bin/co-phpunit --group=migrations --prepend tests/bootstrap.php --colors=always
./vendor/bin/co-phpunit --exclude-group=migrations --prepend tests/bootstrap.php --colors=always

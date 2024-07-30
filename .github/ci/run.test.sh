#!/usr/bin/env bash

set -e

./vendor/bin/co-phpunit --prepend tests/bootstrap.php --colors=always
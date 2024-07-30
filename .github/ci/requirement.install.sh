#!/usr/bin/env sh

set -e

set -x

composer install -vvv

composer require pestphp/pest:2.34.9 --dev
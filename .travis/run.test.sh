#!/usr/bin/env bash

set -e
php bin/hyperf.php migrate

php bin/hyperf.php db:seed

php bin/hyperf.php mine:update

./vendor/bin/pest --parallel
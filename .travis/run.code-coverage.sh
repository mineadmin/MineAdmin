#!/usr/bin/env bash

set -e
php bin/hyperf.php migrate --path=app/Setting/Database/Migrations

php bin/hyperf.php migrate --path=app/System/Database/Migrations

php bin/hyperf.php db:seed --path=app/Setting/Database/Seeders

php bin/hyperf.php db:seed --path=app/System/Database/Seeders

php bin/hyperf.php mine:update

composer coverage
#!/usr/bin/env bash

set -e

composer require pestphp/pest --dev --no-cache

./vendor/bin/pest --parallel
#!/usr/bin/env bash

set -e

composer require pestphp/pest --dev

./vendor/bin/pest --parallel
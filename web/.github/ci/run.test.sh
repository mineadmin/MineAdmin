#!/usr/bin/env bash

set -e

php bin/hyperf.php start

pnpm test:e2e
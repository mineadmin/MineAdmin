#!/usr/bin/env sh

set -e

set -x

pnpm install --reporter=debug

pnpm exec playwright install --with-deps
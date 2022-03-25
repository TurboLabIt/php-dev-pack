#!/usr/bin/env bash
SCRIPT_NAME=test_runner

if [ ! -d "vendor" ]; then
  composer install
fi

XDEBUG_MODE=off ${PHP_CLI} ./vendor/bin/phpunit tests

echo ""

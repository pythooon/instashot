#!/bin/bash

set -e

cd /app
composer install --no-interaction --prefer-dist

php -S 0.0.0.0:8000 -t public public/index.php
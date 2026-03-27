#!/bin/bash

set -euo pipefail

cd /app

composer install --no-interaction --prefer-dist

echo "Waiting for PostgreSQL (symfony-db)…"
db_ok=0
for _ in $(seq 1 60); do
    if php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; then
        db_ok=1
        break
    fi
    sleep 2
done

if [[ "$db_ok" != "1" ]]; then
    echo "ERROR: database not reachable within timeout. Check DATABASE_URL and symfony-db."
    exit 1
fi

echo "Running Doctrine migrations…"
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "Running application seed (demo users, tokens, photos)…"
php bin/console app:seed --no-interaction

exec php -S 0.0.0.0:8000 -t public public/index.php

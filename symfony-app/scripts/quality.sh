#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

PHP_CPD_PHAR="${ROOT}/tools/phpcpd.phar"
PHP_CPD_URL="https://phar.phpunit.de/phpcpd.phar"

if [[ ! -f "$PHP_CPD_PHAR" ]]; then
  echo "Downloading phpcpd PHAR (Composer package conflicts with PHPUnit; official PHAR)…" >&2
  mkdir -p "$(dirname "$PHP_CPD_PHAR")"
  curl -fsSL -o "$PHP_CPD_PHAR" "$PHP_CPD_URL"
fi

if [[ ! -x "${ROOT}/vendor/bin/phpstan" ]]; then
  echo "Run composer install first (vendor/bin/phpstan missing)." >&2
  exit 1
fi

./vendor/bin/phpstan analyse --memory-limit=512M
./vendor/bin/phpcs
php -d error_reporting=22527 "$PHP_CPD_PHAR" --min-lines 5 --min-tokens 70 src tests

if [[ -n "${DATABASE_URL:-}" ]]; then
  php bin/console doctrine:migrations:migrate --no-interaction --env=test
else
  echo "Warning: DATABASE_URL unset — smoke tests will fail if they need a database." >&2
fi

./vendor/bin/phpunit

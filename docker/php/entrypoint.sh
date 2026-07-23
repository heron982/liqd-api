#!/bin/sh
set -e

cd /var/www/html

if [ -f composer.json ] && [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chmod -R ug+rwx storage bootstrap/cache || true

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ -f .env ] && ! grep -qE '^APP_KEY=base64:' .env; then
  php artisan key:generate --force --ansi || true
fi

exec "$@"

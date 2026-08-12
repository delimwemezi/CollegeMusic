#!/bin/bash
set -e

echo "==> Starting CollegeMusic Deployment on Railway..."

mkdir -p database storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database database/database.sqlite 2>/dev/null || true

echo "==> Running database migrations..."
php artisan migrate --force --ansi || echo "Migration warning (continuing)..."

php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Application ready. Launching web server on port ${PORT:-8080}..."

if command -v php-fpm >/dev/null 2>&1 && command -v nginx >/dev/null 2>&1; then
    php-fpm -D
    exec nginx -g 'daemon off;'
else
    exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
fi
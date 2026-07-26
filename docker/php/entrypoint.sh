#!/bin/sh
set -eu

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link >/dev/null 2>&1 || true
fi

chown -R www-data:www-data storage bootstrap/cache

if [ "${1:-}" = "php-fpm" ]; then
    exec "$@"
fi

exec su-exec www-data "$@"

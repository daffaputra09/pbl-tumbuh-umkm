#!/bin/sh
set -e

PORT="${PORT:-8080}"
sed -i "s/LISTEN_PORT/${PORT}/" /etc/nginx/conf.d/default.conf

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf

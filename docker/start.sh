#!/bin/sh
set -e

cd /var/www/html

# Cache using the runtime environment Render provides.
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Schema + reference data (states, building types, default tariffs).
# Safe on every boot: seeders are idempotent, and demo records are skipped in production.
php artisan migrate --force
php artisan db:seed --force

# Artisan ran as root; give Apache's user ownership of anything it created.
chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
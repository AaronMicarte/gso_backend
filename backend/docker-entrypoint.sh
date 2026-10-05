#!/bin/bash
set -e

echo "[GSO Entrypoint] Preparing storage and cache directories..."
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear any stale caches baked into the image
# This ensures new routes/config from routes/api.php are picked up on every boot
echo "[GSO Entrypoint] Clearing stale route/config/cache files..."
php artisan route:clear || echo "[GSO Entrypoint] route:clear failed; continuing..."
php artisan config:clear || echo "[GSO Entrypoint] config:clear failed; continuing..."
php artisan cache:clear || echo "[GSO Entrypoint] cache:clear failed; continuing..."

# Rebuild config cache (safe, fast). Do NOT route:cache — we want routes read fresh.
php artisan config:cache || echo "[GSO Entrypoint] config:cache failed; continuing..."

# Run database migrations if DB is configured and AUTO_MIGRATE is true (default: true)
if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "[GSO Entrypoint] Running database migrations..."
    php artisan migrate --force || echo "[GSO Entrypoint] Migration failed or skipped; continuing server startup..."
fi

# Run seeders if AUTO_SEED=true
if [ "${AUTO_SEED:-false}" = "true" ]; then
    echo "[GSO Entrypoint] Running database seeders..."
    php artisan db:seed --force || echo "[GSO Entrypoint] Seeding encountered an error; continuing..."
fi

echo "[GSO Entrypoint] Starting Apache server on port ${PORT:-10000}..."
exec "$@"
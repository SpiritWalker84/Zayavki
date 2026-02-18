#!/bin/sh
set -e
cd /var/www

# Install dependencies when volume mount overwrites vendor (e.g. after git clone)
if [ ! -f vendor/autoload.php ]; then
    echo "vendor missing, running composer install..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    echo "composer install done."
fi

# Ensure APP_KEY is in .env if passed via container environment
if [ -n "$APP_KEY" ] && ! grep -q "^APP_KEY=" .env 2>/dev/null; then
    echo "APP_KEY=$APP_KEY" >> .env
fi

# Ensure .env is readable by www-data
chmod 644 .env 2>/dev/null || true

exec "$@"

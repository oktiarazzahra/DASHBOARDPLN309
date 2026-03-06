#!/bin/sh
set -e

echo "📦 Starting Dashboard PLN 309..."

# Check if database exists
if [ ! -f "/data/database.sqlite" ]; then
    echo "🗄️  Creating SQLite database..."
    touch /data/database.sqlite
    chown www-data:www-data /data/database.sqlite
    chmod 664 /data/database.sqlite
fi

# Check if .env exists
if [ ! -f "/var/www/html/.env" ]; then
    echo "⚙️  Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
    
    # Set database path
    sed -i 's|DB_DATABASE=.*|DB_DATABASE=/var/www/html/storage/database.sqlite|g' /var/www/html/.env
    sed -i 's|APP_ENV=.*|APP_ENV=production|g' /var/www/html/.env
    sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|g' /var/www/html/.env
    sed -i 's|GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=.*|GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=/var/www/html/storage/app/google/service-account.json|g' /var/www/html/.env
fi

# Generate app key if not exists
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Create Google service account directory if not exists
if [ ! -d "/var/www/html/storage/app/google" ]; then
    echo "📁 Creating Google service account directory..."
    mkdir -p /var/www/html/storage/app/google
    chown -R www-data:www-data /var/www/html/storage/app/google
fi

# Decode service account from base64 if provided
if [ -n "$GOOGLE_SERVICE_ACCOUNT_BASE64" ]; then
    echo "🔐 Decoding Google service account from environment variable..."
    echo "$GOOGLE_SERVICE_ACCOUNT_BASE64" | base64 -d > /var/www/html/storage/app/google/service-account.json
    chown www-data:www-data /var/www/html/storage/app/google/service-account.json
    chmod 600 /var/www/html/storage/app/google/service-account.json
fi

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "🧹 Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Application ready!"

# Auto-sync data from Google Sheets on startup (important for Koyeb ephemeral storage)
if [ -n "$AUTO_SYNC_ON_START" ] && [ "$AUTO_SYNC_ON_START" = "true" ]; then
    echo "🔄 Auto-syncing data from Google Sheets..."
    CURRENT_YEAR=$(date +%Y)
    PREV_YEAR=$((CURRENT_YEAR - 1))
    php artisan sync:all --year=$CURRENT_YEAR || true
    php artisan sync:all --year=$PREV_YEAR || true
    php artisan sync:tarif --year=$CURRENT_YEAR || true
    php artisan sync:tarif --year=$PREV_YEAR || true
    php artisan sync:tarif-ulp --year=$CURRENT_YEAR || true
    php artisan sync:tarif-ulp --year=$PREV_YEAR || true
    echo "✅ Data sync complete!"
fi

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

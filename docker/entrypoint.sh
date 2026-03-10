#!/bin/sh

echo "📦 Starting Dashboard PLN 309..."

# Create all required directories first
echo "📁 Creating required directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/google
mkdir -p /var/www/html/bootstrap/cache

# Set permissions FIRST before doing anything else
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Create database directory and file
DB_PATH="/var/www/html/storage/database.sqlite"
if [ ! -f "$DB_PATH" ]; then
    echo "🗄️  Creating SQLite database..."
    touch "$DB_PATH"
fi

# Set database permissions
chmod 666 "$DB_PATH"
chown www-data:www-data "$DB_PATH"

# Check if .env exists
if [ ! -f "/var/www/html/.env" ]; then
    echo "⚙️  Creating .env file..."
    if [ -f "/var/www/html/.env.example" ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        # Create minimal .env if .env.example doesn't exist
        echo "APP_NAME=DashboardPLN309" > /var/www/html/.env
        echo "APP_ENV=production" >> /var/www/html/.env
        echo "APP_DEBUG=true" >> /var/www/html/.env
        echo "APP_URL=https://dashboardpln309.onrender.com" >> /var/www/html/.env
        echo "DB_CONNECTION=sqlite" >> /var/www/html/.env
        echo "DB_DATABASE=$DB_PATH" >> /var/www/html/.env
        echo "GOOGLE_SERVICE_ENABLED=true" >> /var/www/html/.env
        echo "GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=/var/www/html/storage/app/google/service-account.json" >> /var/www/html/.env
    fi
fi

# Set correct values in .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_PATH|g" /var/www/html/.env
sed -i 's|APP_ENV=.*|APP_ENV=production|g' /var/www/html/.env
sed -i 's|APP_DEBUG=.*|APP_DEBUG=true|g' /var/www/html/.env
sed -i 's|GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=.*|GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=/var/www/html/storage/app/google/service-account.json|g' /var/www/html/.env

# Set APP_KEY from env var or generate
if [ -n "$APP_KEY" ]; then
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" /var/www/html/.env
elif ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Set GOOGLE_SPREADSHEET_ID from env var
if [ -n "$GOOGLE_SPREADSHEET_ID" ]; then
    if grep -q "GOOGLE_SPREADSHEET_ID=" /var/www/html/.env; then
        sed -i "s|GOOGLE_SPREADSHEET_ID=.*|GOOGLE_SPREADSHEET_ID=$GOOGLE_SPREADSHEET_ID|g" /var/www/html/.env
    else
        echo "GOOGLE_SPREADSHEET_ID=$GOOGLE_SPREADSHEET_ID" >> /var/www/html/.env
    fi
fi

# Decode service account from base64 if provided
if [ -n "$GOOGLE_SERVICE_ACCOUNT_BASE64" ]; then
    echo "🔐 Decoding Google service account from environment variable..."
    echo "$GOOGLE_SERVICE_ACCOUNT_BASE64" | base64 -d > /var/www/html/storage/app/google/service-account.json
    chmod 644 /var/www/html/storage/app/google/service-account.json
    chown www-data:www-data /var/www/html/storage/app/google/service-account.json
fi

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force || echo "⚠️ Migration warning (may be OK)"

# Clear and cache config
echo "🧹 Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true
# Don't cache routes in production to allow dynamic routes
php artisan config:cache || true

# Fix permissions
echo "🔐 Setting permissions..."
chRe-set permissions after cache generation
echo "🔐 Re-setting permissions after cache..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod 666 "$DB_PATH"

# Auto-sync data from Google Sheets on startup
if [ -n "$AUTO_SYNC_ON_START" ] && [ "$AUTO_SYNC_ON_START" = "true" ]; then
    echo "🔄 Auto-syncing data from Google Sheets..."
    CURRENT_YEAR=$(date +%Y)
    PREV_YEAR=$((CURRENT_YEAR - 1))
    
    # Sync data per ULP (Customer, Power, Revenue)
    echo "📊 Syncing ULP data for $CURRENT_YEAR..."
    php artisan data:auto-sync --year=$CURRENT_YEAR || true
    echo "📊 Syncing ULP data for $PREV_YEAR..."
    php artisan data:auto-sync --year=$PREV_YEAR || true
    
    # Sync data per Tarif
    echo "🏷️  Syncing Tarif data for $CURRENT_YEAR..."
    php artisan sync:tarif --year=$CURRENT_YEAR || true
    echo "🏷️  Syncing Tarif data for $PREV_YEAR..."
    php artisan sync:tarif --year=$PREV_YEAR || true
    
    # Sync data Tarif per ULP
    echo "🏷️  Syncing Tarif-ULP data for $CURRENT_YEAR..."
    php artisan sync:tarif-ulp --year=$CURRENT_YEAR || true
    echo "🏷️  Syncing Tarif-ULP data for $PREV_YEAR..."
    php artisan sync:tarif-ulp --year=$PREV_YEAR || true
    
    echo "✅ Data sync complete!"

    # Set cache flag agar page load pertama tidak sync lagi
    php artisan tinker --execute="Cache::put('last_sync_ulp', now()->timestamp, 300); Cache::put('last_sync_tarif', now()->timestamp, 300);" 2>/dev/null || true
fi

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

@echo off
echo ========================================
echo PLN Dashboard - Setup Tarif Feature
echo ========================================
echo.

echo Step 1: Clearing Laravel cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Done!
echo.

echo Step 2: Running migrations...
php artisan migrate --force
echo Done!
echo.

echo Step 3: Syncing tarif data from Google Sheets...
php artisan sync:tarif --year=2025
echo Done!
echo.

echo Step 4: Checking database...
php check_tarif_tables.php
echo.

echo ========================================
echo Setup complete!
echo.
echo You can now access:
echo - Per ULP Dashboard: http://127.0.0.1:8000/
echo - Per Tarif Dashboard: http://127.0.0.1:8000/tarif
echo ========================================
pause

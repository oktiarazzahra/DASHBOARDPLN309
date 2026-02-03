@echo off
echo ========================================
echo FIXING TARIF DASHBOARD ERROR
echo ========================================
echo.
echo Problem: Table 'tarif_customer_data' doesn't exist
echo Solution: Running migrations...
echo.

cd /d "%~dp0"

echo Step 1: Running migrations...
php artisan migrate --force
echo.

echo Step 2: Syncing tarif data...
php artisan sync:tarif --year=2025
echo.

echo ========================================
echo DONE! Dashboard tarif should work now.
echo Try accessing: http://127.0.0.1:8000/tarif
echo ========================================
echo.
pause

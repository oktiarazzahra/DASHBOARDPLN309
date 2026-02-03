@echo off
echo ========================================
echo   AUTO SYNC WORKER
echo ========================================
echo.
echo Worker ini akan auto-sync data dari Google Sheets
echo setiap 10 detik secara otomatis.
echo.
echo Tekan Ctrl+C untuk stop worker
echo.
echo ========================================
echo.

cd /d "%~dp0"
php artisan sync:worker --interval=10

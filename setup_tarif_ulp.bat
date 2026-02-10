@echo off
echo ========================================
echo PLN Dashboard - Setup Tarif Per ULP
echo ========================================
echo.
echo Fitur baru: Filter data tarif per ULP
echo Sheet yang digunakan:
echo - SEMUA/TARIF B.SEL
echo - SEMUA/TARIF B.UTARA  
echo - SEMUA/TARIF SAMBOJA
echo - SEMUA/TARIF PETUNG
echo - SEMUA/TARIF LONGIKIS
echo - SEMUA/TARIF T.G.
echo.
echo ========================================
echo.
echo Step 1: Running migration...
php artisan migrate --force
echo Done!
echo.
echo Step 2: Syncing tarif per ULP data dari Google Sheets...
echo Ini akan memakan waktu beberapa menit...
php artisan sync:tarif-ulp --year=2025
echo Done!
echo.
echo ========================================
echo Setup selesai!
echo.
echo Sekarang Anda bisa:
echo 1. Akses dashboard: http://127.0.0.1:8000/tarif
echo 2. Filter berdasarkan ULP di dropdown
echo 3. Kombinasi filter ULP + Bulan
echo ========================================
pause

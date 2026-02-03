# Real-Time Auto-Sync Dashboard PLN 309

## 🚀 Fitur Real-Time Sync

Dashboard sekarang memiliki fitur **real-time auto-sync** yang akan:
- ✅ Sync data otomatis setiap 5 menit dari Google Sheets
- ✅ Frontend check update setiap 30 detik
- ✅ Auto-reload dashboard ketika ada data baru
- ✅ Notifikasi toast ketika data diupdate
- ✅ Indicator status sync di navbar

## 📋 Cara Menjalankan

### 1. Jalankan Laravel Scheduler

Scheduler Laravel akan auto-sync data setiap 5 menit. Ada 2 cara menjalankan:

#### **Cara 1: Development (Manual)**
Buka terminal dan jalankan:
```bash
php artisan schedule:work
```
Command ini akan keep running dan execute scheduled tasks.

#### **Cara 2: Production (Windows Task Scheduler)**
Untuk server production Windows, setup Windows Task Scheduler:

1. Buka **Task Scheduler** (tekan Win+R, ketik `taskschd.msc`)
2. Klik **Create Basic Task**
3. Nama: `Laravel Scheduler PLN Dashboard`
4. Trigger: **Daily** at **00:00** (midnight)
5. Action: **Start a program**
   - Program: `C:\PHP\php.exe` (sesuaikan path PHP Anda)
   - Arguments: `artisan schedule:run`
   - Start in: `C:\Users\Oktiara Azzahrah\PROJECTS\DASHBOARDPLN309PERULP`
6. **Edit** task yang dibuat:
   - Triggers tab → Edit → **Repeat task every: 1 minute**
   - Duration: Indefinitely
   - OK

### 2. Jalankan Laravel Server

```bash
php artisan serve
```

### 3. Buka Dashboard

```
http://127.0.0.1:8000
```

Dashboard akan:
- ✅ Check update otomatis setiap 30 detik
- ✅ Tampilkan notifikasi jika ada data baru
- ✅ Auto-reload halaman untuk load data terbaru

## 🔧 Konfigurasi

### Ubah Interval Sync

Edit file `routes/console.php`:
```php
// Ubah dari everyFiveMinutes() ke:
->everyMinute()          // Setiap menit
->everyTenMinutes()      // Setiap 10 menit
->everyFifteenMinutes()  // Setiap 15 menit
->hourly()               // Setiap jam
```

### Ubah Interval Check Frontend

Edit file `resources/views/dashboard/index.blade.php` di bagian JavaScript:
```javascript
// Ubah dari 30000 (30 detik) ke:
setInterval(checkForUpdates, 60000);  // 1 menit
setInterval(checkForUpdates, 10000);  // 10 detik
```

## 🧪 Testing

### Test Manual Sync
Trigger sync manual via API:
```bash
curl -X POST http://127.0.0.1:8000/api/trigger-sync?year=2025
```

### Test Artisan Command
```bash
php artisan data:auto-sync --year=2025
```

### Check Sync Status
```bash
curl http://127.0.0.1:8000/api/sync-status?year=2025
```

## 📊 Monitoring

- **Navbar**: Indicator "Data terkini" (hijau) atau "Memuat data..." (kuning)
- **Toast Notification**: Popup di kanan atas ketika ada update
- **Console Log**: Cek browser console untuk log real-time sync

## 🔍 Troubleshooting

### Scheduler tidak jalan?
```bash
# Cek apakah ada scheduled tasks
php artisan schedule:list

# Test run scheduler sekali
php artisan schedule:run
```

### Data tidak update?
1. Pastikan scheduler running: `php artisan schedule:work`
2. Cek log: `storage/logs/laravel.log`
3. Test manual sync: `php artisan data:auto-sync`

### Frontend tidak auto-refresh?
1. Buka browser console (F12)
2. Cek apakah ada error
3. Pastikan `/api/sync-status` endpoint accessible

## 🎯 Workflow

```
Google Sheets (Data 2025 & 2026)
        ↓
    [Auto-Sync setiap 5 menit]
        ↓
    Laravel Database
        ↓
    [Frontend check setiap 30 detik]
        ↓
    Dashboard Auto-Reload jika ada perubahan
```

## 💡 Tips

1. **Jangan set interval terlalu cepat** - Google Sheets API ada rate limit
2. **Untuk production** - Gunakan Windows Task Scheduler atau cron job
3. **Monitor logs** - Cek `storage/logs/laravel.log` untuk error
4. **Cache clear** - Jika data stuck, run `php artisan cache:clear`

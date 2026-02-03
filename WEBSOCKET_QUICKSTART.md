# 🚀 WebSocket Real-Time Sync - Quick Start

Dashboard PLN 309 sekarang menggunakan **WebSocket** untuk sync **INSTANT**!

## ⚡ Cara Menjalankan

### 1. Install Laravel Reverb (Sekali saja)
```bash
composer require laravel/reverb
```

### 2. Start Reverb WebSocket Server (Terminal 1)
```bash
php artisan reverb:start
```
Output:
```
Starting Reverb server on 127.0.0.1:8080
Server running...
```

### 3. Start Laravel Scheduler (Terminal 2)
```bash
php artisan schedule:work
```
Ini akan auto-sync data setiap 5 menit.

### 4. Start Laravel Server (Terminal 3)
```bash
php artisan serve
```

### 5. Buka Dashboard
```
http://127.0.0.1:8000
```

## 🔥 Cara Kerja

1. **Google Sheets diubah** → Tunggu max 5 menit (scheduler sync)
2. **Data di-sync** → Backend broadcast event via WebSocket
3. **Dashboard terima event INSTANT** → Auto-reload dalam 1 detik!
4. **Tidak perlu refresh manual!**

## 🎯 Indicator Status

- **🟢 Live** = WebSocket connected, siap terima update
- **🟡 Reconnecting...** = Mencoba reconnect
- **🟡 Memuat data...** = Sedang reload data baru

## ⚡ Test Real-Time

### Manual Sync (untuk test instant update):
```bash
php artisan data:auto-sync --year=2025
```

Begitu command selesai:
- Dashboard langsung dapat notifikasi
- Toast popup "Update: X records (all)"
- Auto-reload dalam 1 detik!

## 📊 Monitoring

1. **Buka browser console (F12)**
2. Lihat log:
   ```
   🚀 Initializing WebSocket connection...
   ✅ WebSocket connected!
   📡 Subscribed to dashboard-updates channel
   🔥 Real-time WebSocket sync ACTIVE - instant updates!
   ```
3. **Ketika ada update:**
   ```
   📥 Data update received: {dataType: "all", year: 2025, updateCount: 144}
   ```

## 🔧 Konfigurasi

### Ubah Interval Sync Backend
Edit `routes/console.php`:
```php
// Ubah dari everyFiveMinutes() ke:
->everyMinute()      // Setiap 1 menit (sangat cepat!)
->everyTwoMinutes()  // Setiap 2 menit
->everyThreeMinutes() // Setiap 3 menit
```

### Ubah Delay Reload
Edit `resources/views/dashboard/index.blade.php`:
```javascript
// Ubah dari 1000 (1 detik) ke:
setTimeout(() => {
    window.location.reload();
}, 500);  // 0.5 detik (lebih cepat!)
```

## 🆚 Perbandingan dengan Polling

| Method | Update Speed | Server Load | Network Traffic |
|--------|-------------|-------------|-----------------|
| **WebSocket** | **INSTANT** | Low | Minimal |
| Polling 30s | 0-30 detik | High | Banyak |
| Polling 10s | 0-10 detik | Very High | Sangat Banyak |

## 🐛 Troubleshooting

### WebSocket tidak connect?

1. **Cek Reverb server running:**
   ```bash
   # Jika error "Address already in use", kill process:
   netstat -ano | findstr :8080
   taskkill /PID <PID> /F
   ```

2. **Restart Reverb:**
   ```bash
   php artisan reverb:start
   ```

3. **Cek browser console untuk error**

### Dashboard tidak auto-reload?

1. Pastikan Reverb server running
2. Buka console, pastikan ada log "✅ WebSocket connected!"
3. Test manual sync: `php artisan data:auto-sync`

### Scheduler tidak jalan?

```bash
# Cek scheduled tasks
php artisan schedule:list

# Manual run
php artisan schedule:run
```

## 💡 Tips Production

### Windows Server
Gunakan **NSSM** (Non-Sucking Service Manager) untuk run Reverb sebagai Windows Service:

```bash
# Download NSSM dari nssm.cc
nssm install ReverbServer
# Program: C:\PHP\php.exe
# Arguments: artisan reverb:start
# Startup directory: C:\path\to\DASHBOARDPLN309PERULP
```

### Linux Server
Gunakan **Supervisor**:

```ini
[program:reverb]
command=php /path/to/artisan reverb:start
autostart=true
autorestart=true
user=www-data
```

## 🎉 Kesimpulan

Dengan WebSocket:
- ✅ Update **INSTANT** tanpa delay
- ✅ Tidak perlu polling yang boros resource
- ✅ User experience lebih baik
- ✅ Server load lebih rendah
- ✅ Real-time notification dan auto-reload

**Selamat! Dashboard sekarang full real-time! 🚀**

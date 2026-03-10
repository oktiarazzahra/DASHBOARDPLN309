# Auto-Sync Setup untuk Dashboard PLN 309

## Cara Kerja Auto-Sync

Dashboard ini sudah dikonfigurasi untuk **otomatis sync data dari Google Sheets setiap 10 menit** secara background.

### Komponen Auto-Sync

1. **Laravel Scheduler** (`routes/console.php`)
   - Menjalankan command sync otomatis setiap 10 menit
   - Sync data untuk tahun 2025 dan 2026
   - Sync 3 jenis data:
     - Data Per ULP (Customer, Power, Revenue)
     - Data Per Tarif
     - Data Tarif Per ULP

2. **Background Worker** (`docker/supervisord.conf`)
   - Laravel scheduler berjalan sebagai background process
   - Dijalankan oleh supervisord bersama nginx dan php-fpm
   - Auto-restart jika crash

3. **Commands yang Dijalankan**
   - `php artisan data:auto-sync --year=2025` (setiap 10 menit)
   - `php artisan data:auto-sync --year=2026` (setiap 10 menit)
   - `php artisan sync:tarif --year=2025` (setiap 10 menit)
   - `php artisan sync:tarif --year=2026` (setiap 10 menit)
   - `php artisan sync:tarif-ulp --year=2025` (setiap 10 menit)
   - `php artisan sync:tarif-ulp --year=2026` (setiap 10 menit)

## Timeline Auto-Sync

```
Deploy → Server Start → Sleep 30s → Scheduler Start
         ↓
         Manual Sync (entrypoint.sh)
         ↓
         Database Populated
         ↓
         Web Server Ready
         ↓
         Scheduler Running (background)
         ↓
         Auto-sync setiap 10 menit
```

## Kapan Data Akan Update?

| Kondisi | Update Otomatis? | Waktu |
|---------|------------------|-------|
| Deploy ulang | ✅ Ya | Langsung saat startup |
| Server tidur (15 menit idle) | ❌ Tidak | - |
| Server bangun dari tidur | ✅ Ya* | Dalam 10 menit |
| Ada perubahan di Google Sheets | ✅ Ya | Maksimal 10 menit |
| User buka halaman | ✅ Ya (data dari DB) | Instant |

*Scheduler akan auto-restart saat server bangun dan melanjutkan schedule

## Mengubah Interval Sync

Edit file `routes/console.php`:

```php
// Ubah dari everyTenMinutes() ke:
->everyFiveMinutes()   // Setiap 5 menit
->everyFifteenMinutes() // Setiap 15 menit
->everyThirtyMinutes()  // Setiap 30 menit
->hourly()              // Setiap jam
```

⚠️ **Catatan**: Interval terlalu pendek (< 5 menit) dapat membebani Google Sheets API.

## Monitoring Auto-Sync

### Cek Log Scheduler

Di Render dashboard, buka "Logs" dan cari:
```
Syncing customer data...
✓ Customer data synced
Syncing power data...
✓ Power data synced
Syncing revenue data...
✓ Revenue data synced
```

### Cek Status Scheduler di Container

```bash
# SSH ke container Render (jika ada akses)
ps aux | grep schedule:work
```

## Troubleshooting

### Scheduler Tidak Berjalan

1. Cek log supervisord di Render dashboard
2. Pastikan file `docker/supervisord.conf` memiliki program `[program:scheduler]`
3. Restart service di Render

### Data Tidak Update

1. Cek log sync di Render dashboard
2. Pastikan Google Sheets API credentials valid
3. Cek quota Google Sheets API

### Sync Terlalu Lambat

1. Kurangi interval sync (misal dari 10 menit ke 15 menit)
2. Atau hapus sync untuk tahun yang tidak digunakan

## Keuntungan Auto-Sync

✅ Data selalu update otomatis tanpa perlu deploy ulang
✅ User tidak perlu klik tombol sync manual
✅ Halaman dashboard load cepat (baca dari database)
✅ Data konsisten di semua halaman
✅ Tidak bergantung pada user untuk trigger sync

## Limitasi di Render Free Tier

⚠️ Server tidur setelah 15 menit idle
⚠️ Saat tidur, scheduler berhenti
✅ Saat bangun, scheduler auto-restart dan sync dalam 10 menit

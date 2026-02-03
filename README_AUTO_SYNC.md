# AUTO SYNC - Cara Kerja

## 🔄 Sistem Auto-Sync Real-Time

Dashboard ini menggunakan **2 layer sync** untuk memastikan data selalu update:

### Layer 1: Background Worker (Recommended)
**File:** `start_auto_sync.bat`

Worker ini berjalan di background dan **otomatis sync data dari Google Sheets ke Database** setiap 10 detik.

**Cara Jalankan:**
1. Double-click `start_auto_sync.bat` 
2. Biarkan window tetap terbuka
3. Data akan auto-sync setiap 10 detik
4. Tekan Ctrl+C untuk stop

**Kelebihan:**
- ✅ Data di database selalu fresh (max 10 detik delay)
- ✅ Tidak perlu manual sync
- ✅ Berjalan independent dari web server
- ✅ Update Per ULP dan Per Tarif bersamaan

### Layer 2: Browser Auto-Reload
**File:** Sudah built-in di halaman dashboard

Browser akan **auto-detect perubahan di database** dan reload halaman.

**Cara Kerja:**
- Setiap 5 detik browser cek: "Ada update di database?"
- Jika ada perubahan → Auto reload halaman
- User langsung lihat data terbaru

---

## 🚀 Setup Complete

### Untuk Jalankan Dashboard:

**Terminal 1 - Web Server:**
```bash
php artisan serve
```

**Terminal 2 - Auto Sync Worker:**
```bash
start_auto_sync.bat
```

### Workflow:
1. Anda edit data di Google Sheets
2. Worker auto-sync ke database (max 10 detik)
3. Browser detect perubahan di database (max 5 detik)
4. Dashboard auto-reload dengan data baru

**Total delay maksimal: ~15 detik**

---

## ⚙️ Kustomisasi

### Ubah Interval Sync (jika mau lebih cepat):

Edit `start_auto_sync.bat`:
```bat
php artisan sync:worker --interval=5
```
(sync setiap 5 detik)

### Manual Sync (jika worker tidak jalan):
```bash
php sync_ulp_manual.php    # Sync Per ULP
php sync_tarif_manual.php  # Sync Per Tarif
```

---

## 📊 Monitoring

Lihat log sync di window `start_auto_sync.bat`:
```
[08:30:15] Syncing data...
  ✓ Per ULP synced
  ✓ Per Tarif synced
  Completed in 1250ms

[08:30:25] Syncing data...
  ✓ Per ULP synced
  ✓ Per Tarif synced
  Completed in 1180ms
```

---

## ❓ Troubleshooting

### Data tidak update?
1. Pastikan `start_auto_sync.bat` tetap berjalan
2. Check log error di window worker
3. Manual sync: `php sync_ulp_manual.php`

### Worker error?
1. Check file `storage/app/google/service-account.json` ada
2. Check internet connection
3. Check Google Sheets permissions

---

**Dibuat oleh: GitHub Copilot**
**Tanggal: 3 Februari 2026**

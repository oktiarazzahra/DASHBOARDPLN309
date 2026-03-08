# 🎯 SOLUSI: Grafik Per ULP Kosong (Data Tarif Ada)

## ✅ MASALAH SUDAH DITEMUKAN DAN DIPERBAIKI!

**Root Cause:** 
Di file `entrypoint.sh`, command yang salah dipanggil:
- ❌ `sync:all` (TIDAK ADA command ini) → Gagal sync data ULP
- ✅ `data:auto-sync` (Command yang BENAR) → Butuh diperbaiki

**Dampak:**
- ✅ Data Tarif muncul (karena `sync:tarif` berhasil)
- ❌ Grafik per ULP kosong (karena `sync:all` gagal, data customer/power/revenue tidak masuk)

**Fix:** 
Sudah diperbaiki dan di-push ke GitHub. Command sekarang:
```bash
php artisan data:auto-sync --year=2025  # ← BENAR untuk sync data ULP
php artisan sync:tarif --year=2025      # ← Untuk sync data tarif
```

---

## 🚀 CARA MEMPERBAIKI (2 PILIHAN)

### ✅ PILIHAN 1: Tunggu Render Auto-Redeploy (Paling Mudah)

1. **Render akan otomatis redeploy** dalam 2-3 menit (karena ada push ke GitHub)
2. **Buka Render Dashboard**: https://dashboard.render.com
3. **Pilih service** "dashboard-pln-309"
4. **Lihat tab "Events"** → tunggu status jadi **"Live"** (dot hijau)
5. **Buka tab "Logs"** → cari baris:
   ```
   📊 Syncing ULP data for 2025...
   📊 Syncing ULP data for 2026...
   ✅ Data sync complete!
   ```
6. **Refresh dashboard** → Grafik per ULP akan muncul! 🎉

**Waktu:** 3-5 menit (deploy + sync)

---

### ✅ PILIHAN 2: Manual Sync SEKARANG (Tercepat, Tidak Perlu Tunggu)

**Langsung sync data tanpa tunggu redeploy:**

1. **Buka Render Dashboard**: https://dashboard.render.com
2. **Pilih service** "dashboard-pln-309"
3. **Klik tab "Shell"** di sidebar kiri
4. **Klik tombol "Connect"** (tunggu shell terbuka, ada tulisan `/var/www/html #`)
5. **Copy-paste command ini** satu per satu:

   ```bash
   php artisan data:auto-sync --year=2025
   ```
   
   **Tunggu sampai selesai** (1-2 menit). Output yang bagus:
   ```
   Starting auto-sync for year 2025...
   Syncing customer data...
   ✓ Customer data synced: XXX records
   Syncing power data...
   ✓ Power data synced: XXX records
   Syncing revenue data...
   ✓ Revenue data synced: XXX records
   ✓ All data synced successfully
   ```

6. **Sync tahun 2026 juga** (opsional):
   ```bash
   php artisan data:auto-sync --year=2026
   ```

7. **Tutup shell** (ketik `exit` atau close tab)

8. **Buka dashboard** → Refresh (F5) → **Grafik per ULP muncul!** 🎉

**Waktu:** 2-3 menit

---

## 🔍 Cara Cek Apakah Sudah Berhasil

### Di Dashboard (https://dashboardpln309.onrender.com):

**✅ SUKSES jika:**
- **Total Pelanggan** > 0 (sebelumnya 0)
- **Grafik "Distribusi Pelanggan"** muncul (ada bar chart)
- **Grafik "Distribusi Daya Tersambung"** muncul (ada bar chart)
- **Table per ULP** muncul (ada data BALIKPAPAN SELATAN, BALIKPAPAN UTARA, dll)

**❌ GAGAL jika:**
- Semua masih 0
- Grafik kosong (hanya label "0K" di sumbu y)
- Table kosong

---

## 🆘 Jika Manual Sync Error

**Kemungkinan error dan solusi:**

### Error 1: "The caller does not have permission"
```
Google_Service_Exception: The caller does not have permission
```
**Solusi:** Google Sheets belum di-share ke service account.
→ Buka Google Sheets → Share → Tambahkan: `dashboardpln@dashboardpln309.iam.gserviceaccount.com`

---

### Error 2: "Requested entity was not found"
```
Google_Service_Exception: Requested entity was not found
```
**Solusi:** GOOGLE_SPREADSHEET_ID salah atau sheet untuk tahun tersebut tidak ada.
→ Cek apakah di Google Sheets ada sheet bernama "DATA PENGUSAHAAN 2025" atau "DATA PENGUSAHAAN"

---

### Error 3: "Call to a member function ... on null"
```
Error: Call to a member function getValueRanges() on null
```
**Solusi:** Environment variable tidak lengkap atau salah.
→ Cek GOOGLE_SPREADSHEET_ID dan GOOGLE_SERVICE_ACCOUNT_BASE64 di Render Environment

---

### Error 4: "No data found in spreadsheet"
**Solusi:** Google Sheets kosong atau format salah.
→ Pastikan ada data di sheet untuk tahun yang di-sync

---

## 📊 Command Reference (Untuk Manual Sync)

| Command | Fungsi |
|---------|--------|
| `php artisan data:auto-sync --year=2025` | Sync data ULP (Customer, Power, Revenue) tahun 2025 |
| `php artisan data:auto-sync --year=2026` | Sync data ULP (Customer, Power, Revenue) tahun 2026 |
| `php artisan sync:tarif --year=2025` | Sync data Tarif tahun 2025 |
| `php artisan sync:tarif --year=2026` | Sync data Tarif tahun 2026 |
| `php artisan sync:tarif-ulp --year=2025` | Sync data Tarif per ULP tahun 2025 |

**Untuk sync semua sekaligus:**
```bash
php artisan data:auto-sync --year=2025
php artisan data:auto-sync --year=2026
php artisan sync:tarif --year=2025
php artisan sync:tarif --year=2026
php artisan sync:tarif-ulp --year=2025
php artisan sync:tarif-ulp --year=2026
```

---

## 🎯 Kesimpulan

**Masalah:** Command salah di entrypoint.sh (`sync:all` yang tidak ada)
**Solusi:** Sudah diperbaiki jadi `data:auto-sync` (command yang benar)
**Action:** Pilih salah satu:
1. Tunggu redeploy otomatis (3-5 menit) → lebih mudah
2. Manual sync dari Shell (2-3 menit) → lebih cepat

**Setelah itu, grafik per ULP akan muncul!** 🎉

---

**Jika masih ada masalah, screenshot error dan tanya saya!**

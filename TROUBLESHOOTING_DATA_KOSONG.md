# 🔍 TROUBLESHOOTING: Data Masih Kosong Setelah Set Environment

## ✅ Checklist Cepat

Ikuti langkah-langkah ini secara berurutan:

---

## 1️⃣ CEK: Apakah Deploy Sudah Selesai?

**Di Render Dashboard:**
1. Buka https://dashboard.render.com
2. Pilih service **dashboard-pln-309**
3. Lihat status di bagian atas:
   - ✅ **"Live"** dengan dot hijau = Deploy selesai
   - 🟡 **"Deploying"** = Masih dalam proses, tunggu selesai
   - ❌ **"Build failed"** atau **"Deploy failed"** = Ada error

**Jika masih "Deploying":** Tunggu sampai selesai (biasanya 3-5 menit), lalu lanjut ke step berikutnya.

**Jika "Failed":** Klik tab **"Logs"** untuk lihat error, screenshot dan tanya saya.

---

## 2️⃣ CEK: Apakah Environment Variables Sudah Benar?

**Di Render Dashboard → Tab "Environment", pastikan semua ini ada:**

| Variable | Harus Ada | Contoh Value |
|----------|-----------|--------------|
| `APP_KEY` | ✅ | `base64:KxW1pKqbOsCcey5vppSQTk0bOvI+yCWdxQkXLobbw1o=` |
| `APP_URL` | ✅ | `https://dashboardpln309.onrender.com` |
| `GOOGLE_SPREADSHEET_ID` | ✅ | `1KDkcPM3lT6lR1D5Cv9YPeYNPTu7DVLaBt4m7Guk5vZU` |
| `GOOGLE_SERVICE_ACCOUNT_BASE64` | ✅ | `ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCI...` (panjang) |

**❌ JANGAN ADA INI** (sudah default):
- ~~`DB_CONNECTION`~~ (sudah di render.yaml)
- ~~`DB_DATABASE`~~ (sudah di render.yaml)
- ~~`APP_ENV`~~ (sudah di render.yaml)

**Jika ada yang salah:**
1. Klik ✏️ (edit) di sebelah environment variable
2. Perbaiki value-nya
3. Klik "Save Changes"
4. Tunggu redeploy selesai

---

## 3️⃣ CEK PENTING: Apakah Google Sheets Sudah Di-Share ke Service Account?

**INI YANG PALING SERING LUPA!**

### Cara Cek dan Fix:

1. **Buka Google Sheets Anda** (spreadsheet yang berisi data PLN)
2. **Klik tombol "Share"** di pojok kanan atas
3. **Lihat apakah email ini sudah ada di daftar:**
   ```
   dashboardpln@dashboardpln309.iam.gserviceaccount.com
   ```

4. **Jika BELUM ADA:**
   - Klik "Add people and groups"
   - Paste email: `dashboardpln@dashboardpln309.iam.gserviceaccount.com`
   - Set akses: **Viewer** (atau Editor jika perlu)
   - **UNCHECK** ✅ "Notify people" (biar gak kirim email)
   - Klik **"Share"** atau **"Send"**

5. **Setelah di-share:**
   - Tunggu 1-2 menit
   - Kembali ke dashboard Render
   - Klik tombol **"Sync"** di dashboard
   - Tunggu beberapa menit
   - Refresh halaman

---

## 4️⃣ CEK: Apakah Auto-Sync Berjalan?

**Di Render Dashboard → Tab "Logs":**

Scroll ke bawah, cari baris seperti ini:

```
🔄 Auto-syncing data from Google Sheets...
✅ Data sync complete!
```

### Kemungkinan yang Anda lihat:

#### ✅ BAGUS - Jika Ada Log Seperti Ini:
```
📦 Starting Dashboard PLN 309...
🗄️  Creating SQLite database...
⚙️  Creating .env file...
🔐 Decoding Google service account from environment variable...
🔄 Running database migrations...
✅ Application ready!
🔄 Auto-syncing data from Google Sheets...
✅ Data sync complete!
```
→ **Sync sudah berjalan!** Refresh dashboard Anda, data seharusnya muncul.

---

#### ❌ MASALAH - Jika Ada Error Seperti Ini:

**Error 1: "Permission denied" atau "403 Forbidden"**
```
Google_Service_Exception: The caller does not have permission
```
→ **SOLUSI**: Google Sheets belum di-share ke service account. Lihat Step 3 di atas.

---

**Error 2: "File not found" atau "Invalid spreadsheet ID"**
```
Google_Service_Exception: Requested entity was not found
```
→ **SOLUSI**: GOOGLE_SPREADSHEET_ID salah. Cek lagi:
1. Buka Google Sheets Anda
2. Lihat URL: `https://docs.google.com/spreadsheets/d/`**`1KDkcPM3...`**`/edit`
3. Copy ID antara `/d/` dan `/edit`
4. Paste di Render Environment → `GOOGLE_SPREADSHEET_ID`

---

**Error 3: "Invalid service account" atau "Invalid credentials"**
```
invalid_grant: Invalid JWT
```
→ **SOLUSI**: GOOGLE_SERVICE_ACCOUNT_BASE64 salah atau ada spasi/newline.
1. Buka file `render_secret_base64.txt`
2. Copy **SELURUH isi** (1 baris panjang, tanpa spasi atau enter tambahan)
3. Paste ulang di Render Environment → `GOOGLE_SERVICE_ACCOUNT_BASE64`
4. Pastikan tidak ada spasi di awal/akhir

---

**Error 4: "No application encryption key"**
```
RuntimeException: No application encryption key has been specified
```
→ **SOLUSI**: APP_KEY belum di-set atau salah.
- Pastikan value: `base64:KxW1pKqbOsCcey5vppSQTk0bOvI+yCWdxQkXLobbw1o=`
- Harus dimulai dengan `base64:`

---

#### ⚠️ TIDAK ADA LOG SYNC

Jika tidak ada log tentang sync sama sekali:
1. Environment variable `AUTO_SYNC_ON_START` cek di render.yaml apakah `true`
2. Atau coba manual sync dari dashboard (klik tombol "Sync")

---

## 5️⃣ MANUAL SYNC (Jika Auto-Sync Gagal)

**Dari Dashboard:**
1. Buka `https://dashboardpln309.onrender.com`
2. Klik tombol **"🔄 Sync"** di navbar kanan atas
3. Tunggu 30 detik - 2 menit
4. **Lihat console browser** (F12 → Console tab):
   - ✅ Sukses: `Sync completed successfully!`
   - ❌ Error: Screenshot error dan tanya saya

5. **Refresh halaman** (F5)

---

## 6️⃣ CEK: Apakah Data Benar-Benar Ada di Google Sheets?

**Buka Google Sheets Anda**, pastikan:

1. **Ada sheet bernama** (case-sensitive):
   - `DATA PENGUSAHAAN 2025` atau `DATA PENGUSAHAAN` untuk tahun 2025
   - `DATA PENGUSAHAAN 2026` untuk tahun 2026

2. **Ada kolom tahun 2025** di sheet tersebut (header baris pertama)

3. **Ada data di dalam sheet** (bukan kosong)

**Jika sheet atau data tidak ada:**
→ Sync tidak akan menghasilkan data. Isi dulu Google Sheets dengan data yang benar.

---

## 7️⃣ CEK DATABASE (Advanced)

**Jika semua di atas sudah benar tapi masih kosong:**

### Cara Cek Database via Render Shell:

1. **Di Render Dashboard → Service Anda → Tab "Shell"**
2. Klik **"Connect"**
3. Jalankan command ini:
   ```bash
   php artisan tinker
   ```
4. Di dalam tinker, jalankan:
   ```php
   \App\Models\Customer::count();
   \App\Models\Revenue::count();
   \App\Models\Power::count();
   ```
5. Lihat hasilnya:
   - Jika `0` semua → Data belum masuk, sync gagal
   - Jika ada angka → Data sudah masuk, tapi query di controller ada masalah

6. Keluar dari tinker:
   ```php
   exit
   ```

---

## 🚀 QUICK FIX: Force Manual Sync via Shell

**Jika ingin force sync langsung dari Render:**

1. **Di Render Dashboard → Service Anda → Tab "Shell"**
2. Klik **"Connect"**
3. Jalankan command ini:
   ```bash
   php artisan sync:all --year=2025
   php artisan sync:all --year=2026
   php artisan sync:tarif --year=2025
   php artisan sync:tarif --year=2026
   ```
4. Lihat output:
   - ✅ Sukses: `✓ All data synced successfully`
   - ❌ Error: Screenshot dan tanya saya

5. **Setelah selesai, refresh dashboard**

---

## 📊 Kemungkinan Masalah & Solusi (Summary)

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| Data kosong (0) | Google Sheets belum di-share | Share ke `dashboardpln@dashboardpln309.iam.gserviceaccount.com` |
| Error 403/Permission | Service account tidak punya akses | Share Google Sheets dengan akses Viewer/Editor |
| Error "File not found" | GOOGLE_SPREADSHEET_ID salah | Cek ulang ID dari URL Google Sheets |
| Error "Invalid JWT/credentials" | GOOGLE_SERVICE_ACCOUNT_BASE64 salah | Copy ulang dari `render_secret_base64.txt`, pastikan tidak ada spasi |
| Error "No encryption key" | APP_KEY belum di-set | Set APP_KEY dengan value yang benar |
| Sync tidak jalan | Deploy belum selesai | Tunggu deploy selesai, cek status "Live" |
| Sync tidak jalan | AUTO_SYNC_ON_START tidak aktif | Coba manual sync dari dashboard atau shell |

---

## 🆘 Jika Masih Gagal

**Screenshot ini dan kirim ke saya:**
1. Render Dashboard → Tab **"Logs"** (scroll ke bawah, screenshot seluruh log)
2. Render Dashboard → Tab **"Environment"** (screenshot semua environment variables, blur value yang sensitive)
3. Browser Console (F12 → Console tab) saat klik tombol "Sync"
4. Google Sheets permissions (screenshot daftar orang yang punya akses)

---

**Setelah ikuti semua step di atas, data seharusnya muncul! 🎉**

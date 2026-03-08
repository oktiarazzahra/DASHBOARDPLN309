# 🔍 DEBUG: Sync Berhasil Tapi Data Tidak Muncul

## 🚨 LANGKAH CEK SEGERA:

### 1️⃣ **Cek Apakah Data Benar-Benar Masuk Database**

**Buka Render Dashboard** → Service → Tab **"Shell"** → **"Connect"**

Jalankan command ini:

```bash
php artisan tinker
```

**Cek jumlah data tahun 2026:**

```php
\App\Models\CustomerData::where('year', 2026)->count();
```

**Lihat data terakhir yang diupdate:**

```php
\App\Models\CustomerData::where('year', 2026)->latest('updated_at')->take(5)->get();
```

**Cek data per ULP tertentu (ganti 'BS' dengan ULP code Anda):**

```php
\App\Models\CustomerData::where('year', 2026)->where('ulp_code', 'BS')->get();
```

**Keluar dari tinker:**

```php
exit
```

---

### 2️⃣ **Interpretasi Hasil:**

#### ✅ **Jika count() > 0 dan updated_at timestamp baru:**
→ **Data SUDAH masuk database**, masalahnya **cache browser**

**Solusi:**
1. Tekan `Ctrl + Shift + Delete`
2. Clear "Cached images and files" → "All time"
3. Clear data
4. Tutup browser SEPENUHNYA
5. Buka lagi → Buka dashboard

---

#### ❌ **Jika count() = 0 atau updated_at timestamp lama:**
→ **Data TIDAK masuk**, sync sebenarnya gagal (meskipun notifikasi berhasil)

**Lanjut ke STEP 3**

---

### 3️⃣ **Manual Sync Dengan Lihat Log Detail**

Masih di Shell, jalankan:

```bash
php artisan data:auto-sync --year=2026
```

**PENTING: Screenshot SELURUH output!**

**Output yang BAGUS:**
```
Starting auto-sync for year 2026...
Syncing customer data...
✓ Customer data synced: 144 records    ← Ada angka > 0
Syncing power data...
✓ Power data synced: 144 records
Syncing revenue data...
✓ Revenue data synced: 144 records
✓ All data synced successfully
```

**Output yang BERMASALAH:**
```
✓ Customer data synced: 0 records      ← MASALAH! Tidak ada yang di-sync
```

atau

```
Error: ... (ada error message)
```

**Screenshot output dan kirim ke saya!**

---

### 4️⃣ **Cek Format Google Sheets**

Kemungkinan data di-skip karena format salah. **Buka Google Sheets Anda:**

#### ✅ **Checklist Format yang BENAR:**

**Sheet: JUMLAH PELANGGAN PER ULP**

```
Row X:   [kosong atau title "DATA PENGUSAHAAN 2026"]
Row Y:   BULANAN                                    ← WAJIB ada
Row Y+1: ULP Code | ULP Name | JAN | FEB | ... | DEC
Row Y+2: BS       | BALIKPAPAN SELATAN | 123 | 456 | ...
Row Y+3: BU       | BALIKPAPAN UTARA   | 789 | ...  | ...
```

**Yang WAJIB:**
- ✅ Ada kata "BULANAN" (exact, huruf besar)
- ✅ Ada angka tahun "2026" di atas atau dekat section BULANAN
- ✅ Kolom A = ULP Code (contoh: BS, BU, LK, PT, dll)
- ✅ Kolom B = ULP Name (contoh: BALIKPAPAN SELATAN)
- ✅ Kolom C-N = Data bulan JAN-DEC

**Yang BOLEH Kosong:**
- ✅ Bulan yang belum ada data (kolom boleh kosong atau angka 0)
- ✅ Tidak semua bulan harus diisi

**Yang TIDAK BOLEH:**
- ❌ ULP Code atau ULP Name kosong (row akan di-skip)
- ❌ SEMUA bulan kosong untuk 1 ULP (row akan di-skip)
- ❌ Format angka salah (contoh: "12.345" dengan separator ribuan → SALAH)

---

### 5️⃣ **Format Angka yang BENAR**

**Di Google Sheets, angka PELANGGAN harus dalam format ribuan:**

| Yang Anda Lihat | Yang Ditulis | Hasil di Database |
|----------------|--------------|-------------------|
| 471.820 pelanggan | `471,82` | 471.820 ✅ |
| 100.500 pelanggan | `100,5` | 100.500 ✅ |
| 50.000 pelanggan | `50` | 50.000 ✅ |
| 123 pelanggan | `0,123` | 123 ✅ |

**PENTING:**
- ✅ **Gunakan KOMA (,) untuk desimal** di Google Sheets
- ✅ Angka dalam **RIBUAN** (471,82 = 471.820)
- ❌ **JANGAN** gunakan titik separator (12.345 → SALAH)
- ❌ **JANGAN** tulis angka penuh dengan separator (471.820 → SALAH, tulis 471,82)

**Contoh:**
```
Jika pelanggan = 50.000 orang
→ Tulis di Google Sheets: 50 atau 50,0
→ Bukan: 50.000 atau 50,000
```

---

## 🎯 TROUBLESHOOTING BY SYMPTOM:

### **Symptom 1: Sync berhasil tapi count() = 0**

**Penyebab:** Format Google Sheets salah, data di-skip semua.

**Solusi:**
1. Cek ada kata "BULANAN" dan tahun "2026"
2. Cek kolom ULP Code dan Name tidak kosong
3. Cek format angka (koma untuk desimal, bukan titik)
4. **Screenshot Google Sheets** (section BULANAN untuk 2026) dan kirim ke saya

---

### **Symptom 2: Sync berhasil, count() > 0, tapi angka tidak sesuai**

**Penyebab:** Format angka salah di Google Sheets.

**Solusi:**
1. Di tinker, cek angka yang masuk:
   ```php
   \App\Models\CustomerData::where('year', 2026)->where('ulp_code', 'BS')->where('month', 'JAN')->first()->customer_count;
   ```
2. Bandingkan dengan angka di Google Sheets
3. Jika beda, format angka salah → perbaiki format (lihat Step 5)

---

### **Symptom 3: Sync berhasil, count() > 0, updated_at baru, tapi web tidak update**

**Penyebab:** 100% cache browser.

**Solusi:**
1. Clear cache COMPLETELY (Ctrl + Shift + Delete → All time)
2. Tutup browser sepenuhnya
3. Buka lagi
4. Atau coba **Incognito Mode** (`Ctrl + Shift + N`)
5. Atau coba **browser lain** (Chrome/Edge/Firefox)

---

## 📸 SCREENSHOT YANG SAYA BUTUHKAN:

Jika masih bermasalah, screenshot dan kirim:

1. ✅ **Output dari tinker** (count, latest data)
2. ✅ **Output dari manual sync** (`php artisan data:auto-sync --year=2026`)
3. ✅ **Google Sheets** - section BULANAN untuk data 2026 (dengan header)
4. ✅ **Dashboard saat ini** - tampilan grafik yang masih kosong
5. ✅ **Browser Console** (F12 → Console tab, ada error?)

---

## 🚀 QUICK FIX STEPS:

```bash
# STEP 1: Cek database
php artisan tinker
\App\Models\CustomerData::where('year', 2026)->count();
\App\Models\CustomerData::where('year', 2026)->latest('updated_at')->first();
exit

# STEP 2: Jika count = 0, manual sync + lihat log
php artisan data:auto-sync --year=2026

# STEP 3: Cek lagi database
php artisan tinker
\App\Models\CustomerData::where('year', 2026)->count();
exit

# STEP 4: Jika count > 0, refresh dashboard dengan hard refresh
# Ctrl + Shift + R di browser
```

---

## ⚡ LANGKAH PALING PENTING:

**LAKUKAN INI SEKARANG:**

1. Buka Render Shell
2. Ketik:
   ```bash
   php artisan tinker
   \App\Models\CustomerData::where('year', 2026)->count();
   ```
3. **Screenshot angka yang muncul** (penting!)
4. Kirim screenshot ke saya

**Dari angka ini saya bisa tahu:**
- Jika 0 → Data tidak masuk, masalah di sync atau format sheets
- Jika > 0 → Data sudah masuk, masalah di cache browser

---

**Tunggu hasil dari tinker dulu sebelum troubleshoot lebih lanjut!** 🎯

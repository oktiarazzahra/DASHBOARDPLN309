# 🔧 SOLUSI: Debug Data TANPA Shell Access (GRATIS!)

## 🎯 LANGKAH CEPAT:

### 1️⃣ Buka Halaman Debug
Akses URL ini di browser:
```
https://dashboardpln309perulp.onrender.com/debug-data
```

Atau dari dashboard, tambahkan `/debug-data` di akhir URL.

---

## 📊 Apa yang Ditampilkan Halaman Debug?

✅ **Jumlah Data di Database** - Lihat berapa banyak record Customer, Power, Revenue untuk setiap tahun

✅ **Data Per ULP** - Lihat ULP mana saja yang punya data

✅ **Preview 10 Data Terbaru** - Lihat sample data yang baru masuk

✅ **Environment Check** - Cek apakah Google Sheets sudah terkoneksi

✅ **Database File Status** - Cek apakah database bisa dibaca/ditulis

---

## 🔍 CARA DIAGNOSA MASALAH:

### JIKA DATA COUNT = 0 (KOSONG):
**Artinya:** Data belum masuk ke database sama sekali

**Penyebab:**
1. ❌ Google Sheets format salah
2. ❌ Belum klik tombol Sync
3. ❌ Sync gagal tapi tidak ada error message

**Solusi:**
1. Cek Google Sheets harus ada kata **"BULANAN"** di atas tabel
2. Cek ada tahun **"2026"** di dekat kata BULANAN
3. Format kolom harus: `Kode ULP | Nama ULP | JAN | FEB | MAR ...`
4. Angka dalam ribu: 50 = 50,000 pelanggan (bukan 50.000)
5. Klik tombol **Sync** di dashboard
6. Tunggu notifikasi **"Sync berhasil"**
7. Refresh halaman debug (F5)

---

### JIKA DATA COUNT > 0 (ADA DATA):
**Artinya:** Data sudah masuk ke database ✅

**Tapi grafik tidak muncul?**

**Penyebab:**
- 🔥 **Browser cache!** (masalah paling sering)

**Solusi:**
1. **Hard refresh:** Tekan `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)
2. **Clear cache:**
   - Tekan `Ctrl + Shift + Delete`
   - Pilih "All time" / "Semua waktu"
   - Centang "Cached images and files"
   - Klik "Clear data"
3. **Tutup browser sepenuhnya**, buka lagi
4. **Coba Incognito mode:** `Ctrl + Shift + N`

---

## 📝 WORKFLOW UPDATE DATA YANG BENAR:

```
1. Edit Google Sheets
   ↓
2. Buka dashboard di browser
   ↓
3. Klik tombol "Sync"
   ↓
4. Tunggu notifikasi "Sync berhasil"
   ↓
5. Buka /debug-data untuk cek jumlah data
   ↓
6. Jika ada data, CLEAR CACHE browser
   ↓
7. Refresh dashboard
   ↓
8. Data muncul! ✅
```

---

## 🚀 TIPS PENTING:

### Cek Data Masuk atau Tidak:
```
1. Buka: https://dashboardpln309perulp.onrender.com/debug-data
2. Lihat angka COUNT:
   - COUNT = 0 → Data belum masuk (cek Google Sheets format)
   - COUNT > 0 → Data sudah masuk (clear browser cache)
```

### Setelah Edit Google Sheets:
```
1. Klik Sync di dashboard
2. Buka /debug-data
3. Cek apakah COUNT naik
4. Jika naik → clear cache
5. Jika tidak naik → cek format Google Sheets
```

### Test Browser Cache Issue:
```
1. Buka dashboard normal
2. Catat COUNT di /debug-data (misal: 120)
3. Edit Google Sheets, tambah data
4. Klik Sync
5. Buka /debug-data lagi, cek COUNT (misal jadi: 132)
6. Jika COUNT naik tapi grafik tidak update → CLEAR CACHE!
```

---

## 📸 Screenshot Debug Page:

Halaman debug akan menampilkan:

```
📊 Jumlah Data (Tahun 2026)
┌─────────────────┬─────────────────┬─────────────────┐
│  📈 Customer    │  ⚡ Power       │  💰 Revenue     │
│     1,200       │     1,200       │     1,200       │
└─────────────────┴─────────────────┴─────────────────┘

🏢 Data Customer Per ULP
┌──────────┬─────────────────────┬────────────────┐
│ ULP Code │ ULP Name            │ Jumlah Record  │
├──────────┼─────────────────────┼────────────────┤
│ 309010   │ ULP JEMBRANA        │ 12 records     │
│ 309020   │ ULP NEGARA          │ 12 records     │
│ ...      │ ...                 │ ...            │
└──────────┴─────────────────────┴────────────────┘
```

Jika melihat angka seperti ini, **DATA SUDAH MASUK!** ✅
→ Tinggal clear browser cache saja.

---

## ⚠️ KESALAHAN UMUM:

### ❌ "Saya sudah Sync tapi COUNT tetap 0"
**Solusi:** Format Google Sheets salah. Cek:
- Ada kata "BULANAN"?
- Ada tahun "2026"?
- Kolom sesuai urutan?

### ❌ "COUNT ada angka tapi grafik kosong"
**Solusi:** Browser cache! Hard refresh atau Incognito mode.

### ❌ "Data tadi ada, sekarang hilang"
**Solusi:** Filter tahun salah. Cek dropdown tahun di dashboard.

---

## 🎯 KESIMPULAN:

**TANPA SHELL ACCESS, PAKAI INI:**
```
https://dashboardpln309perulp.onrender.com/debug-data
```

**Halaman ini adalah pengganti `php artisan tinker`!**

Semua informasi yang diperlukan untuk troubleshooting ada di sini:
- Berapa banyak data ✅
- ULP mana yang ada data ✅
- Sample data terbaru ✅
- Environment sudah benar atau belum ✅

**Tidak perlu Shell access yang berbayar!** 🎉

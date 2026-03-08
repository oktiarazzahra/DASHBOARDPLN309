# 🔄 WORKFLOW: Cara Update Data dari Google Sheets ke Dashboard

## ⚠️ PENTING: Ini Yang Harus Anda Lakukan!

Dashboard **TIDAK** langsung otomatis update ketika Anda ganti angka di Google Sheets.

**WORKFLOW YANG BENAR:**

```
1. Buka Google Sheets → Ganti angka
         ↓
2. Buka Dashboard → Klik tombol "🔄 Sync"
         ↓
3. Tunggu notifikasi "✓ Sync berhasil!"
         ↓
4. Dashboard akan AUTO-RELOAD (refresh otomatis)
         ↓
5. Angka baru muncul! ✅
```

---

## 📝 STEP-BY-STEP LENGKAP:

### **STEP 1: Edit Data di Google Sheets**

1. Buka Google Sheets Anda
2. **Edit angka** yang ingin diganti (misal: Januari 2026 dari 100 jadi 200)
3. **Tekan Enter** atau klik cell lain (pastikan data tersimpan)
4. **Tunggu 1-2 detik** (pastikan Google Sheets auto-save selesai)

---

### **STEP 2: Sync Data ke Dashboard**

1. **Buka dashboard**: `https://dashboardpln309.onrender.com`
2. **Klik tombol "🔄 Sync"** di pojok kanan atas navbar (sebelah selector tahun)
3. **Tunggu notifikasi:**
   - Muncul: "Menyinkronkan data 2026..."
   - Loading spinner muncul
4. **Tunggu sampai selesai** (30 detik - 2 menit tergantung berapa banyak data)
5. Muncul: **"✓ Sync berhasil! Memuat ulang..."**
6. Dashboard akan **AUTO-RELOAD** (refresh otomatis)

---

### **STEP 3: Cek Hasilnya**

Setelah auto-reload:
- ✅ Angka sudah berubah sesuai Google Sheets
- ✅ Grafik sudah update
- ✅ Total sudah update

**Jika masih angka lama:**
- Tekan `Ctrl + Shift + R` (hard refresh)
- Atau clear cache browser

---

## 🚀 FITUR AUTO-REFRESH (Sudah Ada!)

Dashboard sudah punya fitur **auto-polling setiap 5 detik** untuk cek data baru.

**Cara kerja:**
1. Setiap 5 detik, dashboard cek apakah ada data baru di server
2. Jika ada perubahan, muncul notifikasi: **"📊 Data baru tersedia!"**
3. Klik notifikasi atau refresh manual

**TAPI:** Fitur ini hanya jalan **SETELAH Anda klik Sync** di dashboard!

---

## ❌ KESALAHAN UMUM:

### **Kesalahan 1: Langsung Refresh Dashboard Tanpa Sync**
```
Ganti di Google Sheets → Refresh dashboard → ❌ Angka tidak berubah
```
**Kenapa?** Data di Google Sheets tidak otomatis masuk database. **Harus sync dulu!**

---

### **Kesalahan 2: Tunggu Lama Tanpa Klik Sync**
```
Ganti di Google Sheets → Tunggu 10 menit → Refresh dashboard → ❌ Angka tidak berubah
```
**Kenapa?** Tidak ada auto-sync dari Google Sheets. **Harus klik tombol Sync!**

---

### **Kesalahan 3: Sync Tapi Tidak Tunggu Sampai Selesai**
```
Klik Sync → Langsung refresh sebelum notifikasi "✓ Sync berhasil!" → ❌ Angka belum update
```
**Kenapa?** Sync butuh waktu (30s-2 menit). **Tunggu sampai muncul notifikasi sukses!**

---

## ⏱️ BERAPA LAMA SYNC?

| Tahun | Jumlah Data | Estimasi Waktu |
|-------|-------------|----------------|
| 2025 (Full data 12 bulan) | ~1,500 records | 1-2 menit |
| 2026 (Sebagian data) | ~500 records | 30-60 detik |
| Tarif data | ~200 records | 30 detik |

**Total waktu sync (semua):** 2-3 menit

---

## 🔧 TROUBLESHOOTING:

### **Masalah 1: Sudah Sync, Sudah Tunggu, Tapi Angka Masih Lama**

**Solusi:**
1. **Hard refresh:** `Ctrl + Shift + R`
2. **Tekan F12** → Tab "Console" → Lihat apakah ada error
3. **Cek di Render Shell:**
   ```bash
   php artisan tinker
   \App\Models\CustomerData::where('year', 2026)->latest('updated_at')->first();
   ```
   Lihat apakah `updated_at` timestamp baru dan `customer_count` sesuai angka di Google Sheets

---

### **Masalah 2: Tombol Sync Loading Terus, Tidak Selesai**

**Solusi:**
1. **Refresh halaman** (F5)
2. **Coba lagi klik Sync**
3. **Jika masih gagal:**
   - Buka **Render Dashboard** → Shell
   - Manual sync: `php artisan data:auto-sync --year=2026`

---

### **Masalah 3: Notifikasi "Sync gagal, coba lagi"**

**Penyebab:**
- Service Render sedang restart
- Google Sheets tidak bisa diakses
- Environment variable salah

**Solusi:**
1. **Tunggu 1-2 menit**, coba lagi
2. **Cek Render Logs** (Dashboard → Service → Logs) untuk lihat error
3. **Manual sync dari Shell** (lebih reliable)

---

## 💡 TIPS AGAR DATA CEPAT UPDATE:

### **1. Edit Data di Google Sheets Sekaligus**
Jangan:
```
Edit angka → Sync → Edit lagi → Sync → Edit lagi → Sync
```

Lebih baik:
```
Edit SEMUA angka yang perlu diubah → Sync SEKALI
```

---

### **2. Gunakan Manual Sync dari Render Shell (Lebih Cepat)**

**Jika sering edit data, pakai cara ini:**

1. **Render Dashboard** → Service → Tab **"Shell"** → **"Connect"** (keep window open)
2. **Edit data di Google Sheets**
3. **Balik ke Shell**, ketik:
   ```bash
   php artisan data:auto-sync --year=2026
   ```
4. **Tunggu sampai selesai**
5. **Refresh dashboard**

**Keuntungan:** Lebih cepat, bisa lihat log detail, lebih reliable.

---

### **3. Keep Dashboard Open dengan Auto-Polling**

**Tips:**
1. Buka dashboard, biarkan tetap open
2. Edit di Google Sheets
3. Sync dari Shell atau klik tombol Sync
4. **Auto-polling akan detect** perubahan dan kasih notifikasi
5. Klik notifikasi atau refresh

---

## 🎯 KESIMPULAN:

**TIDAK ADA** auto-sync otomatis dari Google Sheets ke Dashboard.

**Yang WAJIB dilakukan:**
1. ✅ Edit data di Google Sheets
2. ✅ **KLIK TOMBOL SYNC** di dashboard (atau manual sync dari Shell)
3. ✅ Tunggu sampai selesai ("✓ Sync berhasil!")
4. ✅ Dashboard auto-reload atau hard refresh (`Ctrl + Shift + R`)

**Setelah sync, fitur auto-polling akan aktif dan kasih notifikasi jika ada perubahan data baru.**

---

## 📞 Jika Masih Bingung:

**Workflow paling sederhana:**
```
1. Ganti angka di Google Sheets
2. Klik tombol "🔄 Sync" di dashboard
3. Tunggu notifikasi "✓ Sync berhasil!"
4. Angka otomatis update (auto-reload)
```

**Jika angka tidak update setelah auto-reload:**
- Tekan `Ctrl + Shift + R` (hard refresh)
- Atau clear browser cache

**Jika masih tidak update:**
- Screenshot hasil sync (notifikasi sukses/gagal)
- Kirim ke saya untuk troubleshoot

---

**Sekarang coba workflow di atas, dan lihat apakah data sudah update!** 🚀

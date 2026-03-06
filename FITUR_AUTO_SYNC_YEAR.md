# 🔄 Fitur Auto-Sync Saat Ganti Tahun

## Deskripsi
Ketika user mengubah tahun di dropdown (misalnya dari 2025 ke 2026 atau sebaliknya), sistem akan **otomatis melakukan sinkronisasi data dari Google Spreadsheet** untuk tahun yang dipilih.

---

## 🎯 Cara Kerja

### 1. **User Memilih Tahun**
- User memilih tahun di dropdown (2025, 2026, dll)
- Sistem mendeteksi perubahan tahun

### 2. **Auto-Sync dari Spreadsheet**
Sistem otomatis melakukan sync untuk:
- ✅ **Data Per ULP** (Customer, Power, Revenue)
- ✅ **Data Per Tarif** (Kategori S, R, B, I, P, T, C, L)
- ✅ **Data Tarif per ULP** (Kombinasi ULP & Tarif)

### 3. **Loading Indicator**
- Dashboard Utama: Toast notification di pojok kanan atas
- Dashboard Tarif: Loading overlay full screen
- Menampilkan progress: "Sinkronisasi data tahun XXXX..."

### 4. **Redirect ke Dashboard**
Setelah sync selesai, otomatis redirect ke dashboard dengan tahun yang dipilih

---

## 📍 Implementasi

### Dashboard Utama (Per ULP)
**File:** `resources/views/dashboard/index.blade.php`

Fungsi `changeYear()` akan:
1. Tampilkan loading toast
2. Panggil API `/api/trigger-sync` untuk data ULP
3. Panggil API `/api/tarif/trigger-sync` untuk data tarif
4. Tunggu kedua sync selesai (parallel)
5. Redirect ke `/?year=XXXX`

### Dashboard Tarif
**File:** `resources/views/tarif/index.blade.php`

Fungsi `changeYear()` akan:
1. Tampilkan loading overlay
2. Panggil API `/api/tarif/trigger-sync`
3. Tunggu sync selesai
4. Redirect ke `/tarif?year=XXXX&month=X&ulp=X`

---

## 🔌 API Endpoints

### 1. Sync Data ULP
**Endpoint:** `POST /api/trigger-sync`

**Body:**
```json
{
  "year": 2026
}
```

**Response:**
```json
{
  "success": true,
  "message": "ULP data sync triggered successfully",
  "year": 2026
}
```

**Command yang dipanggil:**
```bash
php artisan data:auto-sync --year=2026
```

---

### 2. Sync Data Tarif + Tarif ULP
**Endpoint:** `POST /api/tarif/trigger-sync`

**Body:**
```json
{
  "year": 2026
}
```

**Response:**
```json
{
  "success": true,
  "message": "Tarif and Tarif ULP sync triggered successfully",
  "year": 2026
}
```

**Commands yang dipanggil:**
```bash
php artisan sync:tarif --year=2026
php artisan sync:tarif-ulp --year=2026
```

---

## 🎨 User Experience

### Skenario 1: Sync Berhasil
1. User klik tahun 2026
2. Muncul notifikasi: "Sinkronisasi data tahun 2026..."
3. Progress: "Mengambil data dari Google Spreadsheet"
4. Setelah selesai: "✓ Data berhasil di-sync!"
5. Auto redirect & tampilkan data tahun 2026

### Skenario 2: Sync Gagal (Fallback)
1. User klik tahun 2026
2. Muncul notifikasi: "Sinkronisasi data tahun 2026..."
3. Jika gagal: "Sync gagal, menggunakan data cache"
4. Tetap redirect (pakai data lama di database jika ada)

---

## ⚙️ Backend Controller

### SyncStatusController (Data ULP)
**File:** `app/Http/Controllers/Api/SyncStatusController.php`

Method `triggerSync()`:
- Terima parameter `year`
- Panggil `Artisan::call('data:auto-sync', ['--year' => $year])`
- Return JSON response

### TarifSyncStatusController (Data Tarif)
**File:** `app/Http/Controllers/Api/TarifSyncStatusController.php`

Method `triggerSync()`:
- Terima parameter `year`
- Panggil `Artisan::call('sync:tarif', ['--year' => $year])`
- Panggil `Artisan::call('sync:tarif-ulp', ['--year' => $year])`
- Return JSON response

---

## 📋 Checklist Implementasi

- ✅ Update `changeYear()` di dashboard utama
- ✅ Update `changeYear()` di dashboard tarif
- ✅ API endpoint untuk sync ULP data
- ✅ API endpoint untuk sync tarif data
- ✅ Command `data:auto-sync` mendukung `--year`
- ✅ Command `sync:tarif` mendukung `--year`
- ✅ Command `sync:tarif-ulp` mendukung `--year`
- ✅ Loading indicator (toast & overlay)
- ✅ Error handling & fallback
- ✅ CSRF token protection

---

## 🧪 Testing

### Test Manual:
1. Buka dashboard: http://127.0.0.1:8000
2. Pilih tahun berbeda di dropdown
3. Perhatikan:
   - Loading muncul
   - Sync berjalan
   - Data ter-update
   - Redirect otomatis

### Test Console:
```bash
# Test sync manual
php artisan data:auto-sync --year=2026
php artisan sync:tarif --year=2026
php artisan sync:tarif-ulp --year=2026
```

---

## 🎯 Benefit

1. **User tidak perlu manual sync** - Cukup pilih tahun
2. **Data selalu fresh** - Langsung dari spreadsheet
3. **User experience smooth** - Loading feedback jelas
4. **Error handling baik** - Fallback ke data cache
5. **Parallel sync** - Data ULP & Tarif sync bersamaan (lebih cepat)

---

## 📝 Notes

- Sync dilakukan **on-demand** (saat user ganti tahun)
- Jika ingin **real-time sync** tetap jalan, gunakan `start_auto_sync.bat`
- Data tahun lama tetap tersimpan di database (tidak di-replace)
- Sync hanya update data untuk tahun yang dipilih

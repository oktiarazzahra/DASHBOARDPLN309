# Fitur Filter Per ULP di Dashboard Tarif

## Overview
Fitur ini menambahkan kemampuan untuk memfilter data tarif berdasarkan ULP (Unit Layanan Pelanggan) selain filter bulan yang sudah ada sebelumnya.

## Perubahan yang Dilakukan

### 1. Database Migration
File: `database/migrations/2026_02_06_000001_add_ulp_code_to_tarif_tables.php`

Menambahkan kolom baru ke tabel tarif:
- `ulp_code` - Kode ULP (B.SEL, B.UTARA, SAMBOJA, dll)
- `ulp_name` - Nama lengkap ULP

**Tabel yang diupdate:**
- `tarif_customer_data`
- `tarif_power_data`
- `tarif_revenue_data`

**Unique constraint baru:**
- Customer: `[tarif_code, ulp_code, year, month]`
- Power: `[tarif_code, ulp_code, year, month]`
- Revenue: `[tarif_code, ulp_code, year, month, data_type]`

### 2. Service Baru
File: `app/Services/TarifUlpSheetsService.php`

Service untuk membaca data dari sheet-sheet baru di Google Sheets:
- SEMUA/TARIF B.SEL
- SEMUA/TARIF B.UTARA
- SEMUA/TARIF SAMBOJA
- SEMUA/TARIF PETUNG
- SEMUA/TARIF LONGIKIS
- SEMUA/TARIF T.G.

Setiap sheet berisi 4 section data (kiri ke kanan):
1. **JUMLAH PELANGGAN** (Kolom A-M)
2. **DAYA TERSAMBUNG** (Kolom N-Z)
3. **KWH JUAL** (Kolom AA-AM)
4. **RP PENDAPATAN** (Kolom AN-AZ)

### 3. Controller Update
File: `app/Http/Controllers/TarifDashboardController.php`

Perubahan:
- Tambah parameter `$ulp` untuk menerima filter ULP dari request
- Query data ULP untuk dropdown filter
- Tambah kondisi `when()` di setiap query untuk filter berdasarkan ULP
- Pass variable `$ulp` dan `$ulpList` ke view

### 4. View Update
File: `resources/views/tarif/index.blade.php`

Perubahan UI:
- Tambah dropdown "Filter ULP" di sebelah filter bulan
- Layout filter sekarang horizontal dengan label yang jelas
- JavaScript diupdate untuk handle perubahan filter ULP
- Kombinasi filter: ULP + Bulan + Tahun

### 5. Artisan Command
File: `app/Console/Commands/SyncTarifUlpData.php`

Command baru untuk sync data per ULP:
```bash
php artisan sync:tarif-ulp --year=2025
```

Proses:
1. Hapus data lama dengan `ulp_code` untuk tahun tersebut
2. Fetch data dari 6 sheet ULP
3. Insert ke database dengan batch insert (100 records/batch)
4. Tampilkan summary jumlah records yang di-sync

## Cara Penggunaan

### Setup Awal

1. **Jalankan migration:**
   ```bash
   php artisan migrate
   ```

2. **Sync data dari Google Sheets:**
   ```bash
   php artisan sync:tarif-ulp --year=2025
   ```
   
   Atau gunakan script otomatis:
   ```bash
   setup_tarif_ulp.bat
   ```
   
   Atau manual PHP script:
   ```bash
   php sync_tarif_ulp_manual.php
   ```

### Menggunakan Dashboard

1. Buka http://127.0.0.1:8000/tarif

2. Gunakan filter:
   - **Filter ULP**: Pilih ULP tertentu atau "Semua ULP"
   - **Filter Bulan**: Pilih bulan tertentu atau "Semua Bulan"
   - **Tahun**: Saat ini hanya 2025

3. Kombinasi filter:
   - Semua ULP + Semua Bulan = Total keseluruhan
   - ULP tertentu + Semua Bulan = Total untuk ULP tersebut sepanjang tahun
   - Semua ULP + Bulan tertentu = Total semua ULP di bulan tertentu
   - ULP tertentu + Bulan tertentu = Data spesifik ULP di bulan tertentu

## Struktur Data di Google Sheets

Setiap sheet ULP (contoh: SEMUA/TARIF B.SEL) memiliki struktur:

```
Row 1-2: Title/Header area
Row 3: Section header (JUMLAH PELANGGAN 2025...)
Row 4: BULANAN | JAN | FEB | MAR | ... | DEC
Row 5+: Data per tarif

Kolom layout (per section):
- Column 0: Nama Tarif
- Column 1-12: Data JAN-DEC
```

4 section horizontal:
1. A-M: Pelanggan
2. N-Z: Daya Tersambung (VA)
3. AA-AM: kWh Jual
4. AN-AZ: Rp Pendapatan

## Mapping ULP

```php
'B.SEL' => 'BALIKPAPAN SELATAN'
'B.UTARA' => 'BALIKPAPAN UTARA'
'SAMBOJA' => 'SAMBOJA'
'PETUNG' => 'PETUNG'
'LONGIKIS' => 'LONGIKIS'
'T.G.' => 'TENGGARONG'
```

## Troubleshooting

### Data tidak muncul setelah sync
1. Cek apakah migration sudah dijalankan
2. Pastikan kolom `ulp_code` sudah ada di tabel
3. Cek log Laravel di `storage/logs/laravel.log`

### Error saat sync
- Pastikan sheet names di Google Sheets sesuai dengan mapping
- Cek service account credentials
- Verify sheet permissions

### Filter tidak bekerja
- Clear browser cache
- Pastikan JavaScript tidak error (lihat console browser)
- Cek apakah URL parameters ter-pass dengan benar

## Files yang Dibuat/Dimodifikasi

**Baru:**
- `database/migrations/2026_02_06_000001_add_ulp_code_to_tarif_tables.php`
- `app/Services/TarifUlpSheetsService.php`
- `app/Console/Commands/SyncTarifUlpData.php`
- `setup_tarif_ulp.bat`
- `sync_tarif_ulp_manual.php`
- `FITUR_FILTER_ULP.md` (file ini)

**Dimodifikasi:**
- `app/Http/Controllers/TarifDashboardController.php`
- `resources/views/tarif/index.blade.php`

## Catatan Penting

1. **Data Lama vs Baru**: 
   - Data lama (tanpa ULP) tetap ada di database
   - Data baru (dengan ULP) disimpan terpisah
   - Query di controller meng-aggregate keduanya

2. **Performance**:
   - Sync 6 ULP × 4 data types × 70 tarif × 12 bulan = ~20,000 records
   - Waktu sync: 2-5 menit tergantung koneksi internet
   - Batch insert (100 records) untuk efisiensi

3. **Struktur Tarif**:
   - Tarif yang sama di setiap ULP (konsisten)
   - Kategori: S, R, B, I, P, T, C, L
   - Total ~70 tarif berbeda

## Next Steps (Opsional)

1. Tambah filter kategori tarif (S, R, B, dll)
2. Export data per ULP ke Excel
3. Chart comparison antar ULP
4. Real-time sync indicator untuk data per ULP

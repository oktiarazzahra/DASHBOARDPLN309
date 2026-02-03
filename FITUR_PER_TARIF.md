# Dashboard PLN 309 - Fitur Per Tarif

## Overview
Dashboard PLN 309 sekarang memiliki 2 mode tampilan:
1. **Per ULP** - Dashboard berdasarkan lokasi geografis (6 ULP)
2. **309 Per Tarif** - Dashboard berdasarkan kategori tarif pelanggan (~70 kategori)

## Struktur Database

### Tabel Baru untuk Data Tarif:

#### 1. tarif_customer_data
- `tarif_code` - Kode tarif (contoh: S1/220VA, R1/900VA)
- `tarif_name` - Nama lengkap tarif
- `tarif_category` - Kategori utama (S, R, B, I, P, T, C, L)
- `year` - Tahun data
- `month` - Bulan (0-11)
- `month_name` - Nama bulan (JAN-DEC)
- `total_customers` - Jumlah pelanggan

#### 2. tarif_power_data
- `tarif_code` - Kode tarif
- `tarif_name` - Nama lengkap tarif
- `tarif_category` - Kategori utama
- `year` - Tahun data
- `month` - Bulan (0-11)
- `month_name` - Nama bulan
- `total_power` - Total daya (VA)

#### 3. tarif_revenue_data
- `tarif_code` - Kode tarif
- `tarif_name` - Nama lengkap tarif
- `tarif_category` - Kategori utama
- `year` - Tahun data
- `month` - Bulan (0-11)
- `month_name` - Nama bulan
- `data_type` - Tipe data ('kwh' atau 'rp')
- `value` - Nilai (kWh atau Rupiah)

## Service Classes

### 1. TarifCustomerSheetsService
- Mengambil data pelanggan dari sheet PELANGGAN/TARIF
- Parse ~70 kategori tarif
- Skip rows continuation (II, III) dan subtotal (JUMLAH)

### 2. TarifPowerSheetsService
- Mengambil data daya dari sheet DAYA/TARIF
- Struktur sama dengan customer service

### 3. TarifRevenueSheetsService
- Mengambil data revenue dari 2 sheets:
  - KWHJUAL/TARIF
  - PENDAPATAN/TARIF
- Menyimpan dengan flag data_type ('kwh' atau 'rp')

## Artisan Command

```bash
php artisan sync:tarif --year=2025
```

Command ini akan:
1. Fetch data dari Google Sheets (3 sheets)
2. Delete data lama untuk tahun tersebut
3. Insert data baru ke database
4. Menampilkan summary jumlah records

## Controller & Routes

### TarifDashboardController
- Route: `/tarif`
- Parameters: `year`, `month` (optional)
- Query data per kategori tarif
- Group by tarif_category untuk charts
- Top 10 tarif by customers

### Routes
```php
Route::get('/tarif', [TarifDashboardController::class, 'index'])
    ->name('dashboard.tarif');
```

## Views

### resources/views/tarif/index.blade.php
- Tab navigation (Per ULP / 309 Per Tarif)
- Month filter dengan "Semua Bulan" option
- Summary table (5 baris: Pelanggan, Daya, kWh, Rp, Rp/kWh)
- 4 Charts:
  1. **Pelanggan per Kategori** - Pie chart
  2. **Daya per Kategori** - Horizontal bar chart
  3. **kWh Jual per Kategori** - Vertical bar chart
  4. **Rp Pendapatan per Kategori** - Vertical bar chart
- Color palette: Teal/turquoise theme

## Kategori Tarif

Data dikelompokkan berdasarkan kategori utama:
- **S** - Sosial (S1/220VA, S2/450VA, S2/900VA, dll)
- **R** - Rumah Tangga (R1/900VA, R1/1300VA, R2/2200VA, dll)
- **B** - Bisnis (B1/450VA, B1/900VA, B2/6600VA, dll)
- **I** - Industri (I1/450VA, I1/900VA, I2/6600VA, dll)
- **P** - Pemerintah (P1/450VA, P1/900VA, P2/>200kVA, dll)
- **T** - Tegangan Tinggi
- **C** - C-type tariff
- **L** - L-type tariff

## Google Sheets Structure

Sheet PELANGGAN/TARIF (dan sheets lain mengikuti struktur yang sama):
- Row 3: Header "JUMLAH PELANGGAN 2025 (TOTAL BULANAN)"
- Row 4: Column headers (BULANAN | JAN | FEB | ... | DEC)
- Row 5-71: Data tarif per bulan
- Row 72: JUMLAH (grand total)

Subtotal rows (JUMLAH S, JUMLAH R, dll) di-skip saat parsing.

## Migration

Jalankan migration untuk membuat tabel:
```bash
php artisan migrate
```

Files:
- `2026_02_02_154500_create_tarif_customer_data_table.php`
- `2026_02_02_154501_create_tarif_power_and_revenue_tables.php`

## Usage

1. **Sync data pertama kali:**
   ```bash
   php artisan sync:tarif --year=2025
   ```

2. **Akses dashboard:**
   - Per ULP: http://localhost:8000/
   - Per Tarif: http://localhost:8000/tarif

3. **Filter data:**
   - Pilih tahun di dropdown (hanya 2025 tersedia)
   - Pilih bulan untuk melihat data spesifik
   - Klik tab untuk switch antara Per ULP dan Per Tarif

## Features

✅ Tab navigation untuk switch antar dashboard
✅ Filter bulan (Semua Bulan / Januari-Desember)
✅ Summary table dengan total agregat
✅ 4 chart dengan tipe berbeda (pie, horizontal bar, vertical bar)
✅ Color scheme konsisten dengan dashboard utama (teal theme)
✅ Responsive layout
✅ Year restriction (hanya 2025)
✅ Data dikelompokkan per kategori tarif (S, R, B, I, P, T, C, L)

## Notes

- Data tarif lebih detail dibanding data ULP (~70 kategori vs 6 ULP)
- Charts menampilkan data per kategori utama (S, R, B, I, P, T, C, L)
- Bisa extend untuk menampilkan Top 10 tarif individual jika diperlukan
- Real-time sync untuk tarif bisa ditambahkan seperti per-ULP dashboard

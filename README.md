# Dashboard Monitoring PLN 309 Perulp

Dashboard monitoring yang terintegrasi dengan Google Sheets untuk monitoring data listrik PLN secara real-time.

## 🚀 Fitur

- ✅ Integrasi dengan Google Sheets API
- ✅ Dashboard monitoring real-time dengan charts
- ✅ Sinkronisasi data otomatis/manual
- ✅ Database lokal untuk performa cepat
- ✅ Statistik dan visualisasi data
- ✅ Filter berdasarkan lokasi dan status
- ✅ Alert system untuk kondisi critical
- ✅ Responsive design dengan Bootstrap 5

## 📋 Requirements

- PHP 8.1 atau lebih tinggi
- Composer
- SQLite (sudah include)
- Google Cloud Project dengan Sheets API enabled
- Service Account dengan akses ke Google Spreadsheet

## 🛠️ Instalasi

### 1. Setup Google Sheets API

1. **Buat Google Cloud Project**:
   - Kunjungi [Google Cloud Console](https://console.cloud.google.com/)
   - Buat project baru
   - Enable Google Sheets API

2. **Buat Service Account**:
   - Di Google Cloud Console, buka "IAM & Admin" > "Service Accounts"
   - Klik "Create Service Account"
   - Beri nama dan deskripsi
   - Download file JSON key

3. **Simpan Service Account Key**:
   ```bash
   mkdir storage\app\google
   # Copy file JSON yang di-download ke folder ini dengan nama service-account.json
   ```

4. **Share Spreadsheet**:
   - Buka Google Spreadsheet Anda
   - Klik tombol "Share"
   - Paste email service account (dari file JSON)
   - Beri akses "Editor"

### 2. Konfigurasi Environment

Edit file `.env`:

```env
GOOGLE_SERVICE_ACCOUNT_JSON_PATH=storage/app/google/service-account.json
GOOGLE_SPREADSHEET_ID=YOUR_ACTUAL_SPREADSHEET_ID
GOOGLE_SHEET_NAME=Sheet1
```

### 3. Setup Database

```bash
php artisan migrate
```

### 4. Setup Struktur Google Spreadsheet

Header spreadsheet (baris pertama):
| location | status | voltage | current | power | energy | alert_type | description | recorded_at |

Atau dalam bahasa Indonesia:
| lokasi | status | tegangan | arus | daya | energi | tipe_alert | deskripsi | tanggal |

## 🎯 Cara Penggunaan

### Jalankan Development Server

```bash
php artisan serve
```

Buka: [http://localhost:8000](http://localhost:8000)

### Sync Data dari Google Sheets

Via Web:
- Klik tombol **"Sync Data"** di dashboard

Via Command:
```bash
php artisan sheets:sync
```

## 📊 API Endpoints

### GET /api/monitoring-data
```bash
# Semua data
curl http://localhost:8000/api/monitoring-data

# Filter by location
curl http://localhost:8000/api/monitoring-data?location=Lokasi%20A

# Filter by status
curl http://localhost:8000/api/monitoring-data?status=critical
```

### GET /api/statistics
```bash
curl http://localhost:8000/api/statistics
```

## 🔧 Troubleshooting

### Error: "Could not resolve host"
- Pastikan koneksi internet aktif
- Pastikan file service account JSON sudah benar

### Error: "The caller does not have permission"
- Pastikan spreadsheet sudah di-share dengan service account email

### Data tidak muncul
- Cek struktur kolom spreadsheet
- Cek log: `storage/logs/laravel.log`
- Jalankan: `php artisan sheets:sync -v`

### Database error
```bash
php artisan migrate:fresh
php artisan sheets:sync
```

## 📝 Command Available

```bash
# Sync data dari Google Sheets
php artisan sheets:sync

# Reset database
php artisan migrate:fresh

# Lihat routes
php artisan route:list

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 🏗️ Struktur Project

```
app/
├── Console/Commands/
│   └── SyncGoogleSheets.php
├── Http/Controllers/
│   └── DashboardController.php
├── Models/
│   └── MonitoringData.php
└── Services/
    └── GoogleSheetsService.php

config/
└── google.php

database/migrations/
└── 2026_01_29_000001_create_monitoring_data_table.php

resources/views/dashboard/
└── index.blade.php

storage/app/google/
└── service-account.json  # JANGAN COMMIT!
```

## 🔐 Security

⚠️ **PENTING:**
- **JANGAN** commit file `service-account.json`
- File sudah ditambahkan ke `.gitignore`
- Simpan backup key di tempat aman

## 📄 License

Internal use only - PLN 309 Perulp

---

**Dibuat dengan ❤️ untuk PLN 309 Perulp**

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

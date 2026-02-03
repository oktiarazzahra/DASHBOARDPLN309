# 📁 File-File Penting dalam Project

## 🔑 File Konfigurasi Utama

### `.env`
**Lokasi:** Root project
**Fungsi:** Environment variables dan konfigurasi sensitive
**Yang Perlu Diisi:**
```env
GOOGLE_SPREADSHEET_ID=your_spreadsheet_id_here
GOOGLE_SHEET_NAME=Sheet1
```

### `config/google.php`
**Lokasi:** `config/google.php`
**Fungsi:** Konfigurasi Google Sheets API
**Sudah Auto Setup:** ✅

### `storage/app/google/service-account.json`
**Lokasi:** `storage/app/google/service-account.json`
**Fungsi:** Credentials Google Service Account
**⚠️ WAJIB:** File ini HARUS ada dan berisi JSON key dari Google Cloud
**🔒 Security:** File ini di `.gitignore`, JANGAN di-commit ke Git!

---

## 📂 Struktur Folder Project

```
DASHBOARDPLN309PERULP/
│
├── 📁 app/
│   ├── 📁 Console/Commands/
│   │   └── SyncGoogleSheets.php       ← Command untuk sync manual
│   ├── 📁 Http/Controllers/
│   │   └── DashboardController.php     ← Controller utama dashboard
│   ├── 📁 Models/
│   │   └── MonitoringData.php          ← Model data monitoring
│   └── 📁 Services/
│       └── GoogleSheetsService.php     ← Logic integrasi Google Sheets
│
├── 📁 config/
│   └── google.php                      ← Konfigurasi Google API
│
├── 📁 database/
│   ├── 📁 migrations/
│   │   └── 2026_01_29_000001_create_monitoring_data_table.php
│   └── database.sqlite                 ← Database SQLite
│
├── 📁 resources/views/
│   └── 📁 dashboard/
│       └── index.blade.php             ← Tampilan dashboard
│
├── 📁 routes/
│   └── web.php                         ← Routes aplikasi
│
├── 📁 storage/
│   ├── 📁 app/google/
│   │   └── service-account.json        ← 🔑 SERVICE ACCOUNT KEY
│   └── 📁 logs/
│       └── laravel.log                 ← Log file untuk debugging
│
├── .env                                ← 🔑 ENVIRONMENT CONFIG
├── .gitignore                          ← File yang diabaikan Git
├── CHECKLIST.md                        ← Checklist setup
├── DATA_FORMAT.md                      ← Format data spreadsheet
├── QUICK_START.md                      ← Panduan cepat
└── README.md                           ← Dokumentasi lengkap
```

---

## 🎯 File Yang Perlu Anda Isi

### 1. Service Account JSON
**File:** `storage/app/google/service-account.json`
**Status:** ⚠️ BELUM ADA
**Cara Dapat:**
1. Download dari Google Cloud Console
2. Copy ke folder `storage/app/google/`
3. Rename menjadi `service-account.json`

**Isi file harus seperti:**
```json
{
  "type": "service_account",
  "project_id": "your-project-id",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...",
  "client_email": "xxx@xxx.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  ...
}
```

### 2. Environment Config
**File:** `.env`
**Status:** ⚠️ PERLU DIISI
**Yang Harus Diisi:**
```env
# Ganti dengan ID spreadsheet Anda
GOOGLE_SPREADSHEET_ID=1abc123def456...

# Nama sheet (default: Sheet1)
GOOGLE_SHEET_NAME=Sheet1
```

**Cara Dapat Spreadsheet ID:**
1. Buka Google Spreadsheet
2. Lihat URL: `https://docs.google.com/spreadsheets/d/COPY_THIS_PART/edit`
3. Copy bagian setelah `/d/` dan sebelum `/edit`

---

## 📊 File Yang Auto-Generated

### Database
**File:** `database/database.sqlite`
**Status:** ✅ Auto created saat migration
**Fungsi:** Menyimpan data yang di-sync dari Google Sheets

### Logs
**File:** `storage/logs/laravel.log`
**Status:** ✅ Auto created
**Fungsi:** Mencatat semua error dan activity
**Cara Baca:**
```bash
# Windows
type storage\logs\laravel.log

# Atau buka dengan Notepad
notepad storage\logs\laravel.log
```

---

## 🔍 Cara Cek File Sudah Benar

### Cek Service Account JSON
```bash
# Pastikan file ada
dir storage\app\google\service-account.json

# Buka dan cek isi
notepad storage\app\google\service-account.json
```

**✅ Benar jika:**
- File ada
- Isi format JSON
- Ada field `client_email`
- Ada field `private_key`

### Cek .env Config
```bash
notepad .env
```

**✅ Benar jika:**
- `GOOGLE_SPREADSHEET_ID` terisi (bukan `your_spreadsheet_id_here`)
- `GOOGLE_SHEET_NAME` terisi (default: `Sheet1`)
- `GOOGLE_SERVICE_ACCOUNT_JSON_PATH` = `storage/app/google/service-account.json`

### Cek Database
```bash
dir database\database.sqlite
```

**✅ Benar jika:**
- File ada
- Ukuran > 0 bytes

---

## 🚨 File Yang HARUS Di-Gitignore

File-file ini JANGAN di-commit ke Git karena berisi data sensitive:

```
storage/app/google/service-account.json  ← PENTING!
.env
.env.backup
database/database.sqlite
storage/logs/*.log
```

File `.gitignore` sudah di-setup dengan benar ✅

---

## 📝 File Dokumentasi

### README.md
Dokumentasi lengkap project dengan:
- Fitur
- Cara instalasi
- API endpoints
- Troubleshooting

### QUICK_START.md
Panduan setup cepat (5 menit)

### CHECKLIST.md
Checklist step-by-step setup

### DATA_FORMAT.md
Format dan contoh data spreadsheet

---

## 🔧 File Development

### Composer Files
- `composer.json` ← Dependencies PHP
- `composer.lock` ← Lock file dependencies

### Artisan
- `artisan` ← Laravel command line tool

---

## 📞 Yang Harus Dilakukan Sekarang

1. ☑️ Setup `storage/app/google/service-account.json`
2. ☑️ Isi `GOOGLE_SPREADSHEET_ID` di `.env`
3. ☑️ Jalankan `php artisan migrate` (jika belum)
4. ☑️ Test dengan `php artisan sheets:sync`
5. ☑️ Start server: `php artisan serve`

---

**Setelah semua file terisi dengan benar, dashboard siap digunakan!** 🎉

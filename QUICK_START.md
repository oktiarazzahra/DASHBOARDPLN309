# Quick Start Guide - Dashboard PLN 309

## 📌 Setup Cepat (5 Menit)

### 1. Setup Google Sheets API

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih yang sudah ada
3. Enable Google Sheets API:
   - Buka "APIs & Services" > "Library"
   - Cari "Google Sheets API"
   - Klik "Enable"

4. Buat Service Account:
   - Buka "APIs & Services" > "Credentials"
   - Klik "Create Credentials" > "Service Account"
   - Isi nama: `dashboard-pln-309`
   - Klik "Create and Continue" > "Done"

5. Download Key:
   - Klik service account yang baru dibuat
   - Tab "Keys" > "Add Key" > "Create New Key"
   - Pilih "JSON" > "Create"
   - File akan otomatis ter-download

### 2. Setup Project

```bash
# 1. Buat folder untuk service account
mkdir storage\app\google

# 2. Copy file JSON yang di-download ke folder google
copy Downloads\your-service-account-file.json storage\app\google\service-account.json

# 3. Edit file .env
notepad .env
```

Isi di `.env`:
```env
GOOGLE_SPREADSHEET_ID=paste-spreadsheet-id-disini
GOOGLE_SHEET_NAME=Sheet1
```

### 3. Ambil Spreadsheet ID

1. Buka Google Spreadsheet Anda
2. Lihat URL di browser
3. Copy ID dari URL:
```
https://docs.google.com/spreadsheets/d/COPY_THIS_PART/edit
                                        ^^^^^^^^^^^^^^^^
```

### 4. Share Spreadsheet

1. Buka file service account JSON yang sudah di-download
2. Cari email (field `client_email`), contoh:
   `dashboard-pln-309@project-name.iam.gserviceaccount.com`
3. Buka Google Spreadsheet Anda
4. Klik tombol **Share**
5. Paste email service account
6. Pilih role **Editor**
7. Klik **Send**

### 5. Isi Data Spreadsheet

Buat header di baris pertama:

| location | status | voltage | current | power | energy | alert_type | description | recorded_at |
|----------|--------|---------|---------|-------|--------|------------|-------------|-------------|
| Gardu A  | normal | 220.5   | 10.2    | 2.25  | 100.5  |            | OK          | 2026-01-29 10:00 |
| Gardu B  | warning| 215.0   | 15.5    | 3.33  | 150.2  | warning    | High load   | 2026-01-29 10:05 |
| Gardu C  | critical| 190.0  | 20.0    | 3.80  | 200.0  | critical   | Low voltage | 2026-01-29 10:10 |

### 6. Jalankan Aplikasi

```bash
# Run migration
php artisan migrate

# Start server
php artisan serve
```

Buka browser: **http://localhost:8000**

### 7. Sync Data

Klik tombol **"Sync Data"** di dashboard, atau jalankan:
```bash
php artisan sheets:sync
```

## ✅ Checklist Setup

- [ ] Google Cloud Project sudah dibuat
- [ ] Google Sheets API sudah di-enable
- [ ] Service Account sudah dibuat
- [ ] File JSON sudah di-download
- [ ] File JSON sudah di-copy ke `storage/app/google/service-account.json`
- [ ] Spreadsheet ID sudah di-copy ke `.env`
- [ ] Spreadsheet sudah di-share dengan service account
- [ ] Data sudah ada di spreadsheet dengan header yang benar
- [ ] Migration sudah dijalankan
- [ ] Server sudah running
- [ ] Data berhasil di-sync

## 🆘 Quick Troubleshooting

### Masalah: "The caller does not have permission"
**Solusi:** Spreadsheet belum di-share dengan service account
- Share spreadsheet dengan email dari file JSON

### Masalah: "Spreadsheet not found"
**Solusi:** Spreadsheet ID salah
- Cek kembali ID di .env dengan URL spreadsheet

### Masalah: Data tidak muncul
**Solusi:** Struktur kolom spreadsheet salah
- Pastikan header sesuai dengan tabel di atas

### Masalah: Error "Class not found"
**Solusi:** Jalankan:
```bash
composer dump-autoload
php artisan config:clear
```

## 📞 Butuh Bantuan?

Lihat log error di:
```bash
storage\logs\laravel.log
```

Atau jalankan sync dengan verbose:
```bash
php artisan sheets:sync -v
```

---

Setelah setup selesai, baca **README.md** untuk fitur lengkap!

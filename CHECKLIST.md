# 🎯 CHECKLIST SETUP - Dashboard PLN 309

Print checklist ini dan tandai setiap langkah yang sudah selesai!

---

## ☑️ Persiapan Awal

- [ ] PHP 8.1+ sudah terinstall
- [ ] Composer sudah terinstall
- [ ] Browser modern (Chrome/Firefox/Edge)
- [ ] Koneksi internet stabil
- [ ] Akun Google aktif

---

## ☑️ Google Cloud Setup

### Membuat Project
- [ ] Buka https://console.cloud.google.com/
- [ ] Klik "Select a project" > "New Project"
- [ ] Nama project: `dashboard-pln-309` (atau nama lain)
- [ ] Klik "Create"
- [ ] Tunggu sampai project selesai dibuat

### Enable Google Sheets API
- [ ] Di sidebar, klik "APIs & Services" > "Library"
- [ ] Cari "Google Sheets API"
- [ ] Klik "Enable"
- [ ] Tunggu sampai status menjadi "Enabled"

### Membuat Service Account
- [ ] Klik "APIs & Services" > "Credentials"
- [ ] Klik "Create Credentials" > "Service Account"
- [ ] Isi Service account name: `dashboard-pln-309`
- [ ] Service account ID akan otomatis terisi
- [ ] Klik "Create and Continue"
- [ ] Skip "Grant this service account access" (klik "Continue")
- [ ] Skip "Grant users access" (klik "Done")

### Download Service Account Key
- [ ] Klik service account yang baru dibuat
- [ ] Tab "Keys"
- [ ] Klik "Add Key" > "Create New Key"
- [ ] Pilih "JSON"
- [ ] Klik "Create"
- [ ] File JSON akan otomatis ter-download
- [ ] **PENTING:** Simpan file ini dengan aman!

### Catat Email Service Account
- [ ] Buka file JSON yang sudah di-download
- [ ] Cari field `client_email`
- [ ] Copy email tersebut (contoh: `xxx@xxx.iam.gserviceaccount.com`)
- [ ] Email: _________________________________

---

## ☑️ Google Spreadsheet Setup

### Buat atau Siapkan Spreadsheet
- [ ] Buka Google Sheets
- [ ] Buat spreadsheet baru atau gunakan yang sudah ada
- [ ] Nama spreadsheet: `Monitoring PLN 309` (atau nama lain)

### Setup Header Spreadsheet
- [ ] Di baris pertama, isi header kolom:
  ```
  location | status | voltage | current | power | energy | alert_type | description | recorded_at
  ```
  ATAU
  ```
  lokasi | status | tegangan | arus | daya | energi | tipe_alert | deskripsi | tanggal
  ```

### Isi Sample Data
- [ ] Isi minimal 3-5 baris data untuk testing
- [ ] Gunakan data dari file `DATA_FORMAT.md`
- [ ] Pastikan format tanggal benar

### Ambil Spreadsheet ID
- [ ] Lihat URL di browser
- [ ] Copy bagian ID dari URL:
  ```
  https://docs.google.com/spreadsheets/d/COPY_THIS_PART/edit
  ```
- [ ] Spreadsheet ID: _________________________________

### Share Spreadsheet
- [ ] Klik tombol "Share" (pojok kanan atas)
- [ ] Paste email service account yang sudah di-copy tadi
- [ ] Pilih role: **Editor**
- [ ] **UNCHECK** "Notify people" (agar tidak kirim email)
- [ ] Klik "Share" atau "Send"
- [ ] Pastikan service account muncul di list "People with access"

---

## ☑️ Laravel Project Setup

### Copy Service Account File
- [ ] Buka File Explorer
- [ ] Navigate ke: `c:\Users\Oktiara Azzahrah\PROJECTS\DASHBOARDPLN309PERULP`
- [ ] Buat folder: `storage\app\google`
- [ ] Copy file JSON yang di-download ke folder tersebut
- [ ] Rename file menjadi: `service-account.json`
- [ ] Confirm path: `storage\app\google\service-account.json`

### Edit Environment File
- [ ] Buka file `.env` di project root
- [ ] Cari section Google Sheets Configuration
- [ ] Isi `GOOGLE_SPREADSHEET_ID` dengan ID yang sudah di-copy
- [ ] Isi `GOOGLE_SHEET_NAME` (default: `Sheet1`)
- [ ] Pastikan `GOOGLE_SERVICE_ACCOUNT_JSON_PATH` sudah benar
- [ ] Save file

### Install Dependencies (Skip jika sudah)
- [ ] Buka terminal/command prompt
- [ ] cd ke folder project
- [ ] Jalankan: `composer install`
- [ ] Tunggu sampai selesai

### Setup Database
- [ ] Jalankan: `php artisan migrate`
- [ ] Pastikan muncul: "Migration completed successfully"
- [ ] Check folder `database` ada file `database.sqlite`

---

## ☑️ Testing

### Test Connection
- [ ] Jalankan: `php artisan sheets:sync -v`
- [ ] Tidak ada error "permission denied"
- [ ] Tidak ada error "spreadsheet not found"
- [ ] Muncul pesan success dengan jumlah data yang di-sync

### Start Server
- [ ] Jalankan: `php artisan serve`
- [ ] Muncul: "Server started on http://localhost:8000"
- [ ] Buka browser: http://localhost:8000

### Check Dashboard
- [ ] Dashboard muncul dengan sempurna
- [ ] Tidak ada error di browser console (F12)
- [ ] Statistik cards menampilkan angka
- [ ] Charts muncul
- [ ] Tabel data muncul

### Test Sync Button
- [ ] Klik tombol "Sync Data"
- [ ] Muncul alert success
- [ ] Data bertambah atau ter-update
- [ ] Statistik berubah sesuai data baru

---

## ☑️ Final Verification

- [ ] Service account email sudah ada di spreadsheet access
- [ ] File `service-account.json` ada di `storage/app/google/`
- [ ] File `.env` sudah diisi dengan benar
- [ ] Database migration sudah dijalankan
- [ ] Test sync berhasil tanpa error
- [ ] Dashboard bisa diakses di browser
- [ ] Data dari spreadsheet muncul di dashboard
- [ ] Tombol sync berfungsi

---

## 🎉 Setup Complete!

Jika semua checklist sudah di-tandai, setup berhasil!

### Next Steps:
1. Tambahkan lebih banyak data ke spreadsheet
2. Customize tampilan dashboard sesuai kebutuhan
3. Setup auto-sync (optional)
4. Share dashboard URL ke tim

---

## 📞 Troubleshooting

Jika ada yang tidak berfungsi, cek:
- [ ] `storage/logs/laravel.log` untuk error log
- [ ] Console browser (F12) untuk JavaScript error
- [ ] Jalankan `php artisan config:clear`
- [ ] Restart server: `php artisan serve`

---

**Setup Date:** ___/___/______
**Setup By:** _________________
**Contact:** __________________

---

Print halaman ini dan simpan sebagai dokumentasi!

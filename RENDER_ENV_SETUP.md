# 🔐 Environment Variables untuk Render

## ✅ Environment Variables yang WAJIB di-set di Render Dashboard

Buka **Render Dashboard** → Pilih service Anda → Tab **Environment**

### 1. **APP_KEY** (WAJIB)
```
Generate dengan command: php artisan key:generate --show
Contoh: base64:1234abcd...
```

### 2. **GOOGLE_SPREADSHEET_ID** (WAJIB)
```
ID dari Google Sheets Anda
Contoh: 1abcdefghijklmnopqrstuvwxyz123456789
```

### 3. **GOOGLE_SERVICE_ACCOUNT_BASE64** (WAJIB)
```
Base64 encoding dari service account JSON
Anda sudah punya file: render_secret_base64.txt
Salin isi file tersebut dan paste ke Render
```

### 4. **APP_URL** (WAJIB)
```
URL public dari Render
Contoh: https://dashboard-pln-309.onrender.com
```

---

## 📋 Environment Variables yang SUDAH OTOMATIS di render.yaml

Variables ini **TIDAK PERLU** di-set manual karena sudah ada di render.yaml:

| Variable | Value | Keterangan |
|----------|-------|------------|
| `APP_ENV` | `production` | Laravel environment |
| `APP_DEBUG` | `false` | Debug mode OFF untuk production |
| `DB_CONNECTION` | `sqlite` | Tipe database |
| `DB_DATABASE` | `/var/www/html/storage/database.sqlite` | Path database SQLite |
| `AUTO_SYNC_ON_START` | `"true"` | Auto sync saat container start |

---

## 🎯 Cara Set Environment di Render

1. **Login ke Render Dashboard**: https://dashboard.render.com
2. **Pilih service** Anda (dashboard-pln-309)
3. **Klik tab "Environment"** di sidebar kiri
4. **Klik "Add Environment Variable"**
5. **Tambahkan satu per satu**:
   - **Key**: `APP_KEY`  
     **Value**: `base64:...` (hasil dari `php artisan key:generate --show`)
   
   - **Key**: `APP_URL`  
     **Value**: `https://dashboard-pln-309.onrender.com` (sesuaikan URL Anda)
   
   - **Key**: `GOOGLE_SPREADSHEET_ID`  
     **Value**: ID spreadsheet Anda
   
   - **Key**: `GOOGLE_SERVICE_ACCOUNT_BASE64`  
     **Value**: Isi dari file `render_secret_base64.txt`

6. **Klik "Save Changes"**
7. Render akan **otomatis redeploy**

---

## 🔍 Cara Generate APP_KEY

Di komputer lokal Anda, jalankan:

```powershell
php artisan key:generate --show
```

Copy hasilnya (contoh: `base64:1234abcd...`) dan paste ke Render sebagai value `APP_KEY`.

---

## ✅ Checklist Environment Variables

- [ ] `APP_KEY` → Generate dari `php artisan key:generate --show`
- [ ] `APP_URL` → URL Render Anda
- [ ] `GOOGLE_SPREADSHEET_ID` → ID Google Sheets
- [ ] `GOOGLE_SERVICE_ACCOUNT_BASE64` → Isi file `render_secret_base64.txt`

**TIDAK PERLU tambahkan lagi** (sudah di render.yaml):
- ~~DB_CONNECTION~~
- ~~DB_DATABASE~~
- ~~APP_ENV~~
- ~~APP_DEBUG~~
- ~~AUTO_SYNC_ON_START~~

---

## 🚀 Setelah Set Environment

1. Simpan semua environment variables
2. Render akan otomatis redeploy
3. Tunggu hingga selesai (sekitar 3-5 menit)
4. Buka URL dashboard Anda
5. Jika sukses, dashboard akan muncul dan auto-sync berjalan

---

## 🐛 Troubleshooting

**Jika masih error 500:**
1. Cek Render Logs: Dashboard → service → Logs
2. Pastikan semua 4 environment variables sudah di-set
3. Pastikan APP_KEY dimulai dengan `base64:`
4. Pastikan GOOGLE_SERVICE_ACCOUNT_BASE64 tidak ada spasi atau newline

**Jika APP_KEY salah:**
```
Error: "No application encryption key has been specified"
```
→ Generate ulang dengan `php artisan key:generate --show` dan set di Render

**Jika Google Sheets error:**
```
Error: "Unable to read Google Sheets"
```
→ Cek GOOGLE_SPREADSHEET_ID dan GOOGLE_SERVICE_ACCOUNT_BASE64 sudah benar

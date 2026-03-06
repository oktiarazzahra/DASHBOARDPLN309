# 🚀 Deploy ke Koyeb (Gratis, Tanpa Kartu Kredit!)

## ✨ Keuntungan Koyeb Free Tier:
- ✅ **Gratis selamanya** (TIDAK perlu credit card!)
- ✅ 2 web services (512MB RAM each)
- ✅ SSL otomatis (HTTPS)
- ✅ Auto-deploy dari GitHub
- ✅ Deploy via Docker (Dockerfile kita sudah siap!)
- ✅ Setup ~15 menit

---

## 📋 Persiapan Sebelum Deploy

Pastikan sudah punya:
1. **Akun GitHub** dengan repo project ini
2. **Google service account JSON** file (untuk akses Google Sheets)
3. **Spreadsheet ID** Google Sheets kamu

---

## 🔐 Step 1: Daftar Koyeb

1. Buka: https://www.koyeb.com/
2. Klik **"Sign up for free"**
3. Register pakai **email/GitHub** (tidak perlu credit card!)
4. Verifikasi email
5. Login ke dashboard

---

## 🔗 Step 2: Hubungkan GitHub

1. Di Koyeb dashboard, klik tanda **"+"** atau **"Create Service"**
2. Pilih **"GitHub"** sebagai source
3. Klik **"Connect GitHub account"**
4. Authorize Koyeb untuk akses repo kamu
5. Pilih repo: `DASHBOARDPLN309PERULP` (atau nama repo kamu)

---

## 🐳 Step 3: Konfigurasi Service

Isi form deploy:

### Build Settings:
- **Builder:** `Dockerfile`
- **Dockerfile path:** `Dockerfile` (sudah ada di root project)
- **Build context:** `/` (root)

### Service Name:
```
dashboard-pln-309
```

### Port:
```
8080
```

### Region:
- Pilih **Singapore (sin)** - terdekat dari Indonesia

---

## 🔑 Step 4: Setup Environment Variables

Di bagian **"Environment variables"**, tambahkan semua variable berikut:

### Wajib:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dashboard-pln-309.koyeb.app
```

> **APP_KEY** akan di-generate otomatis oleh entrypoint script, tapi
> lebih aman jika set manual. Jalankan di PC kamu dulu:
> ```powershell
> php artisan key:generate --show
> ```
> Copy hasilnya (format: `base64:xxx...`), lalu set:
> ```
> APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxx
> ```

### Google Sheets:
```
GOOGLE_SPREADSHEET_ID=<id spreadsheet kamu>
```
> ID ada di URL spreadsheet: `docs.google.com/spreadsheets/d/**ID_INI**/edit`

### Google Service Account (Base64):
Jalankan perintah ini di PowerShell PC kamu:
```powershell
$json = Get-Content "storage\app\google\service-account.json" -Raw
$base64 = [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes($json))
Write-Output $base64
```
Copy output-nya, lalu tambahkan environment variable:
```
GOOGLE_SERVICE_ACCOUNT_BASE64=<hasil base64 di atas>
```

### Auto-Sync saat Startup:
```
AUTO_SYNC_ON_START=true
```
> Ini akan otomatis sync data dari Google Sheets setiap kali app start/restart.
> Butuh waktu ~2-3 menit saat pertama kali deploy.

### Database:
```
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/storage/database.sqlite
```

---

## 🚀 Step 5: Deploy!

1. Setelah semua setting diisi, klik **"Deploy"**
2. Koyeb akan mulai build Docker image (~3-5 menit)
3. Setelah build selesai, app akan start
4. Auto-sync data dari Google Sheets akan berjalan (~2-3 menit)
5. Selesai!

---

## ✅ Step 6: Buka Dashboard

URL app kamu akan seperti:
```
https://dashboard-pln-309-<username>.koyeb.app
```

Atau lihat di Koyeb dashboard → Services → URL.

---

## ⚠️ Catatan Penting: SQLite & Storage

Koyeb **free tier** menggunakan **ephemeral storage** — artinya:
- Data SQLite **akan reset** setiap kali app di-redeploy
- Tapi data akan **otomatis sync ulang** dari Google Sheets saat startup
  (karena kita set `AUTO_SYNC_ON_START=true`)

**Solusi alternatif jika ingin data persisten:**
- Gunakan **Koyeb Persistent Volume** (berbayar)
- Atau **upgrade** ke plan berbayar

Untuk kebutuhan dashboard internal, reset + re-sync sudah cukup.

---

## 🔄 Update Aplikasi (Push Code Baru)

Setiap kali push ke GitHub, Koyeb akan **auto-deploy** otomatis:

```powershell
git add .
git commit -m "Update fitur baru"
git push
```

Koyeb akan detect perubahan dan rebuild otomatis dalam ~5 menit.

Untuk **disable auto-deploy**, bisa diatur di Koyeb → Service → Settings.

---

## 🔧 Troubleshooting

### Lihat logs:
Di Koyeb dashboard → Services → klik nama service → tab **"Logs"**

### App error 500:
- Cek `APP_KEY` sudah di-set
- Cek `APP_DEBUG=false` di production
- Lihat logs untuk detail error

### Data kosong / tidak sync:
- Cek `GOOGLE_SPREADSHEET_ID` sudah benar
- Cek `GOOGLE_SERVICE_ACCOUNT_BASE64` sudah benar
- Cek logs untuk error sync

### Restart manual:
Di Koyeb dashboard → Services → klik service → tombol **"Redeploy"**

---

## 📊 Free Tier Limits

| Resource | Free Tier |
|---|---|
| Services | 2 services |
| RAM | 512MB per service |
| CPU | Shared (burstable) |
| Bandwidth | Unlimited |
| SSL | ✅ Gratis otomatis |
| Custom Domain | ✅ Support |
| Auto-deploy | ✅ Dari GitHub |

---

## 🎁 Custom Domain (Opsional)

Jika punya domain sendiri:
1. Koyeb dashboard → Services → klik service
2. Tab **"Domains"**
3. Klik **"Add domain"**
4. Masukkan domain kamu
5. Add CNAME record sesuai instruksi Koyeb
6. SSL otomatis aktif!

---

## 🆘 Butuh Bantuan?

- Docs: https://www.koyeb.com/docs
- Community: https://community.koyeb.com/
- Status: https://status.koyeb.com/

---

**Dashboard PLN sudah online dan bisa diakses publik 24/7!** 🎉

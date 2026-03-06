# 🚀 Deploy ke Fly.io (Gratis $0 Selamanya!)

## ✨ Keuntungan Fly.io Free Tier:
- ✅ **Gratis selamanya** (tidak perlu credit card!)
- ✅ 3 tiny VMs (256MB RAM each) - cukup untuk 1 app
- ✅ 3GB persistent storage
- ✅ 160GB bandwidth/bulan
- ✅ Auto-deploy dari GitHub
- ✅ SSL gratis otomatis
- ✅ Setup **10 menit**!

---

## 📋 Step 1: Install Fly CLI (2 menit)

### Windows (PowerShell):
```powershell
# Download dan install
iwr https://fly.io/install.ps1 -useb | iex
```

### Atau Download Manual:
- Buka: https://fly.io/docs/hands-on/install-flyctl/
- Download installer untuk Windows
- Install seperti biasa

### Verify Installation:
```powershell
flyctl version
```

---

## 🔐 Step 2: Signup Fly.io (3 menit)

```powershell
# Login atau signup
flyctl auth signup
```

Akan buka browser untuk signup:
- ✅ **Email** saja (TIDAK perlu credit card!)
- ✅ Verify email
- ✅ Selesai

---

## 🚢 Step 3: Deploy Aplikasi (5 menit)

### A. Masuk ke folder project:
```powershell
cd "C:\Users\Oktiara Azzahrah\PROJECTS\DASHBOARDPLN309PERULP"
```

### B. Launch aplikasi ke Fly.io:
```powershell
flyctl launch
```

**Akan muncul pertanyaan:**

1. **Choose an app name:** `dashboard-pln-309` (atau nama lain)
2. **Choose a region:** Ketik **sin** (Singapore - terdekat)
3. **Would you like to set up a PostgreSQL database?** → **NO** (kita pakai SQLite)
4. **Would you like to set up a Redis database?** → **NO**
5. **Would you like to deploy now?** → **NO** (kita setup dulu)

### C. Setup secrets (environment variables):
```powershell
# Set APP_KEY (generate random key)
flyctl secrets set APP_KEY=base64:$(php artisan key:generate --show)

# Set Google Spreadsheet ID (ganti dengan ID spreadsheet Anda)
flyctl secrets set GOOGLE_SPREADSHEET_ID=your_spreadsheet_id_here
```

### D. Upload service account JSON:

**Cara 1 - Via Base64 (recommended):**
```powershell
# Convert file ke base64
$json = Get-Content "storage\app\google\service-account.json" -Raw
$base64 = [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes($json))
flyctl secrets set GOOGLE_SERVICE_ACCOUNT_BASE64=$base64
```

**Cara 2 - Manual copy file via SSH (nanti setelah deploy):**
- Skip dulu, kita upload nanti

### E. Deploy!
```powershell
flyctl deploy
```

Tunggu 3-5 menit. Fly.io akan:
1. Build Docker image
2. Upload ke registry
3. Deploy ke VM
4. Start aplikasi

---

## 🎯 Step 4: Setup Service Account (jika belum via base64)

Jika Anda skip base64, upload manual:

```powershell
# SSH ke VM
flyctl ssh console

# Create directory
mkdir -p /var/www/html/storage/app/google

# Exit SSH
exit
```

Lalu create file `service-account.json` via Fly.io dashboard atau:

```powershell
# Copy file ke VM
flyctl ssh sftp shell
put storage/app/google/service-account.json /var/www/html/storage/app/google/service-account.json
exit
```

Atau **edit Dockerfile** untuk include base64 decode di entrypoint.

---

## 🔄 Step 5: Sync Data Pertama Kali

```powershell
# SSH ke VM
flyctl ssh console

# Run sync
cd /var/www/html
php artisan sync:all --year=2025
php artisan sync:tarif --year=2025
php artisan sync:tarif-ulp --year=2025

# Exit
exit
```

---

## ✅ Step 6: Buka Dashboard!

```powershell
# Buka di browser
flyctl open
```

Atau akses via URL: `https://dashboard-pln-309.fly.dev`

🎉 **Dashboard sudah online dan bisa diakses publik 24/7!**

---

## 🔧 Troubleshooting

### Check logs jika ada error:
```powershell
flyctl logs
```

### Restart aplikasi:
```powershell
flyctl apps restart dashboard-pln-309
```

### SSH untuk debug:
```powershell
flyctl ssh console
```

### Check status:
```powershell
flyctl status
```

---

## 🔄 Update Aplikasi (Push Code Baru)

Setiap kali update code di GitHub:

```powershell
# Pull update
git pull

# Deploy ulang
flyctl deploy
```

Atau setup **auto-deploy** dari GitHub Actions (opsional).

---

## 📊 Monitor Resource Usage

```powershell
# Check metrics
flyctl dashboard
```

Buka dashboard web: https://fly.io/dashboard

**Free tier limits:**
- 256MB RAM (cukup untuk Laravel + SQLite)
- 3GB disk (cukup untuk database + logs)
- 160GB bandwidth/bulan (cukup untuk ~5000-10000 pageviews)

---

## 🎁 Bonus: Setup Custom Domain (Opsional)

Jika punya domain sendiri:

```powershell
# Add domain
flyctl certs add yourdomain.com

# Akan dapat instruksi DNS setting
# Add A & AAAA records sesuai instruksi
```

SSL otomatis gratis dari Let's Encrypt!

---

## 🔐 Setup Auto-Sync Worker (Background Job)

Edit `fly.toml`:

```toml
# Tambah di akhir file
[[services]]
  internal_port = 8080
  protocol = "tcp"

[[processes]]
  app = "web"
  cmd = "supervisord -c /etc/supervisor/conf.d/supervisord.conf"

[[processes]]
  worker = "php artisan data:auto-sync --year=2025"
```

Lalu deploy ulang:
```powershell
flyctl deploy
```

---

## 💰 Biaya

**FREE TIER:**
- ✅ 3 shared-cpu-1x VMs (256MB RAM)
- ✅ 3GB persistent volumes
- ✅ 160GB outbound data transfer
- ✅ **$0/bulan - GRATIS SELAMANYA!**

**Jika butuh lebih (opsional):**
- Upgrade RAM → ~$2-5/bulan
- Extra bandwidth → $0.02/GB

Tapi untuk internal company/small traffic, free tier sudah cukup!

---

## 📝 Catatan Penting

1. **SQLite database** disimpan di `/data` (persistent volume) - tidak akan hilang saat redeploy
2. **Service account JSON** harus di-upload manual atau via base64 secret
3. **Logs** bisa dilihat via `flyctl logs`
4. **Auto-deploy** bisa disetup via GitHub Actions

---

## 🆘 Butuh Bantuan?

- Docs: https://fly.io/docs/
- Community: https://community.fly.io/
- Status: https://status.flyio.net/

---

**Setup selesai! Dashboard Anda sudah online 24/7 dan bisa diakses publik!** 🚀

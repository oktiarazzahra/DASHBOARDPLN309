# 🚀 Deploy ke Oracle Cloud (Gratis Selamanya)

## Step 1: Daftar Oracle Cloud (15 menit)

1. **Buka** https://www.oracle.com/cloud/free/
2. **Klik "Start for free"**
3. **Isi form:**
   - Email
   - Country: Indonesia
   - Home Region: **AP-SINGAPORE-1** (paling dekat)
4. **Verifikasi kartu kredit** (tidak akan didebet, cuma verifikasi)
5. **Tunggu approval email** (~5-30 menit)

---

## Step 2: Buat VM Instance (10 menit)

### A. Login ke Oracle Cloud Console
- https://cloud.oracle.com/

### B. Create Compute Instance
1. Buka **Menu (☰)** → **Compute** → **Instances**
2. Klik **"Create Instance"**

### C. Configure Instance
**Basic Information:**
- Name: `dashboard-pln-309`
- Compartment: (root) - biarkan default

**Placement:**
- Availability Domain: pilih salah satu (AD-1, AD-2, atau AD-3)

**Image and Shape:**
- **Image:** Ubuntu 22.04 (gratis)
- **Shape:** 
  - Click "Change Shape"
  - Pilih **VM.Standard.A1.Flex** (ARM - gratis selamanya)
  - OCPUs: **4** (max gratis)
  - Memory: **24 GB** (max gratis)

**Networking:**
- Virtual cloud network: (default) VCN-xxx
- Subnet: (default) Public Subnet
- **Centang:** "Assign a public IPv4 address"

**Add SSH keys:**
- **PENTING:** Download private key file (akan dipakai untuk login)
- Simpan file `.key` di folder aman

**Boot volume:**
- Biarkan default (50 GB)

3. Klik **"Create"** → tunggu status jadi **Running** (~2 menit)
4. **Copy IP Public** (contoh: `123.456.78.90`)

---

## Step 3: Setup Firewall (5 menit)

### A. Buka Port 80 dan 443
1. Di halaman Instance, klik **VCN name** (VCN-xxx)
2. Klik **Security Lists** → **Default Security List**
3. Klik **"Add Ingress Rules"**

**Rule 1 - HTTP:**
- Source CIDR: `0.0.0.0/0`
- IP Protocol: TCP
- Destination Port Range: `80`
- Description: `HTTP`

**Rule 2 - HTTPS:**
- Source CIDR: `0.0.0.0/0`
- IP Protocol: TCP
- Destination Port Range: `443`
- Description: `HTTPS`

4. Klik **"Add Ingress Rules"**

---

## Step 4: Login ke Server (SSH)

### Windows (PowerShell):
```powershell
# Ubah permission SSH key
icacls "C:\path\to\ssh-key-xxx.key" /inheritance:r
icacls "C:\path\to\ssh-key-xxx.key" /grant:r "%username%:R"

# Login (ganti IP_PUBLIK dengan IP server Anda)
ssh -i "C:\path\to\ssh-key-xxx.key" ubuntu@IP_PUBLIK
```

### Atau pakai **PuTTY** (lebih mudah):
1. Download PuTTY: https://www.putty.org/
2. Convert `.key` ke `.ppk` pakai **PuTTYgen**
3. Login pakai PuTTY

---

## Step 5: Install Dependencies di Server

Setelah login SSH, jalankan command ini satu per satu:

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring \
  php8.2-xml php8.2-sqlite3 php8.2-curl php8.2-zip php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install Git
sudo apt install -y git unzip
```

---

## Step 6: Clone & Setup Aplikasi

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/oktiarazzahra/DASHBOARDPLN309.git dashboard-pln

# Set ownership
sudo chown -R www-data:www-data /var/www/dashboard-pln
sudo chmod -R 775 /var/www/dashboard-pln/storage
sudo chmod -R 775 /var/www/dashboard-pln/bootstrap/cache

# Masuk ke folder
cd /var/www/dashboard-pln

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Copy .env
sudo cp .env.example .env

# Generate key
sudo -u www-data php artisan key:generate
```

---

## Step 7: Setup Environment & Google Credentials

```bash
# Edit .env
sudo nano /var/www/dashboard-pln/.env
```

**Ubah:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_IP_ADDRESS

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/dashboard-pln/database/database.sqlite

GOOGLE_SPREADSHEET_ID=your_spreadsheet_id_here
```

**Upload service account JSON:**
```bash
# Buat folder
sudo mkdir -p /var/www/dashboard-pln/storage/app/google

# Copy isi file service-account.json dari PC
# Bisa pakai SFTP atau paste manual
sudo nano /var/www/dashboard-pln/storage/app/google/service-account.json
# Paste isi JSON, Ctrl+X, Y, Enter

# Set permission
sudo chown www-data:www-data /var/www/dashboard-pln/storage/app/google/service-account.json
sudo chmod 600 /var/www/dashboard-pln/storage/app/google/service-account.json
```

---

## Step 8: Setup Database

```bash
# Buat database
sudo -u www-data touch /var/www/dashboard-pln/database/database.sqlite

# Run migrations
cd /var/www/dashboard-pln
sudo -u www-data php artisan migrate --force

# Sync data pertama kali
sudo -u www-data php artisan sync:all --year=2025
sudo -u www-data php artisan sync:tarif --year=2025
sudo -u www-data php artisan sync:tarif-ulp --year=2025
```

---

## Step 9: Configure Nginx

```bash
# Buat config
sudo nano /etc/nginx/sites-available/dashboard-pln
```

**Paste config ini:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name _;
    root /var/www/dashboard-pln/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Aktifkan config:**
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/dashboard-pln /etc/nginx/sites-enabled/

# Disable default site
sudo rm /etc/nginx/sites-enabled/default

# Test config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## Step 10: Setup Auto-Sync (Background Worker)

```bash
# Buat systemd service
sudo nano /etc/systemd/system/dashboard-pln-worker.service
```

**Paste:**
```ini
[Unit]
Description=Dashboard PLN Auto Sync Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/dashboard-pln
ExecStart=/usr/bin/php /var/www/dashboard-pln/artisan data:auto-sync --year=2025
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

**Aktifkan:**
```bash
sudo systemctl daemon-reload
sudo systemctl enable dashboard-pln-worker
sudo systemctl start dashboard-pln-worker

# Check status
sudo systemctl status dashboard-pln-worker
```

---

## Step 11: Setup UFW Firewall di Ubuntu

```bash
# Enable firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## ✅ SELESAI!

**Buka browser:** `http://YOUR_IP_ADDRESS`

Dashboard sudah online dan bisa diakses publik 24/7!

---

## Optional: Setup Domain & SSL (Gratis)

### A. Dapat Domain Gratis
- Freenom: https://www.freenom.com/ (gratis .tk, .ml, .ga)
- Atau beli domain murah: Niagahoster, Cloudflare

### B. Point Domain ke IP Server
- Buat A Record: `dashboard-pln.your-domain.com` → `YOUR_IP_ADDRESS`

### C. Install SSL (Let's Encrypt - Gratis)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d dashboard-pln.your-domain.com
```

Otomatis dapat HTTPS! 🎉

---

## Troubleshooting

### Error 500
```bash
# Check logs
sudo tail -f /var/www/dashboard-pln/storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

### Permission error
```bash
sudo chown -R www-data:www-data /var/www/dashboard-pln
sudo chmod -R 775 /var/www/dashboard-pln/storage
```

### Update code dari GitHub
```bash
cd /var/www/dashboard-pln
sudo -u www-data git pull
sudo -u www-data composer install --no-dev
sudo -u www-data php artisan migrate --force
sudo systemctl restart php8.2-fpm
```

---

## Monitoring

### Check worker status
```bash
sudo systemctl status dashboard-pln-worker
```

### View worker logs
```bash
sudo journalctl -u dashboard-pln-worker -f
```

### Restart services
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart dashboard-pln-worker
```

---

**Biaya: $0/bulan - GRATIS SELAMANYA!** 🎉

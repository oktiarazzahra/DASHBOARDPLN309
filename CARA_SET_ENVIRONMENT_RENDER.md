# 🚀 CARA SET ENVIRONMENT VARIABLES DI RENDER (WAJIB!)

## ⚠️ PENTING: Tanpa environment variables, dashboard akan error dan data kosong!

Dashboard saat ini menampilkan data kosong karena **environment variables belum di-set di Render**.

---

## 📝 LANGKAH 1: Login ke Render Dashboard

1. Buka browser, pergi ke: **https://dashboard.render.com**
2. Login dengan akun Render Anda
3. Cari service bernama **"dashboard-pln-309"** (atau nama service Anda)
4. **Klik service tersebut**

---

## 🔑 LANGKAH 2: Tambahkan Environment Variables

Di dalam service dashboard, cari tab **"Environment"** di sidebar kiri, lalu klik.

### ✅ Environment Variables yang WAJIB ditambahkan:

Klik tombol **"Add Environment Variable"** dan tambahkan **4 variables** berikut satu per satu:

---

#### 1️⃣ **APP_KEY** (Application Encryption Key)

| Key | Value |
|-----|-------|
| `APP_KEY` | `base64:KxW1pKqbOsCcey5vppSQTk0bOvI+yCWdxQkXLobbw1o=` |

> ⚠️ **PENTING**: Copy EXACTLY seperti di atas (termasuk `base64:`)

---

#### 2️⃣ **APP_URL** (URL Dashboard Anda)

| Key | Value |
|-----|-------|
| `APP_URL` | `https://dashboardpln309.onrender.com` |

> 💡 Ganti URL sesuai dengan URL dashboard Anda di Render

---

#### 3️⃣ **GOOGLE_SPREADSHEET_ID** (ID Google Sheets)

| Key | Value |
|-----|-------|
| `GOOGLE_SPREADSHEET_ID` | `1KDkcPM3lT6lR1D5Cv9YPeYNPTu7DVLaBt4m7Guk5vZU` |

> 💡 **Cara dapat ID**: Buka Google Sheets Anda, lihat URL di browser.  
> URL: `https://docs.google.com/spreadsheets/d/`**`1KDkcPM3lT6lR...`**`/edit`  
> Copy bagian setelah `/d/` sampai sebelum `/edit`

---

#### 4️⃣ **GOOGLE_SERVICE_ACCOUNT_BASE64** (Service Account Credentials)

| Key | Value |
|-----|-------|
| `GOOGLE_SERVICE_ACCOUNT_BASE64` | *Copy dari file `render_secret_base64.txt`* |

> 💡 Buka file **`render_secret_base64.txt`** yang ada di project Anda  
> Copy **SELURUH ISI FILE** (1 baris panjang yang dimulai dengan `ewogICJ0...`)  
> Paste ke value

**File render_secret_base64.txt** berisi:
```
ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsCiAgInByb2plY3RfaWQiOiAiZGFzaGJvYXJkcGxuMzA5IiwKICAicHJpdmF0ZV9rZXlfaWQiOiAiNjJjYWEwNTAxZmFlODI0NWIxYzNiYzFkNTQwNjM5YTdmMjI3MGY0MSIsCiAgInByaXZhdGVfa2V5IjogIi0tLS0tQkVHSU4gUFJJVkFURSBLRVktLS0tLVxuTUlJRXZBSUJBREFOQmdrcWhraUc5dzBCQVFFRkFBU0NCS1l3Z2dTaUFnRUFBb0lCQVFDODdDL29hN25nRmIwSlxucGtDbXV4bTZNeFNCbjU5empyTlRRSXh3V0s0dER0S042T003bFlzNDVjSm54R1JuOXRXM29uMFc3VUJmNGgyZ1xuRWpwTm9uU1dlY0RBOVZVVG9KL1dLem1wa3I2dHRwZ0JILzRNdkJTbk9JRVh2QXE3cUEzUE1LQm8rS252RVY0R1xuaWczRTRqOGp0OGl6S0IzbjF6ZGhvYjhINW5ubmpadVo1NG9SVFNBZTZGYXJ2bHRKclpNUUJNeVljVHQweW1uUFxuU09oQ2ljbHV5bWVCZzFycmdKbHZtdVIrS0Q3UVMrNCtoZWlRaitjMzZ6YlNQdU1GN3FFZWVuaUtZTEloM3ZNYlxuTjJFaDJJb2FoMjRYQUthc2htRHpVNXJIWVlxcDE1bVRpMC9lRXhnSnJoVERhbm92RVc5U1E5YnVYTDlUSmM5WFxuemRrSTM5OTNBZ01CQUFFQ2dnRUFBV3ZSYkhFckE4bUdFNFdCcFA5eFVScERSRHBjSzdWZUdmazVWVHNGakdjNVxuSkVJbE8zU0RpSmtKM2xmMjNXVlk5RG12c1RWMXI0TDI4QlpUQS9SOTVnZmljRkVvSTY2b3BTYk1Gejk0V2J3dVxuNW90RUVQcDFPVkRPZjh3MVZIWU9MSW5VR3hZTHlUV2JQSGRiWlNqQ2l0VStHRUxyL3F0NnlpdzZVS2ZtTU55M1xuZ3c2cUV4KzVtcWNwVXZWZU5VeHlHQmdacmhDNUdZQTNqR2QzZWlUUVdKQ0xsT05oQWZzcVczWWxhMEtxMlZTMVxuS1ZoZEV5OWtKdVNkMXJFS0lXb3BmcW1CUU90UnJpRUlINkVEYytISVFhMVJOdDcySDNXYlB6dmJha2Z2cVhlM1xuclIveEJwbXZvZk1kU3Z6cm53bFFnSGJBNU14bGRkM21hYnc4R3h2Y3NRS0JnUUQzVTl5WnJHamRxYnJ4UWM5TlxucjdJaENyQktjL3czNU9uYUMxbGpuRGM1VVlFNjdMdjYzVGtHR0t2Z3BRVU1XVTdRS0ozN0dPVEZyNjhyV1AydFxuVDlyeVFiUUg0bkJNd0VLcSs3NWE3UjNmUkhOOEp0dklQVTQzMTNDWEQyT3BCL3FOSkZ6QzZ0TjdzZ0lXdEFKdFxuWDNGeklOM0pMdlJhdmhUUk5ETnVXRjVxU1FLQmdRRERqQTEzZzZIdkxhUE5OSnZpSit3dXdUUnVVdnRyVExvY1xuS2psUTdNckhlcytXem5BeEluRHhBSGt6N2QwSjluQ290QUsySTlMd2tyMTdlZFpWSnB0Q2JTSGdGQ3RJRWNyaVxuaGJLZTdQS3RNYkRTS2pZQ0M2aituSnlUZkJmZjNXSG9LdFl5VU4zZDlhbEZKWi9RbVNLUHg5cTBKWTJsV3JpU1xuVWdCR3JuSDd2d0tCZ0VUSFRIU0R4cEhGN28vQzNsUmJSS3o5blBMSGVGOU8wRjFyaElzMzJQK2VrOUtBSFBKeFxuYkxjdVN6WG9qaFBXRGgzQy9kUGpJMU42UEx0UVB4TmdQcUhaOWFldnA4MGdOaW40WWdKRktHWjdVYVkzNUN5MlxuSjdkTnVTaTVCZHp1dEJWbUJFbk1KYzJqUGdOMFFheUt1ekRwOWVOSnBIaExuZHhqcU4zSncwL0pBb0dBSVNUSFxuMDQzNEVIWUg0V3dkVDhPOGdHQTI1c3doMlJuMElYbjJwUVM5aWZvQzNXaTRFMXZuRUxJbjJPbGtJYUFua0RDU1xubFJTVldEbEc3SHJHSFVDaDZDeGZKWC9GcGoxOXljOG5hMUIyQVd6K3IyQ0FMdkNUQURURFJTd3ZUNzVTTGV1MFxueW1weWNJQ0c1RFZ3VlFiZm1NY2pCbzJtNkhQci9NRHhVd21XUURrQ2dZQi9TUHFyaVJQczh2ZmZJUmVweTd3YlxuMHBWaW05dE51ek5BTThnNXVGWks1LzQ1WVJTUkZsVTVuWjJIc1Vxc2tobWhxMHA4NTdCYWZEd3FtVE5pWS91SFxuaHhSdmVqY0Q1T0hDUGdqcktac0RVbFVlS05ndGNPZnU4bzFoejRMdW0rOFo4ZncrUHJqcFlFdm5SR3BvY0ZBV1xuNXowaG4wSjNyOUVTeGloQkYwcHBudz09XG4tLS0tLUVORCBQUklWQVRFIEtFWS0tLS0tXG4iLAogICJjbGllbnRfZW1haWwiOiAiZGFzaGJvYXJkcGxuQGRhc2hib2FyZHBsbjMwOS5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsCiAgImNsaWVudF9pZCI6ICIxMTEwNDI3NDcyODI4MzIwMDk1NTAiLAogICJhdXRoX3VyaSI6ICJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20vby9vYXV0aDIvYXV0aCIsCiAgInRva2VuX3VyaSI6ICJodHRwczovL29hdXRoMi5nb29nbGVhcGlzLmNvbS90b2tlbiIsCiAgImF1dGhfcHJvdmlkZXJfeDUwOV9jZXJ0X3VybCI6ICJodHRwczovL3d3dy5nb29nbGVhcGlzLmNvbS9vYXV0aDIvdjEvY2VydHMiLAogICJjbGllbnRfeDUwOV9jZXJ0X3VybCI6ICJodHRwczovL3d3dy5nb29nbGVhcGlzLmNvbS9yb2JvdC92MS9tZXRhZGF0YS94NTA5L2Rhc2hib2FyZHBsbiU0MGRhc2hib2FyZHBsbjMwOS5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsCiAgInVuaXZlcnNlX2RvbWFpbiI6ICJnb29nbGVhcGlzLmNvbSIKfQo=
```

> ⚠️ **PENTING**: Copy SELURUH string panjang di atas (jangan ada spasi atau enter/newline tambahan)

---

## 💾 LANGKAH 3: Simpan dan Deploy

1. Setelah keempat environment variables ditambahkan, klik **"Save Changes"**
2. Render akan **otomatis redeploy** service Anda (tunggu 3-5 menit)
3. Lihat progress di tab **"Logs"** atau **"Events"**

---

## 🔄 LANGKAH 4: Tunggu Auto-Sync atau Manual Sync

### Opsi A: Auto-Sync (Otomatis)
Setelah deploy selesai, entrypoint.sh akan otomatis sync data tahun 2025 dan 2026 dari Google Sheets.

**Cara cek progress:**
1. Di Render Dashboard → Service Anda → Tab **"Logs"**
2. Cari log yang menunjukkan:
   ```
   🔄 Auto-syncing data from Google Sheets...
   ✅ Data sync complete!
   ```

### Opsi B: Manual Sync (Dari Dashboard)
1. Buka dashboard Anda di browser: `https://dashboardpln309.onrender.com`
2. Klik tombol **"Sync"** di pojok kanan atas navbar
3. Tunggu beberapa menit
4. Refresh halaman

---

## ✅ CHECKLIST Environment Variables

Pastikan semua 4 variables ini sudah di-set di Render:

- [ ] **APP_KEY** → `base64:KxW1pKqbOsCcey5vppSQTk0bOvI+yCWdxQkXLobbw1o=`
- [ ] **APP_URL** → `https://dashboardpln309.onrender.com` (sesuaikan)
- [ ] **GOOGLE_SPREADSHEET_ID** → ID dari Google Sheets Anda
- [ ] **GOOGLE_SERVICE_ACCOUNT_BASE64** → Isi dari `render_secret_base64.txt`

**TIDAK PERLU** set lagi (sudah default di render.yaml):
- ~~DB_CONNECTION~~ (sudah: `sqlite`)
- ~~DB_DATABASE~~ (sudah: `/var/www/html/storage/database.sqlite`)
- ~~APP_ENV~~ (sudah: `production`)
- ~~APP_DEBUG~~ (bisa set `true` untuk debug, `false` untuk production)
- ~~AUTO_SYNC_ON_START~~ (sudah: `true`)

---

## 🐛 Troubleshooting

### ❌ Error: "No application encryption key has been specified"
→ APP_KEY belum di-set atau salah format. Pastikan dimulai dengan `base64:`

### ❌ Data masih kosong setelah sync
→ Cek:
1. GOOGLE_SPREADSHEET_ID apakah benar?
2. GOOGLE_SERVICE_ACCOUNT_BASE64 sudah di-set?
3. Service account sudah diberi akses ke Google Sheets? (share sheets ke email: `dashboardpln@dashboardpln309.iam.gserviceaccount.com`)

### ❌ Error 500 setelah deploy
→ Cek Render Logs untuk detail error. Biasanya karena environment variables salah atau belum di-set.

---

## 📧 Service Account Email untuk Google Sheets Access

**WAJIB**: Buka Google Sheets Anda, klik **Share**, lalu tambahkan email ini dengan akses **Viewer** atau **Editor**:

```
dashboardpln@dashboardpln309.iam.gserviceaccount.com
```

Tanpa ini, sync akan gagal dengan error "Permission denied".

---

## 🎯 Hasil Akhir

Setelah semua environment variables di-set dan deploy selesai:

1. ✅ Dashboard bisa diakses tanpa error 500
2. ✅ Data 2025 dan 2026 muncul otomatis
3. ✅ Filter ULP dan Bulan berfungsi
4. ✅ Sync manual via tombol "Sync" berfungsi

---

**Selamat! Dashboard Anda siap digunakan! 🎉**

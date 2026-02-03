## 📋 Contoh Struktur Data Spreadsheet

### Format Header (Baris 1)

**Bahasa Inggris:**
```
location | status | voltage | current | power | energy | alert_type | description | recorded_at
```

**Bahasa Indonesia:**
```
lokasi | status | tegangan | arus | daya | energi | tipe_alert | deskripsi | tanggal
```

### Contoh Data

#### Sheet dengan Bahasa Inggris:

| location | status   | voltage | current | power | energy | alert_type | description        | recorded_at       |
|----------|----------|---------|---------|-------|--------|------------|-------------------|-------------------|
| Gardu A  | normal   | 220.5   | 10.2    | 2.25  | 100.5  |            | Normal operation  | 2026-01-29 08:00 |
| Gardu B  | normal   | 219.8   | 11.5    | 2.53  | 110.2  |            | Operating well    | 2026-01-29 08:15 |
| Gardu C  | warning  | 215.0   | 15.5    | 3.33  | 150.2  | warning    | High current      | 2026-01-29 08:30 |
| Gardu D  | normal   | 221.2   | 9.8     | 2.17  | 95.3   |            | Stable            | 2026-01-29 08:45 |
| Gardu E  | critical | 190.0   | 20.0    | 3.80  | 200.0  | critical   | Low voltage       | 2026-01-29 09:00 |
| Gardu F  | normal   | 220.1   | 10.5    | 2.31  | 102.8  |            | All systems OK    | 2026-01-29 09:15 |
| Gardu G  | warning  | 214.5   | 16.2    | 3.47  | 155.6  | warning    | Approaching limit | 2026-01-29 09:30 |
| Gardu H  | normal   | 219.5   | 11.0    | 2.41  | 108.5  |            | Normal            | 2026-01-29 09:45 |
| Gardu I  | normal   | 220.8   | 10.8    | 2.38  | 106.2  |            | Good condition    | 2026-01-29 10:00 |
| Gardu J  | critical | 185.0   | 22.0    | 4.07  | 210.5  | critical   | Very low voltage  | 2026-01-29 10:15 |

#### Sheet dengan Bahasa Indonesia:

| lokasi       | status   | tegangan | arus  | daya | energi | tipe_alert | deskripsi           | tanggal           |
|--------------|----------|----------|-------|------|--------|------------|---------------------|-------------------|
| Gardu A      | normal   | 220.5    | 10.2  | 2.25 | 100.5  |            | Operasi normal      | 2026-01-29 08:00 |
| Gardu B      | normal   | 219.8    | 11.5  | 2.53 | 110.2  |            | Berjalan baik       | 2026-01-29 08:15 |
| Gardu C      | warning  | 215.0    | 15.5  | 3.33 | 150.2  | warning    | Arus tinggi         | 2026-01-29 08:30 |
| Gardu D      | normal   | 221.2    | 9.8   | 2.17 | 95.3   |            | Stabil              | 2026-01-29 08:45 |
| Gardu E      | critical | 190.0    | 20.0  | 3.80 | 200.0  | critical   | Tegangan rendah     | 2026-01-29 09:00 |
| PLN 309 Unit1| normal   | 220.1    | 10.5  | 2.31 | 102.8  |            | Semua sistem OK     | 2026-01-29 09:15 |
| PLN 309 Unit2| warning  | 214.5    | 16.2  | 3.47 | 155.6  | warning    | Mendekati batas     | 2026-01-29 09:30 |
| Perulp A     | normal   | 219.5    | 11.0  | 2.41 | 108.5  |            | Normal              | 2026-01-29 09:45 |
| Perulp B     | normal   | 220.8    | 10.8  | 2.38 | 106.2  |            | Kondisi baik        | 2026-01-29 10:00 |
| Perulp C     | critical | 185.0    | 22.0  | 4.07 | 210.5  | critical   | Tegangan sangat rendah | 2026-01-29 10:15 |

### Penjelasan Kolom

1. **location/lokasi**: Nama lokasi gardu atau unit monitoring
2. **status**: Kondisi saat ini (`normal`, `warning`, `critical`)
3. **voltage/tegangan**: Tegangan dalam Volt (V)
4. **current/arus**: Arus dalam Ampere (A)
5. **power/daya**: Daya dalam kilowatt (kW)
6. **energy/energi**: Energi dalam kilowatt-hour (kWh)
7. **alert_type/tipe_alert**: Tipe alert (`warning`, `critical`, atau kosong jika OK)
8. **description/deskripsi**: Deskripsi kondisi atau catatan
9. **recorded_at/tanggal**: Waktu pencatatan (format: `YYYY-MM-DD HH:MM` atau `DD/MM/YYYY HH:MM`)

### Tips Mengisi Data

✅ **DO:**
- Gunakan format tanggal konsisten
- Isi semua kolom yang wajib (location, status, recorded_at)
- Gunakan status: `normal`, `warning`, atau `critical` (lowercase)
- Kosongkan kolom alert_type jika status normal

❌ **DON'T:**
- Jangan ubah nama header kolom
- Jangan tambah/hapus kolom tanpa update kode
- Jangan gunakan status selain 3 pilihan di atas
- Jangan kosongkan kolom location

### Formula Excel/Google Sheets (Optional)

Untuk auto-calculate power dari voltage dan current:
```
=C2*D2/1000
```
(dimana C2 = voltage, D2 = current, hasil dalam kW)

### Validasi Data

Gunakan Data Validation di Google Sheets untuk kolom status:
1. Pilih kolom status
2. Data > Data validation
3. Criteria: List of items
4. Items: `normal,warning,critical`

### Import Data Bulk

Untuk import banyak data sekaligus:
1. Siapkan data di Excel
2. Copy semua data (termasuk header)
3. Paste ke Google Sheets
4. Klik tombol "Sync Data" di dashboard

### Format Alternatif Tanggal

Dashboard support berbagai format tanggal:
- `2026-01-29 10:00`
- `29/01/2026 10:00`
- `29 Jan 2026 10:00`
- `2026-01-29T10:00:00`

---

**Note:** Setelah mengisi data, jangan lupa share spreadsheet dengan service account dan klik "Sync Data" di dashboard!

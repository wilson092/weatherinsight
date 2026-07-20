**Nama:** Wilson Fabian  
**NIM:** 2024080198  
**Domain:** [weatherinsight.online](https://weatherinsight.online)  
**Mata Kuliah:** Pemrograman Web CR002

---

# 🌤️ WeatherInsight

Sistem monitoring dan analisis risiko cuaca berbasis web yang mengubah data cuaca mentah dari **OpenWeather API** menjadi informasi yang mudah dipahami — lengkap dengan kategori risiko, rekomendasi aktivitas, dan peringatan dini cuaca ekstrem.

---

## ✨ Fitur Utama

- 🔍 **Pencarian Kota** — cuaca real-time & prakiraan 5 hari
- 🧠 **Analisis Risiko Otomatis** — klasifikasi cuaca (Rendah/Sedang/Tinggi) berbasis Weather Rules
- 🗺️ **Peta Cuaca Interaktif** — visualisasi spasial kota terpantau
- 📊 **Dashboard Admin** — statistik, log API, dan log aktivitas
- ⚖️ **Perbandingan Cuaca** antar kota
- 🏆 **Weather Leaderboard** — papan peringkat cuaca ekstrem
- 🕒 **Riwayat Cuaca** tersimpan otomatis
- ⚙️ **Weather Rules & Risk Category** — dikonfigurasi admin tanpa ubah kode
- 👤 **Role Management** — kontrol akses admin & pengguna

---

## 🛠️ Tech Stack

| Kategori | Teknologi |
|---|---|
| Backend | Laravel 12 |
| Admin Panel | Filament v3 |
| Frontend Reaktif | Livewire |
| Styling | Tailwind CSS |
| Database | MariaDB |
| Peta | Leaflet.js |
| Sumber Data | OpenWeather API |
| Environment | Docker |

---

## 🚀 Menjalankan Project

**1. Clone repository**
```bash
git clone https://github.com/wilson092/weatherinsight.git
cd weatherinsight
```

**2. Jalankan container Docker**
```bash
docker compose up -d
```

**3. Jalankan migration database**
```bash
docker compose exec php php artisan migrate
```

**4. Jalankan seeder**
```bash
docker compose exec php php artisan db:seed
```

**5. Ambil data cuaca awal**
```bash
docker compose exec php php artisan weather:fetch
```

**6. Menghentikan container**
```bash
docker compose down
```

Aplikasi dapat diakses melalui browser sesuai konfigurasi Nginx pada project.

---

## 📁 Struktur Project

```
weatherinsight/
├── docs/          # Dokumentasi
├── nginx/         # Konfigurasi web server
├── php/           # Konfigurasi PHP
├── db/conf.d/     # Konfigurasi database
├── src/           # Source code Laravel
├── docker-compose.yml
└── readme.md
```

---


# Dashboard Final Polish

## Objective

Lakukan final polish pada frontend WeatherInsight.

Business logic sudah benar.

JANGAN mengubah:

- Route
- Controller
- Service
- Rule Engine
- Database
- API
- Filament Resource

Fokus hanya pada UI/UX dan data presentation.

---

# Tech Stack

- Laravel 11
- Blade
- TailwindCSS
- AlpineJS
- Filament v3
- MariaDB
- Docker
- Leaflet
- OpenWeather API

Custom Command:

```bash
dcu
dcd
dca
dcm
```

---

# Current Condition

Dashboard sudah berfungsi.

Semua data berasal dari backend.

Tidak boleh menggunakan dummy data.

---

# Task 1 — Current Weather Card

Rapikan Current Weather Card.

## Hapus card berikut

- Visibility
- UV Index
- Feels Like

Karena data sering kosong dan membuat tampilan tidak rapi.

Sisakan hanya:

- Humidity
- Pressure
- Wind

Ketiga card tersebut harus memenuhi lebar card secara proporsional.

Weather icon tetap di kanan atas.

Ukuran temperature tetap besar.

---

# Task 2 — Weather Map

Perbaiki tampilan Weather Map.

## Sunrise & Sunset

Saat ini tampil:

Unavailable

atau

N/A

Jika backend tidak mengirim data Sunrise/Sunset:

- sembunyikan field tersebut
- jangan tampilkan placeholder "Unavailable"

Location Information cukup menampilkan:

- City
- Country
- Latitude
- Longitude
- Timezone

Card menjadi lebih ringkas.

---

Perbesar ukuran Leaflet Map sekitar 10%.

Popup tetap sama.

---

# Task 3 — Weather History

Perbaiki halaman History.

Saat ini terdapat pencarian kota.

Hapus filter City.

History harus menampilkan seluruh data weather_histories.

Kolom City tetap tampil di tabel.

Filter yang dipertahankan:

- Date
- Refresh

Sehingga user dapat melihat history semua kota tanpa harus mencari kota terlebih dahulu.

Jangan mengubah query yang menyebabkan data hilang.

Gunakan seluruh data history.

---

# Task 4 — Weather Alert Center

Saat tidak ada alert:

Tampilkan:

✅ No Active Weather Alerts

All monitored weather parameters are within configured thresholds.

Last Updated:
{{ current update time }}

Card jangan terlalu kosong.

---

Saat ada alert:

Tampilkan setiap alert aktif.

Minimal berisi:

- Icon
- Rule Name
- Current Value
- Threshold
- Severity
- Trigger Time

Gunakan warna:

Green

Yellow

Orange

Red

berdasarkan Risk Category.

---

# Task 5 — Recommendation

Tambahkan Recommended Action.

Contoh:

Recommended Actions

• Drink enough water

• Avoid prolonged outdoor activity

• Wear protective clothing

• Monitor weather updates

Semua recommendation tetap berasal dari Rule Engine atau Risk Category.

Jangan hardcode.

---

# Task 6 — Leaderboard

Leaderboard saat ini terlalu kosong.

Perbesar visual.

Tambahkan ranking yang lebih informatif.

Misalnya:

Rank

City

Temperature

Humidity

Wind

Risk

Tanpa mengubah service.

---

# Task 7 — Comparison

Polish Comparison Summary.

Gunakan badge atau highlight.

Contoh:

+2°C Warmer

12% More Humid

0.7 m/s Stronger Wind

Safer Risk Level

Jangan mengubah logic.

Hanya presentation.

---

# Task 8 — General UI

Perbaiki spacing.

Gunakan tinggi card yang konsisten.

Hilangkan area kosong yang tidak perlu.

Pastikan seluruh dashboard terasa seperti aplikasi SaaS modern.

---

# Constraints

Jangan install package baru.

Jangan membuat component baru jika tidak diperlukan.

Gunakan component Blade yang sudah ada.

Gunakan TailwindCSS yang sudah dipakai project.

---

# Workflow

Sebelum coding:

1. Analisis file yang akan diubah.
2. Sebutkan alasan perubahan.
3. Setelah itu implementasikan satu per satu.

Jangan melakukan refactor besar.

Utamakan perubahan kecil namun berdampak besar terhadap UI.
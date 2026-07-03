# WeatherInsight Dashboard UI Redesign

## Role

Bertindak sebagai Senior Laravel Developer dan UI Engineer.

Fokus pada:
- Laravel 11
- Blade
- TailwindCSS
- AlpineJS
- Filament v3
- Livewire (admin only)
- Docker
- MariaDB

Jangan membuat project baru.
Jangan mengubah arsitektur project.

---

# Tech Stack

- Laravel 11
- PHP 8.3
- Blade
- TailwindCSS
- AlpineJS
- Filament v3
- Livewire
- MariaDB
- Docker
- OpenWeather API
- Leaflet

Custom command project:

```bash
dcu = docker compose up
dcd = docker compose down
dca = php artisan
dcm = custom make model,migration,seeder,controller,filament resource
```

---

# Project Architecture

Project menggunakan:

Controller
→ Service Layer
→ Rule Engine
→ Models
→ Blade Components
→ Views

Business Logic sudah berjalan dengan baik.

Jangan memindahkan logic ke Blade.

---

# Current Status

Semua fitur backend sudah berjalan.

Sudah tersedia:

- Current Weather
- Hourly Forecast
- 5 Day Forecast
- Risk Assessment
- Weather Recommendation
- Weather Alert
- Weather Map (Leaflet)
- Comparison
- Leaderboard
- History
- Rule Engine
- Admin Panel Filament

Semua data berasal dari backend.

---

# Objective

Redesign Dashboard agar tampilannya semirip mungkin dengan screenshot referensi.

Prioritas utama adalah:

- Layout
- UI
- UX
- Visual hierarchy

Bukan mengubah business logic.

---

# Rules

## Jangan Diubah

- Route
- Controller (kecuali diperlukan)
- Service
- Rule Engine
- Database
- API
- Filament Resource
- Query
- Business Logic

Jangan hardcode data.

Tetap gunakan data backend.

---

## Yang Boleh Diubah

- Blade View
- Blade Component
- Tailwind Class
- Layout
- Spacing
- Typography
- Animation
- Card Design
- Responsive Layout

---

# Design Style

Gunakan style modern SaaS.

Karakter visual:

- Dark Theme
- Glassmorphism ringan
- Cyan Accent
- Blue Gradient
- Rounded 2xl / 3xl
- Soft Shadow
- Thin Border
- Smooth Hover
- Clean Typography

---

# Target Layout

## Navbar

Tetap gunakan navbar sekarang.

Isi:

- Logo
- Dashboard
- Comparison
- Leaderboard
- History
- Search
- User
- Detect Location

---

## Hero

### Left

Current Weather Card

Berisi:

- Kota
- Tanggal
- Jam
- Suhu besar
- Weather Icon
- Description
- Humidity
- Pressure
- Wind
- Visibility
- UV Index
- Feels Like

### Right

Hourly Forecast

Atas:

Line Chart

Bawah:

Forecast Card horizontal scroll.

---

## Section 2

### Left

5 Day Forecast

Card horizontal.

### Right

Weather Risk Assessment

Isi:

- Circular Progress
- Risk Score
- Risk Level
- Weather Insight
- Risk Factors

Semua berasal dari Rule Engine.

---

## Section 3

### Left

Weather Map

Map lebih besar.

Di samping map tampilkan:

- City
- Country
- Latitude
- Longitude
- Timezone
- Sunrise
- Sunset

### Right

Weather Alert Center

Jika ada alert:

- Icon
- Title
- Description
- Severity Color

Jika tidak ada:

No Active Weather Alerts.

Di bawahnya:

Weather Recommendation

Isi:

- Recommendation
- Insight
- Current Risk

---

# UI Guidelines

Gunakan:

- rounded-3xl
- shadow-xl
- border-white/10
- bg-slate-900
- bg-slate-800
- hover:border-cyan-400
- transition duration-300

Spacing harus lebih lega.

Semua card memiliki tinggi yang konsisten.

---

# Responsive

Desktop:

2 Column

Tablet:

2 Column

Mobile:

1 Column

Tidak boleh overflow.

---

# Performance

Jangan install package baru.

Tetap gunakan:

- Blade
- Tailwind
- Alpine
- Leaflet
- Chart yang sudah ada

---

# Workflow

Sebelum coding:

1. Analisis struktur dashboard.
2. Identifikasi Blade Component yang digunakan.
3. Sebutkan file yang akan diubah.
4. Jelaskan alasan perubahan.

Jangan langsung coding.

Setelah analisis selesai, implementasikan bertahap.

Urutan:

1. Navbar
2. Hero
3. Hourly Forecast
4. Forecast
5. Risk Assessment
6. Weather Map
7. Alert Center
8. Recommendation
9. Responsive
10. Final Polish

---
hasil akhir seperti gambar yang saya kirim
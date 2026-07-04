markdown# Prompt: Revert Fitur Minute Forecast & Kembalikan Endpoint Forecast ke Versi Stabil

## Konteks
Fitur **"Minute Forecast - Precipitation"** yang ditambahkan pada task redesign sebelumnya menyebabkan bug: seluruh data **Hourly Forecast** dan **Daily/5-Day Forecast** hilang (muncul pesan "Forecast data is not available" dan "Hourly forecast data is currently unavailable"). Ini terjadi karena implementasi fitur tersebut mengganti endpoint API dari `/data/2.5/forecast` (gratis) ke `/data/3.0/onecall` (One Call API 3.0), yang membutuhkan subscription terpisah dan tidak didukung oleh API key saat ini.

**Keputusan**: Hapus/batalkan fitur Minute Forecast sepenuhnya. Kembalikan sistem forecast ke endpoint lama yang stabil.

## Yang Harus Dilakukan

### 1. Hapus Fitur Minute Forecast dari UI
- Hapus komponen/panel "Minute Forecast - Precipitation" dari Weather Map (termasuk timeline horizontal, legenda warna intensitas hujan, dan card di pojok kiri bawah peta).
- Hapus juga referensinya di Blade template, CSS, dan JS terkait jika ada.

### 2. Revert Endpoint API ke `/data/2.5/forecast`
- Kembalikan seluruh request forecast (hourly & daily) untuk memakai endpoint lama:
  - `https://api.openweathermap.org/data/2.5/forecast?q={city}&appid={key}&units=metric`
- Endpoint ini mengembalikan data forecast 5 hari dengan interval 3 jam — cukup untuk kebutuhan Hourly Forecast (ambil beberapa slot terdekat) dan Daily Forecast (kelompokkan per hari, ambil suhu min/max).
- Hapus semua kode yang memanggil `/data/3.0/onecall` atau `exclude=minutely,alerts` dsb — pastikan tidak ada sisa request ke One Call API di controller/service manapun.

### 3. Perbaiki Parsing Data agar Sesuai Struktur Response `/forecast`
- Struktur response `/data/2.5/forecast` berbeda dari One Call API — field-nya ada di `list[]` (array per 3 jam, total 40 item untuk 5 hari), bukan `hourly[]`/`daily[]`.
- Perbaiki service/helper yang memproses response ini:
  - **Hourly Forecast**: ambil 8 item pertama dari `list[]` (= 24 jam ke depan, interval 3 jam).
  - **Daily Forecast (5 Day)**: kelompokkan `list[]` berdasarkan tanggal (`dt_txt`), ambil suhu tertinggi & terendah per hari, dan ambil salah satu kondisi cuaca dominan (misalnya dari data jam 12:00 siang) untuk representasi ikon & deskripsi harian.
- Simpan hasil parsing ini ke variable yang dikirim ke Blade/Livewire (`$hourlyForecast`, `$dailyForecast`), pastikan tidak null/empty sehingga tidak lagi menampilkan pesan fallback "tidak tersedia".

### 4. Tampilkan Kembali Section yang Sempat Hilang
- **Hourly Forecast card**: tampilkan grafik garis + list card per jam seperti versi sebelumnya (sudah pernah bagus, jangan diubah stylingnya, cukup pastikan datanya terisi lagi).
- **5 Day Forecast / Day Tabs**: kembalikan tampilan card per hari (atau tab hari jika itu yang diminta sebelumnya) dengan data suhu min/max, ikon, dan deskripsi cuaca yang benar dari hasil parsing baru.

### 5. Bersihkan Kode Sisa
- Hapus semua variable, method, atau import yang khusus dibuat untuk fitur Minute Forecast (termasuk kemungkinan migration/tabel `minutely_forecasts` jika sempat dibuat — hapus migration-nya atau buat migration baru untuk drop table jika sudah sempat dijalankan di database).
- Pastikan tidak ada dead code atau referensi ke fitur yang sudah dihapus ini di file manapun.

## Verifikasi Setelah Fix
- [ ] Hourly Forecast menampilkan data 8 slot jam ke depan dengan grafik dan suhu yang benar.
- [ ] 5 Day Forecast / Day Tabs menampilkan 5 hari ke depan dengan suhu min/max dan ikon cuaca yang sesuai.
- [ ] Tidak ada lagi pesan "Forecast data is not available" atau "Hourly forecast data is currently unavailable" selama API key valid dan kota ditemukan.
- [ ] Panel "Minute Forecast - Precipitation" sudah tidak muncul lagi di Weather Map.
- [ ] Coba search beberapa kota berbeda (Jakarta, Bandung, Surabaya) — pastikan forecast konsisten muncul untuk semua kota.
- [ ] Cek tabel `api_logs` — request forecast tercatat dengan `status_code` 200 dan endpoint yang benar (`/data/2.5/forecast`).
- [ ] Fitur lain (Current Weather, Weather Map, Weather Risk Assessment, Weather Alert Center, Weather Recommendation) tetap berjalan normal, tidak terdampak perubahan ini.


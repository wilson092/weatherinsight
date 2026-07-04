# Prompt: Fix Bug Timezone Hourly Forecast & Bug Toggle °C/°F Tidak Konsisten

## Konteks
Dashboard menampilkan 2 bug nyata di section "Hourly Forecast" (screenshot `current-dashboard.png` terlampir):

1. **Data jam tidak masuk akal** — misal jam "06:00" menampilkan suhu 37°C, padahal itu adalah suhu yang wajar untuk siang hari (jam 12:00-15:00), bukan jam 6 pagi. Ini indikasi kuat bahwa data dari OpenWeather API (yang dikembalikan dalam **UTC**) belum dikonversi dengan benar ke timezone lokal kota (WIB / UTC+7 untuk Jakarta), atau konversi timezone-nya terbalik/dobel.
2. **Toggle °C/°F tidak konsisten** — saat diubah ke Fahrenheit, card cuaca utama dan tab hari (Today/Sat/Sun/dst) berubah unit dengan benar, tapi **Hourly Forecast tetap menampilkan Celsius**, tidak ikut berubah.

## A. Fix Timezone Hourly Forecast (WAJIB)

### Root Cause yang Harus Dicek
- Response dari `/data/2.5/forecast` mengembalikan field `dt` (Unix timestamp UTC) dan `dt_txt` (string UTC, contoh "2026-07-04 06:00:00" — ini WAKTU UTC, BUKAN waktu lokal).
- Response juga mengembalikan field `city.timezone` (offset dalam detik dari UTC, contoh untuk Jakarta = 25200 detik = +7 jam).
- **Bug kemungkinan besar**: sistem saat ini menampilkan `dt_txt` APA ADANYA (masih UTC) sebagai jam lokal, tanpa menambahkan offset `city.timezone`. Ini menyebabkan semua jam bergeser 7 jam dari yang seharusnya (WIB), sehingga data siang hari (suhu tinggi) tertampil seolah-olah jam pagi.

### Perbaikan yang Harus Dilakukan
- Ambil `city.timezone` (offset detik) dari response API untuk kota yang sedang ditampilkan.
- Untuk SETIAP item forecast, konversi timestamp dari UTC ke waktu lokal: `local_time = dt (UTC) + timezone_offset`.
- Gunakan hasil konversi ini untuk SEMUA tampilan jam di Hourly Forecast (label jam di chart, label jam di card per-slot, label jam di sumbu grafik).
- Pastikan konversi ini konsisten dengan jam yang ditampilkan di card utama ("Saturday, 04 Jul 2026 — 20:55") dan di popup Weather Map — semua harus merujuk ke waktu lokal kota yang sama, tidak boleh campur UTC dan lokal.
- **Test verifikasi**: cek manual — jika sekarang jam lokal Jakarta adalah 20:55 WIB, maka slot pertama di Hourly Forecast harus mulai dari jam terdekat setelah 20:55 (misal 21:00), BUKAN mulai dari jam yang jauh berbeda seperti sekarang.

## B. Ubah Rentang Hourly Forecast Jadi Rolling 24 Jam dari Waktu Sekarang

- Rentang tampilan Hourly Forecast harus **rolling 24 jam ke depan dari jam saat ini** (real-time), bukan fix dari tengah malam ke tengah malam.
- Contoh: jika sekarang jam lokal 21:00 (hari ini), maka Hourly Forecast menampilkan slot mulai ~21:00/22:00 hari ini sampai ~21:00/22:00 besok (24 jam ke depan, mengikuti interval data yang tersedia dari API — interval 3 jam untuk endpoint `/forecast`).
- Pastikan ini konsisten setiap kali halaman di-refresh atau kota diganti — selalu dihitung ulang berdasarkan waktu saat request dilakukan (real "now" di timezone lokal kota tersebut), bukan hardcoded ke jam tertentu.

## C. Fix Toggle °C/°F Tidak Berlaku di Hourly Forecast (WAJIB)

### Root Cause yang Harus Dicek
- Kemungkinan komponen Hourly Forecast menyimpan/menampilkan nilai suhu dari variabel/state yang terpisah dari komponen current weather & tab hari, sehingga tidak ikut ter-update saat toggle unit berubah.
- Atau: konversi suhu (Celsius → Fahrenheit) dilakukan di satu tempat (misal saat fetch data awal) alih-alih dihitung ulang secara reaktif setiap kali unit toggle berubah.

### Perbaikan yang Harus Dilakukan
- Pastikan SEMUA komponen yang menampilkan suhu (Current Weather card, Day Tabs, Hourly Forecast, 5-Day/Weather Map popup jika ada) membaca dari **satu sumber state unit yang sama** (misal Livewire property `$unit` atau Alpine store global), bukan state lokal per-komponen yang terpisah.
- Konversi suhu (`celsiusToFahrenheit()`) harus dilakukan secara reaktif di level tampilan (computed property/getter), bukan disimpan sebagai nilai statis saat data pertama kali di-fetch — supaya begitu toggle diklik, SEMUA tempat yang menampilkan suhu langsung ikut berubah tanpa perlu re-fetch data dari API.
- Test: toggle ke Fahrenheit → refresh visual → pastikan Card Cuaca Utama, Day Tabs, DAN Hourly Forecast (baik grafik garis maupun angka di tiap slot card) semuanya berubah ke Fahrenheit secara bersamaan.

## Ketentuan Teknis
- Jangan ubah endpoint API (`/data/2.5/forecast` tetap dipakai).
- Buat helper/utility function terpusat untuk konversi timezone (`convertUtcToLocal($timestamp, $timezoneOffset)`) dan konversi suhu (`convertTemperature($celsius, $unit)`), pakai di semua tempat yang butuh, jangan duplikat logic berbeda-beda di tiap komponen.
- Pastikan perubahan ini tidak merusak fitur lain yang sudah stabil (Weather Map, Weather Risk Assessment, Weather Alert Center, Weather Recommendation, Day Tabs).

## Deliverable
- Fix konversi timezone di Hourly Forecast — jam yang ditampilkan harus waktu lokal yang benar, bukan UTC mentah.
- Ubah Hourly Forecast jadi rolling 24 jam dari waktu sekarang (real-time), bukan rentang statis.
- Fix toggle °C/°F agar berlaku di SEMUA komponen termasuk Hourly Forecast.
- Screenshot hasil akhir untuk verifikasi (termasuk screenshot setelah toggle ke Fahrenheit, pastikan semua section berubah unit).

## Checklist Verifikasi
- [ ] Jam di Hourly Forecast sudah dikonversi ke waktu lokal (WIB untuk Jakarta), tidak lagi UTC mentah.
- [ ] Suhu di setiap jam sekarang masuk akal secara logis (siang hari lebih panas dari malam/dini hari, sesuai pola cuaca normal).
- [ ] Hourly Forecast menampilkan rolling 24 jam dari waktu sekarang (bukan fix 00:00-00:00), dan slot pertama dekat dengan jam saat ini.
- [ ] Toggle °C/°F mengubah SEMUA tampilan suhu sekaligus: Current Weather, Day Tabs, DAN Hourly Forecast (grafik + card per-jam).
- [ ] Test dengan minimal 2 kota berbeda timezone (jika ada, misal WIB vs WITA/WIT) untuk memastikan konversi timezone benar-benar dinamis, bukan hardcoded +7 untuk semua kota.
- [ ] Fitur lain (Weather Map, Risk Assessment, Alert Center, Recommendation) tetap berfungsi normal, tidak terdampak perubahan ini.
# Prompt: Tampilkan Timezone & Waktu Lokal di Card Cuaca Dashboard

## Konteks
Saat ini card "Current Day Detail" di Dashboard menampilkan tanggal dan jam (misal "Wednesday, 08 Jul 2026" + jam di pojok kanan atas), tapi tidak menampilkan informasi **timezone** kota yang sedang dicari. Saya ingin menambahkan informasi timezone (misal "UTC+7" untuk Jakarta, "UTC+2" untuk Paris — offset ini berubah tergantung kota) beserta waktu lokal kota tersebut, sehingga user tahu persis di zona waktu mana kota itu berada, terutama berguna saat membandingkan kota-kota di timezone berbeda.

## Yang Harus Ditambahkan

### 1. Tampilkan Label Timezone di Card Cuaca
- Di card "Current Day Detail" (Dashboard), tambahkan label kecil timezone di dekat informasi tanggal/jam yang sudah ada — contoh posisi: di samping atau di bawah jam ("Wednesday, 08 Jul 2026 — 22:22 **(UTC+7)**") atau sebagai elemen kecil terpisah di pojok kanan atas card, berdekatan dengan jam yang sudah ada.
- Format timezone: "UTC+7" untuk Jakarta (WIB), "UTC+2" untuk Paris (waktu musim panas/CEST) atau "UTC+1" (musim dingin/CET) — format harus dinamis mengikuti offset aktual dari API, BUKAN hardcoded per kota (karena beberapa negara punya daylight saving time yang mengubah offset sepanjang tahun).

### 2. Sumber Data Timezone
- Ambil dari field `timezone` di response `/data/2.5/weather` (endpoint current weather) — field ini berupa **offset dalam detik** dari UTC (contoh: Jakarta = `25200` detik = +7 jam, Paris saat musim panas = `7200` detik = +2 jam).
- Konversi offset detik ini ke format tampilan "UTC+X" atau "UTC-X":
offset_jam = timezone_detik / 3600
format_display = "UTC" + (offset_jam >= 0 ? "+" : "") + offset_jam
  Contoh: `25200 / 3600 = 7` → tampil "UTC+7". Untuk offset yang bukan kelipatan jam penuh (misal India UTC+5:30), tampilkan dengan format "UTC+5:30" (offset dalam jam:menit, bukan dibulatkan).

### 3. Waktu Lokal yang Sudah Benar (Reuse dari Perbaikan Sebelumnya)
- Jam yang ditampilkan di card ini (pojok kanan atas, "22:22") **seharusnya sudah** menggunakan waktu lokal kota tersebut (hasil dari perbaikan konversi timezone yang sudah dikerjakan sebelumnya untuk Hourly Forecast) — pastikan konsisten, gunakan helper/service yang sama untuk konversi ini, jangan buat logic terpisah.
- Verifikasi: waktu yang ditampilkan di card ini HARUS sama persis dengan waktu yang dipakai sebagai acuan "jam sekarang" di Hourly Forecast (rolling 24 jam dari waktu sekarang) — kedua tempat ini harus merujuk ke satu sumber waktu lokal yang konsisten.

### 4. Terapkan di Semua Tempat yang Relevan
- Selain card cuaca utama, jika ada tempat lain yang menampilkan waktu kota (misal popup Weather Map), pertimbangkan menambahkan info timezone yang sama di sana juga untuk konsistensi — namun prioritas utama adalah card cuaca Dashboard.

## Ketentuan Teknis
- Gunakan field `timezone` yang sudah tersedia dari API `/data/2.5/weather` — TIDAK perlu API tambahan.
- Buat/reuse helper function terpusat untuk konversi (`formatUtcOffset($timezoneSeconds)`) yang mengembalikan string format "UTC+X" atau "UTC-X" (dengan handling untuk offset non-bulat seperti +5:30).
- Test dengan minimal 2-3 kota di timezone berbeda untuk verifikasi:
  - Jakarta (UTC+7)
  - Paris (UTC+1 atau +2 tergantung musim)
  - Kota dengan offset non-bulat jika ingin extra thorough (misal Mumbai, India — UTC+5:30) untuk memastikan format menit juga dihandle dengan benar.
- **TIDAK ADA migration baru** — field timezone tidak perlu disimpan permanen ke database, cukup ditampilkan real-time dari response API saat itu (atau simpan sementara di state Livewire, bukan kolom database baru).
- **TIDAK ADA perubahan Docker.**

## Deliverable
- Card cuaca Dashboard menampilkan info timezone (format "UTC+X") berdampingan dengan tanggal/jam yang sudah ada.
- Waktu lokal tetap akurat dan konsisten dengan Hourly Forecast.
- Screenshot hasil akhir untuk minimal 2 kota berbeda timezone (Jakarta & kota lain, misal Paris atau London) untuk membuktikan info timezone berubah sesuai kota.

## Checklist Verifikasi
- [ ] Label timezone ("UTC+7", dst) muncul di card cuaca Dashboard.
- [ ] Format timezone dinamis mengikuti offset aktual dari API, bukan hardcoded.
- [ ] Offset non-bulat (misal +5:30) ditampilkan dengan benar jika ditemui saat testing.
- [ ] Waktu lokal di card tetap konsisten dengan waktu di Hourly Forecast (tidak ada perbedaan/konflik).
- [ ] Test dengan minimal 2 kota berbeda timezone — info timezone berubah sesuai kota yang dicari.
- [ ] Tidak ada migration baru dibuat.
- [ ] Tidak ada perubahan Docker.
- [ ] Fitur lain (Weather Map, Risk Assessment, dst) tetap berfungsi normal.
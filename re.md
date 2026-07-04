# Prompt: Fix Bug Data Kosong Saat Klik Tab Hari + Tambah Suhu Max/Min di Tab

## Konteks
Ditemukan bug: saat pertama kali search kota (misal "Jakarta"), card "Current Day Detail" menampilkan Humidity, Pressure, Wind dengan benar. **Namun begitu user klik salah satu tab hari** (Sun, Mon, Tue, Wed, Thu — bukan "Today"), ketiga field tersebut berubah jadi **"N/A"** semua (screenshot `bug-tab-click.png` terlampir, dibandingkan `normal-state.png` saat masih di tab Today).

Selain itu, saya ingin menambahkan **suhu maksimum dan minimum** di setiap card tab hari (Today, Sun, Mon, dst) — saat ini tab hanya menampilkan 1 suhu tunggal (misal "31°"), padahal data 3-jam dari API memungkinkan dihitung suhu tertinggi & terendah per hari.

## A. Fix Bug: Humidity/Pressure/Wind Jadi N/A Saat Klik Tab Hari

### Root Cause yang Harus Dicek
- Endpoint `/data/2.5/forecast` mengembalikan `list[]` berisi data per 3 jam, dan SETIAP item punya field lengkap: `main.humidity`, `main.pressure`, `wind.speed`, bukan hanya `main.temp`.
- Kemungkinan besar bug ini terjadi karena: saat merender card "Current Day Detail" untuk tab SELAIN "Today", sistem hanya mengoper data suhu (untuk chart tab) tapi TIDAK mengoper/mapping field `humidity`, `pressure`, `wind` dari item forecast yang representatif untuk hari tersebut (misal data slot siang hari/jam 12:00-15:00 sebagai representasi kondisi hari itu).
- Cek method/function yang menangani event klik tab (`selectDay($date)` atau sejenisnya di Livewire component) — pastikan method ini benar-benar mengambil SATU item lengkap dari `list[]` (bukan cuma agregat suhu), lalu simpan ke property yang dipakai untuk render card detail (termasuk humidity, pressure, wind).

### Perbaikan yang Harus Dilakukan
- Saat user klik tab hari manapun, sistem harus:
  1. Filter `list[]` berdasarkan tanggal yang dipilih.
  2. Pilih 1 item representatif dari hari itu (rekomendasi: ambil slot jam paling mendekati siang hari, misal jam 12:00 atau 13:00 lokal, karena ini biasanya paling representatif untuk kondisi "siang hari").
  3. Dari item tersebut, ambil SEMUA field yang dibutuhkan: `main.temp` (suhu), `weather[0].main`/`description` (kondisi), `main.humidity`, `main.pressure`, `wind.speed` — bukan cuma suhu saja.
  4. Update semua field ini ke card "Current Day Detail" sekaligus (Humidity, Pressure, Wind harus terisi, tidak boleh N/A lagi).
- Pastikan ini berlaku untuk SEMUA tab (Today, Sun, Mon, Tue, Wed, Thu), tidak hanya sebagian.
- Test: klik setiap tab satu per satu, pastikan Humidity/Pressure/Wind selalu terisi angka valid (bukan N/A) di setiap tab.

## B. Tambah Suhu Maksimum & Minimum di Card Tab Hari

- Untuk setiap tab hari (Today, Sun, Mon, Tue, Wed, Thu), tambahkan tampilan suhu **max dan min** di bawah suhu utama yang sudah ada, mengikuti pola umum aplikasi cuaca (contoh format: "31° / 24°" atau "H:31° L:24°").
- Cara hitung: dari `list[]` yang di-filter berdasarkan tanggal hari tersebut (semua slot 3-jam dalam 1 hari, biasanya 8 slot), ambil `main.temp_max` tertinggi dan `main.temp_min` terendah dari seluruh slot tersebut (atau gunakan `main.temp` jika field `temp_max`/`temp_min` tidak konsisten, ambil nilai tertinggi dan terendah dari seluruh slot temp dalam hari itu).
- Tampilkan di card tab dengan format yang ringkas, tidak mengganggu ukuran card yang sudah ada — contoh layout: suhu utama besar tetap seperti sekarang, tambahkan baris kecil di bawahnya "31° / 24°" dengan ukuran font lebih kecil, warna sedikit lebih redup (`text-slate-400`).
- Pastikan perhitungan max/min ini konsisten dengan suhu yang ditampilkan saat tab tersebut diklik (card detail di bawahnya) — tidak boleh ada perbedaan angka yang membingungkan antara tab dan card detail.

## Ketentuan Teknis
- Gunakan data yang sudah tersedia dari response `/data/2.5/forecast` — tidak perlu tambahan API call.
- Buat helper/service method terpusat untuk "ambil data representatif per hari" (misal `getDayRepresentativeData($date, $forecastList)`) yang mengembalikan objek lengkap (temp, temp_max, temp_min, humidity, pressure, wind, weather condition) — pakai method ini baik untuk render tab maupun untuk render card detail saat tab diklik, supaya konsisten dan tidak ada logic terpisah yang bisa out-of-sync.
- Pastikan timezone tetap dihandle dengan benar (mengacu ke perbaikan timezone sebelumnya) saat mengelompokkan data per hari.

## Deliverable
- Fix bug Humidity/Pressure/Wind N/A saat klik tab hari manapun.
- Tambahkan suhu max/min di setiap card tab hari.
- Screenshot hasil akhir: klik beberapa tab berbeda, pastikan semua data (suhu utama, max/min, humidity, pressure, wind) terisi benar di setiap tab.

## Checklist Verifikasi
- [ ] Klik tab "Today" — Humidity, Pressure, Wind terisi benar (sudah OK sebelumnya, pastikan tetap OK).
- [ ] Klik tab "Sun" — Humidity, Pressure, Wind terisi angka valid, TIDAK N/A.
- [ ] Klik tab "Mon", "Tue", "Wed", "Thu" — sama, semua field terisi valid.
- [ ] Setiap card tab hari menampilkan suhu max/min tambahan (format "H° / L°" atau sejenis).
- [ ] Suhu max/min di tab konsisten dengan suhu yang muncul di card detail saat tab tersebut diklik.
- [ ] Perpindahan antar tab tidak memicu reload halaman (tetap real-time via Livewire).
- [ ] Fitur lain (search kota, toggle unit, weather map, dst) tetap berfungsi normal setelah perubahan ini.
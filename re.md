# Prompt: Audit Akurasi Data & Perbaiki Keterbacaan Hourly Forecast

## Konteks
Fitur timezone sudah berhasil (card menampilkan "UTC+02:00" untuk Paris, jam lokal 17:50 sudah benar). Namun Hourly Forecast masih terasa membingungkan (screenshot `paris-dashboard.png` terlampir):

1. **Keterbacaan buruk** — grafik garis di atas card per-jam tidak punya referensi visual yang jelas (tidak ada gridline, tidak ada pembeda siang/malam, angka suhu di atas grafik terasa mengambang tanpa terhubung jelas ke titik datanya di bawah).
2. **Kecurigaan data tidak akurat** — saat scroll ke jam-jam tertentu (misal sekitar 22:00), muncul suhu yang terasa tidak masuk akal dibanding jam-jam di sekitarnya (naik-turun tidak logis untuk pola suhu harian normal).

## A. Audit Akurasi Data Suhu Per Jam

### 1. Verifikasi Urutan Logis Suhu
- Cek SEMUA slot jam yang ditampilkan di Hourly Forecast (bukan cuma yang terlihat di layar awal, scroll penuh sampai slot terakhir) untuk kota Paris dan minimal 1 kota lain.
- Pastikan pola suhu mengikuti logika wajar: biasanya turun setelah matahari terbenam, mencapai titik terendah menjelang subuh, lalu naik lagi setelah matahari terbit — TIDAK ADA lonjakan aneh di tengah malam yang tidak sesuai pola ini.
- Jika ditemukan ada slot jam dengan suhu yang melompat tidak wajar (misal naik drastis di jam 22:00-23:00 padahal jam sebelum-sesudahnya turun), ini indikasi data API tidak ter-parsing dengan benar atau ada pergeseran index saat mapping `list[]` dari response `/data/2.5/forecast` ke tampilan.

### 2. Cek Ulang Sinkronisasi Timezone dengan Data Suhu
- Pastikan setelah konversi timezone (yang sudah diperbaiki sebelumnya untuk mengatasi error `InvalidTimeZoneException`), data suhu yang di-attach ke SETIAP slot jam juga ikut bergeser dengan benar sesuai jam yang sudah dikonversi — jangan sampai LABEL jam sudah benar (lokal) tapi NILAI suhu yang ditempelkan ke label tersebut masih dari index/urutan yang salah (tertukar antara data asli UTC dengan label yang sudah dikonversi ke lokal).
- Test spesifik: ambil response mentah dari API untuk Paris, cocokkan manual timestamp UTC → offset +2 jam → apakah suhu di jam lokal hasil konversi SAMA PERSIS dengan suhu yang ditampilkan di UI untuk jam tersebut.

## B. Perbaiki Keterbacaan Visual Grafik & Card Per-Jam

### 1. Tambahkan Gridline pada Grafik
- Tambahkan garis bantu vertikal tipis (`stroke-slate-700/30`) di setiap titik data pada grafik, sejajar dengan label jam di sumbu X — ini membantu mata mengaitkan titik data dengan label jamnya.

### 2. Tambahkan Tooltip Saat Hover
- Saat kursor/mouse diarahkan ke titik tertentu pada grafik garis, tampilkan tooltip kecil yang menunjukkan jam + suhu tepat di titik tersebut — ini akan sangat membantu verifikasi visual dan kenyamanan baca, terutama karena angka suhu di atas grafik saat ini terasa "mengambang" tanpa penghubung jelas ke titik data.

### 3. Beda Visual Siang vs Malam
- Beri sedikit shading/background berbeda pada area grafik yang merepresentasikan malam hari (misal jam 18:00-06:00 dengan overlay sedikit lebih gelap) vs siang hari — ini membantu user langsung menangkap pola "kenapa turun di sini, naik di situ" tanpa perlu menghitung manual.

### 4. Perjelas Alignment Angka Suhu dengan Titik Data
- Pastikan angka suhu yang ditampilkan di atas grafik (misal "30°", "26°", dst) posisinya PERSIS lurus di atas titik data yang bersangkutan pada grafik, bukan hanya kira-kira sejajar. Gunakan koordinat yang sama (x-position) antara label angka dan titik data di SVG/canvas grafik.

### 5. Perjelas Card Per-Jam di Bawah Grafik
- Pastikan card per-jam (jam, ikon, suhu, %hujan) tersusun dalam urutan yang SAMA PERSIS dengan urutan titik di grafik atasnya — user harus bisa langsung mencocokkan titik di grafik dengan card di bawahnya tanpa bingung urutan.

## Ketentuan Teknis
- Untuk audit data (bagian A), buat log sementara/debug print urutan `list[]` dari API vs hasil final yang ditampilkan, untuk membandingkan apakah ada index yang tertukar saat konversi timezone.
- Untuk perbaikan visual (bagian B), gunakan library chart yang sudah dipakai saat ini (jika custom SVG, tambahkan elemen gridline/tooltip manual; jika pakai chart library seperti Chart.js/Recharts, aktifkan fitur tooltip & gridline bawaan library tersebut).
- Tidak perlu API tambahan, tidak ada migration baru, tidak ada perubahan Docker.

## Deliverable
- Laporan hasil audit: apakah ditemukan anomali suhu, dan jika ada, penjelasan root cause + perbaikannya.
- Grafik Hourly Forecast dengan gridline, tooltip hover, shading siang/malam, dan alignment angka suhu yang presisi.
- Screenshot hasil akhir untuk Paris dan minimal 1 kota lain, termasuk screenshot saat hover di beberapa titik grafik untuk membuktikan tooltip berfungsi.

## Checklist Verifikasi
- [ ] Semua slot jam (scroll penuh) menunjukkan pola suhu yang logis, tidak ada lonjakan aneh yang tidak wajar.
- [ ] Data suhu per-jam sudah tersinkronisasi benar dengan label jam yang sudah dikonversi ke waktu lokal (tidak tertukar index).
- [ ] Grafik punya gridline yang membantu membaca titik data.
- [ ] Tooltip muncul saat hover di titik grafik, menampilkan jam + suhu yang akurat.
- [ ] Ada perbedaan visual (shading) antara periode siang dan malam pada grafik.
- [ ] Angka suhu di atas grafik sejajar presisi dengan titik data di bawahnya.
- [ ] Urutan card per-jam konsisten dengan urutan titik di grafik.
- [ ] Test di minimal 2 kota berbeda timezone.
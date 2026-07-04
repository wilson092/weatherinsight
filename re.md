# Prompt 1/3: Restyle Halaman Comparison Mengikuti Tema Dashboard

## ⚠️ BATASAN PENTING
- **JANGAN membuat migration baru.** Data yang dibutuhkan sudah tersedia di tabel yang ada.
- **JANGAN mengubah konfigurasi Docker** (Dockerfile, docker-compose.yml, environment container). Kerjakan murni di level Blade/Livewire/Tailwind/PHP query.
- **PENTING — Halaman yang dimaksud**: "Comparison" adalah **halaman terpisah** yang diakses lewat menu navbar (klik "Comparison" di navigation bar atas, URL `/comparison`) — **BUKAN** bagian dari halaman Dashboard utama. Pastikan Anda mengedit file/route/component untuk halaman Comparison ini secara spesifik, JANGAN edit file Dashboard.
- Fokus HANYA pada halaman **Comparison** dulu. Leaderboard dan History akan dikerjakan terpisah nanti (prompt lanjutan), jangan disentuh dulu di task ini.

## Konteks
Aplikasi WeatherInsight punya navbar dengan 4 menu: **Dashboard**, **Comparison**, **Leaderboard**, **History** — masing-masing halaman terpisah dengan route/component sendiri.

Dashboard utama sudah final dan menjadi acuan tema visual aplikasi:
- Card menggunakan `rounded-xl bg-slate-800/50 border border-slate-700/50 p-4` (atau `p-5` untuk card besar).
- Warna aksen solid cyan (`bg-cyan-500 hover:bg-cyan-600`) untuk tombol utama — BUKAN gradient.
- Header section pakai pola: ikon kecil bulat + judul (font-semibold, ukuran sedang), tanpa label kapital besar bergaya promosional.
- Badge kategori risk (Low/Medium/High Risk) pakai bentuk pill/rounded-full dengan warna hijau/kuning/merah sesuai kategori.
- Tipografi judul halaman: font besar, bold, putih.

Halaman **Comparison** (menu navbar "Comparison", terpisah dari Dashboard) saat ini masih bergaya berbeda:
- Ada card besar di atas dengan label kapital kecil "SIDE-BY-SIDE INTELLIGENCE" (warna cyan), judul besar "Weather Comparison", dan subtitle abu-abu — card ini pakai background gradient gelap yang berbeda dari card standar Dashboard.
- Di bawahnya ada 2 input side-by-side: "Primary City" dan "Comparison City", dengan tombol "Compare Cities" bergaya gradient cyan-ke-teal (bukan solid cyan).
- Ada card besar kosong (state awal sebelum ada perbandingan) bertuliskan "Ready for Comparison" dengan ikon panah bolak-balik besar di tengah, juga pakai background gradient promosional.

## Perubahan yang Diminta

### 1. Card Header Halaman Comparison
- Hapus card gradient besar dengan label "SIDE-BY-SIDE INTELLIGENCE".
- Ganti dengan pola sederhana: judul halaman "Weather Comparison" (font besar, bold, putih) + subtitle kecil abu-abu di bawahnya ("Compare weather conditions between two cities and analyze the differences") — TANPA background card gradient, cukup teks biasa (mengikuti pola judul "Jakarta" polos tanpa bingkai di Dashboard).

### 2. Card Input Perbandingan
- Bungkus 2 input ("Primary City", "Comparison City") dan tombol "Compare Cities" dalam SATU card standar: `rounded-xl bg-slate-800/50 border border-slate-700/50 p-5`.
- Style input text: samakan dengan input search bar full-width di Dashboard (background gelap sedikit beda dari card, border tipis, padding nyaman).
- Tombol "Compare Cities": ubah dari gradient cyan-ke-teal menjadi **solid cyan** (`bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg`), samakan persis dengan tombol "Search" di Dashboard.

### 3. Card State Kosong ("Ready for Comparison")
- Hapus background gradient besar pada card ini.
- Ganti jadi card standar (`rounded-xl bg-slate-800/50 border border-slate-700/50 p-8`).
- Ikon panah bolak-balik tetap dipertahankan tapi perkecil ukurannya dan gunakan warna cyan solid (bukan di dalam kotak gradient besar seperti sekarang) — cukup ikon dengan warna aksen, teks "Ready for Comparison" (bold, putih) dan subtitle abu-abu di bawahnya, semua center-aligned dalam card standar.

### 4. Saat Hasil Perbandingan Muncul (Setelah Klik "Compare Cities")
- Pastikan card hasil perbandingan (menampilkan data 2 kota side-by-side: suhu, kondisi, humidity, dst) JUGA memakai card style standar (`rounded-xl bg-slate-800/50 border`), bukan style berbeda.
- Badge risk level (jika ditampilkan di hasil perbandingan) memakai style pill yang sama dengan Weather Alert Center di Dashboard.

## Ketentuan Teknis
- **Hanya ubah file/route/component untuk halaman Comparison** (biasanya di `resources/views/livewire/comparison.blade.php` atau nama serupa, dan komponen Livewire terkait) — **JANGAN sentuh file Dashboard**.
- Tidak perlu query/logic baru — fungsi "Compare Cities" yang sudah ada (fetch data 2 kota, tampilkan perbandingan) tetap dipakai, hanya restyle tampilannya.
- Tidak ada migration baru, tidak ada perubahan Docker.

## Deliverable
- Halaman Comparison (diakses via navbar, bukan Dashboard) dengan tema visual yang identik dengan Dashboard.
- Screenshot hasil akhir: state kosong (sebelum compare) DAN state hasil (setelah compare 2 kota) untuk verifikasi.
- Screenshot Dashboard SEBELUM dan SESUDAH pengerjaan untuk membuktikan Dashboard tidak ikut berubah/rusak.

## Checklist Verifikasi
- [ ] Perubahan dilakukan di halaman Comparison (route/component terpisah), BUKAN di file Dashboard.
- [ ] Dashboard tetap sama persis seperti sebelumnya, tidak ada perubahan tidak sengaja.
- [ ] Card gradient besar "SIDE-BY-SIDE INTELLIGENCE" sudah dihapus, diganti judul+subtitle polos.
- [ ] Input "Primary City" & "Comparison City" dibungkus card standar, style input konsisten dengan Dashboard.
- [ ] Tombol "Compare Cities" solid cyan, bukan gradient.
- [ ] Card "Ready for Comparison" sudah tidak pakai gradient besar, memakai card standar.
- [ ] Card hasil perbandingan (setelah klik Compare) memakai style card standar Dashboard.
- [ ] Badge risk (jika ada di hasil comparison) konsisten dengan Dashboard.
- [ ] Tidak ada migration baru dibuat.
- [ ] Tidak ada perubahan pada Docker/environment container.
- [ ] Tampilan tetap responsive di mobile.
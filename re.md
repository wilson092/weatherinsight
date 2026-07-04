markdown# Prompt: Perbaiki Posisi Search Bar, Hapus Visibility, & Perkaya Card Cuaca (Tanpa Background Gradient)

## Konteks
Dashboard sudah lebih baik, tapi masih ada 3 hal yang perlu diperbaiki (screenshot `current-dashboard.png` dan `current-weather-card.png` terlampir):
1. Search bar terlalu kecil, hanya ada di pojok kanan header — perlu dipindah jadi full-width di bawah header (seperti versi awal dulu).
2. Field "Visibility" selalu menampilkan "N/A" — hapus saja field ini.
3. Card "Current Day Detail" (Saturday, 31°, Scattered Clouds) terlihat sangat kosong di bagian tengah — perlu diisi agar lebih hidup, **TANPA menggunakan background gradient/foto** — cukup dengan penataan ulang layout, tipografi, dan tambahan insight teks.

## A. Kembalikan Search Bar Full-Width di Bawah Header

- Tambahkan kembali search bar besar full-width tepat di bawah header, mengikuti posisi versi awal dulu:
  - Input text besar dengan placeholder "Cari kota..." di kiri, tombol "Search" (warna cyan, sesuai brand) di kanan.
  - Full width mengikuti lebar container dashboard.
- Hapus search bar kecil yang saat ini ada di header (kanan atas, dekat toggle °C/°F) — cukup satu search bar saja, agar tidak duplikat.
- Fungsional tetap sama: ketik nama kota → Enter/klik Search → trigger Livewire → update seluruh dashboard tanpa reload, dengan loading state saat proses fetch.
- Tambahkan spacing (margin-bottom) antara search bar ini dan section "Jakarta" + tabs hari di bawahnya.

## B. Hapus Field Visibility

- Hapus field "Visibility" dari grid info card cuaca (card "Current Day Detail" dan tempat lain jika ada, misal popup Weather Map).
- Setelah dihapus, grid info tinggal 3 item: Humidity, Pressure, Wind. Susun ulang jadi 3 kolom sejajar dalam 1 baris agar tetap seimbang secara visual (bukan 2x2 dengan 1 slot kosong).

## C. Perkaya Card "Current Day Detail" TANPA Background Gradient

Ruang kosong di tengah card (antara header tanggal/jam dengan blok suhu+ikon, dan antara blok suhu+ikon dengan grid info) perlu diisi dengan pendekatan berikut:

### C1. Tambahkan Insight/Rekomendasi Singkat
- Di bawah ikon cuaca + suhu, tambahkan 1 baris kalimat insight singkat yang diambil dari field `insight` di `weather_histories` (data yang sama dipakai di section "Weather Recommendation" di bawah — cukup ambil 1 kalimat pertama/ringkas).
- Contoh: "Cuaca cerah berawan, nyaman untuk aktivitas luar ruangan." — style teks subtle (warna abu-abu terang, sedikit lebih kecil dari teks utama), diberi sedikit padding/border-top tipis sebagai pemisah dari blok suhu di atasnya.

### C2. Perbesar Ikon Cuaca & Reposisi Layout Horizontal
- Perbesar ukuran ikon cuaca (saat ini terlihat kecil dibanding ruang kosong di sekitarnya).
- Susun ikon cuaca besar di kiri, suhu + deskripsi cuaca di kanan ikon, sejajar horizontal (bukan menumpuk vertikal seperti sekarang) agar ruang terisi lebih proporsional.

### C3. Kurangi Jarak Vertikal Berlebih
- Rapatkan jarak antara header tanggal/jam dengan blok suhu+ikon.
- Rapatkan jarak antara blok suhu+ikon (+ insight baru) dengan divider/grid info di bawahnya.
- Tujuan: card terasa padat berisi, bukan banyak ruang kosong yang dipaksa melebar.

### C4. Tambahkan Elemen Kecil Tambahan (Opsional, jika ingin lebih kaya)
- Tambahkan badge kecil kategori suhu di dekat angka suhu besar, contoh: badge "Normal" / "Tinggi" / "Rendah" (warna sesuai kategori: hijau/kuning/merah) — ini bisa dihubungkan ke data risk assessment yang sudah ada (`risk_level` per faktor suhu), memberi konteks tambahan tanpa perlu elemen visual berat seperti gambar.
- Tambahkan divider tipis (garis horizontal subtle, `border-t border-slate-700/50`) untuk memisahkan blok suhu dari grid info bawah, memberi struktur visual yang lebih jelas.

## Ketentuan Teknis
- Tidak ada perubahan background/gradient/foto pada card — murni perbaikan layout, tipografi, spacing, dan tambahan teks insight + badge kecil.
- Field insight diambil dari kolom `insight` yang sudah ada di `weather_histories` — tidak perlu tambahan API/database baru.
- Badge kategori suhu (jika dipakai) mengambil data yang sama dengan yang dipakai di Weather Risk Assessment (field `risk_level` per faktor suhu) — tidak perlu logic baru, cukup reuse.
- Search bar full-width menggunakan Livewire component yang sama (method `searchCity()`), cukup dipindah posisinya di Blade template.
- Pastikan responsive: di mobile, search bar full-width tetap proporsional, card cuaca (ikon+suhu horizontal, insight, badge) tetap terbaca rapi.

## Deliverable
- Pindahkan search bar jadi full-width di bawah header, hapus search bar kecil di header.
- Hapus field Visibility dari grid info card.
- Redesign card "Current Day Detail": layout ikon+suhu horizontal, tambah insight singkat, tambah divider, rapatkan spacing, (opsional) tambah badge kategori suhu.
- Screenshot hasil akhir (desktop + mobile) untuk verifikasi.

## Checklist Verifikasi
- [ ] Search bar full-width tampil di bawah header, berfungsi normal (update dashboard tanpa reload).
- [ ] Tidak ada search bar duplikat di header.
- [ ] Field Visibility sudah tidak muncul lagi di card manapun.
- [ ] Ikon cuaca + suhu tersusun horizontal (bukan menumpuk vertikal seperti sebelumnya).
- [ ] Ada insight singkat di bawah blok suhu.
- [ ] Ada divider pemisah antara blok suhu dan grid info.
- [ ] Spacing di dalam card lebih rapat, tidak ada ruang kosong berlebih.
- [ ] Grid info (Humidity, Pressure, Wind) tersusun 3 kolom sejajar, tanpa Visibility.
- [ ] Tampilan responsive di mobile tetap baik.
- [ ] Tidak ada background gradient/foto yang ditambahkan pada card.
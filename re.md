markdown# Prompt: Redesign Card Cuaca Mengikuti Referensi Clean (Tanpa UV Index, Tanpa Insight)

## Konteks
Referensi desain card cuaca yang diinginkan sudah ada diterapkan ke card "Current Day Detail" di dashboard saat ini (`current-dashboard.png` terlampir). **UV Index tidak perlu diimplementasikan** (karena butuh One Call API 3.0 yang tidak tersedia di plan saat ini) — footer cukup Sunrise & Sunset saja. Card juga masih menampilkan teks paragraf komentar/insight yang **harus dihapus total**.

## Struktur Card Baru (Ikuti Referensi)

### 1. Header
- Nama hari besar ("Saturday") + tanggal kecil di bawahnya ("04 Jul 2026") di kiri.
- Jam saat ini ("20:41") di kanan atas.

### 2. Blok Utama: 2 Kolom dengan Divider Vertikal

**Kolom Kiri:**
- Ikon cuaca besar (perbesar signifikan dari ukuran saat ini, ~120-140px).
- Suhu besar ("30°") + badge kategori kecil di sampingnya (contoh: "Low" — pastikan Title Case, bukan "low" huruf kecil).
- Deskripsi cuaca di bawahnya ("Scattered Clouds").
- **HAPUS teks paragraf insight/komentar** yang saat ini muncul — sudah beberapa kali diminta dihapus, pastikan benar-benar hilang dan tidak muncul lagi di card ini.

**Kolom Kanan** (dipisahkan garis vertikal tipis `border-l border-slate-700/50`):
- Humidity — ikon tetes air + persen, dengan divider horizontal tipis di bawahnya.
- Pressure — ikon gauge + nilai hPa, dengan divider horizontal tipis di bawahnya.
- Wind — ikon angin + kecepatan km/h.
- (Visibility tetap dihapus, tidak perlu dikembalikan.)

### 3. Footer: Sunrise & Sunset Saja (2 Kolom, Tanpa UV Index)
Tambahkan baris footer di bagian bawah card, dipisahkan garis horizontal (`border-t border-slate-700/50`), berisi 2 kolom sejajar dengan divider vertikal tipis di antaranya:
- **Sunrise** — ikon matahari terbit + jam (format "05:52").
- **Sunset** — ikon matahari terbenam + jam (format "17:55").
- Karena hanya 2 item (bukan 3 seperti referensi asli), atur lebar kolom agar tetap seimbang secara visual (masing-masing 50% lebar, atau beri padding horizontal lebih agar tidak terlihat renggang di tengah).

## Ketentuan Data & Teknis

### Sunrise & Sunset
- Ambil dari field `sys.sunrise` dan `sys.sunset` di response `/data/2.5/weather` (endpoint current weather).
- Field berupa Unix timestamp — konversi ke jam lokal sesuai timezone kota (`timezone` offset dari response yang sama), format tampilan "HH:mm".
- Field ini gratis dan selalu tersedia, wajib diimplementasikan.

### UV Index
- **TIDAK PERLU diimplementasikan.** Jangan tambahkan field ini di card, jangan buat placeholder/dummy untuk ini.

## Bug yang Harus Diperbaiki Bersamaan
- **Hapus teks insight/komentar** dari card cuaca secara permanen — cek ulang logic yang menampilkannya, pastikan tidak ada kondisi yang secara otomatis merender teks ini lagi.
- **Fix kapitalisasi badge kategori**: "low" → "Low" (Title Case), pastikan konsisten untuk semua kategori (Normal, Rendah, Tinggi, dst).

## Deliverable
- Redesign card cuaca: header, 2 kolom (ikon+suhu+badge+deskripsi | Humidity+Pressure+Wind), footer Sunrise & Sunset saja.
- Hapus teks insight/komentar dari card ini secara permanen.
- Fix kapitalisasi badge kategori.
- Screenshot hasil akhir untuk verifikasi.

## Checklist Verifikasi
- [ ] Card cuaca TIDAK ADA teks paragraf komentar/insight apapun.
- [ ] Badge kategori tampil Title Case ("Low", bukan "low").
- [ ] Layout 2 kolom dengan divider vertikal: kiri (ikon besar+suhu+badge+deskripsi), kanan (Humidity, Pressure, Wind).
- [ ] Footer menampilkan Sunrise & Sunset dengan jam yang benar dan proporsional (2 kolom, bukan 3).
- [ ] UV Index TIDAK muncul di manapun pada card ini.
- [ ] Visibility tetap tidak muncul.
- [ ] Test di minimal 2 kota berbeda untuk memastikan Sunrise/Sunset berubah sesuai
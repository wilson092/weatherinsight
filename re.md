# Prompt 3/3: Restyle Halaman History Mengikuti Tema Dashboard

## ⚠️ BATASAN PENTING
- **JANGAN membuat migration baru.** Semua data yang dibutuhkan sudah tersedia di tabel `weather_histories`.
- **JANGAN mengubah konfigurasi Docker** (Dockerfile, docker-compose.yml, environment container). Kerjakan murni di level Blade/Livewire/Tailwind.
- **PENTING — Halaman yang dimaksud**: "History" adalah **halaman terpisah** yang diakses lewat menu navbar (klik "History", URL `/history`) — **BUKAN** bagian dari Dashboard. Edit HANYA file/route/component untuk halaman History ini.
- Ini adalah task terakhir dari 3 halaman (Comparison ✅ selesai, Leaderboard ✅ selesai, History — task ini). **JANGAN sentuh file Dashboard, Comparison, atau Leaderboard** yang sudah selesai dikerjakan sebelumnya.

## Konteks
Dashboard, Comparison, dan Leaderboard sudah final dengan tema visual yang konsisten:
- Card menggunakan `rounded-xl bg-slate-800/50 border border-slate-700/50 p-4` (atau `p-5` untuk card besar).
- Warna aksen solid cyan (`bg-cyan-500 hover:bg-cyan-600`) untuk tombol utama — BUKAN gradient.
- Badge kategori risk (Low/Medium/High) pakai bentuk pill/rounded-full dengan warna hijau/kuning/merah.
- Header section pakai pola: ikon kecil bulat (background berwarna) + judul (font besar, bold, putih) + subtitle kecil abu-abu.
- Tabel: header `bg-slate-900/50` dengan teks uppercase kecil abu-abu, baris dengan border tipis (`border-b border-slate-700/30`) dan hover effect halus.

Halaman **History** (screenshot `current-history.png` terlampir) SEBENARNYA **sudah cukup dekat** dengan tema ini (card header dengan ikon jam ungu sudah bagus, tabel sudah rapi dengan badge Low Risk yang konsisten). Namun ada beberapa hal yang masih perlu disamakan/dirapikan:

### Kondisi Saat Ini
1. Section filter tanggal (input "dd/mm/yyyy" + tombol "Date" cyan solid + tombol refresh kecil ikon panah melingkar di kanan) sudah dibungkus card (`rounded-xl` dengan border), ini SUDAH BAGUS dan konsisten.
2. Tabel di bawahnya (DATE & TIME, CITY, TEMP, HUMIDITY, PRESSURE, WIND, RISK, WEATHER) TIDAK dibungkus card terpisah — menyatu langsung dengan card filter tanggal di atasnya dalam satu container besar, padahal secara pola Dashboard biasanya tiap section punya card sendiri.
3. Nama kota di kolom CITY tidak konsisten kapitalisasi — "tel aviv" dan "bandung" huruf kecil semua, sementara "Jakarta" sudah benar Title Case.
4. Badge "LOW" di kolom RISK terlihat agak menempel rapat dengan teks "Low Risk" di atasnya — perlu sedikit spacing agar lebih rapi.

## Perubahan yang Diminta

### 1. Pisahkan Card Filter Tanggal dan Card Tabel
- Bungkus tabel history dalam card TERPISAH dari card filter tanggal (`rounded-xl bg-slate-800/50 border border-slate-700/50`), dengan jarak (`mt-6` atau `space-y-6`) di antara keduanya — mengikuti pola pemisahan card section seperti di Dashboard (misal antara card Current Weather dan card Hourly Forecast yang terpisah jelas).
- Card filter tanggal tetap seperti sekarang (sudah bagus): input date + tombol "Date" solid cyan + tombol refresh.

### 2. Fix Kapitalisasi Nama Kota
- Perbaiki tampilan nama kota di kolom CITY agar selalu Title Case: "Tel Aviv" (bukan "tel aviv"), "Bandung" (bukan "bandung"), dst.
- Gunakan `Str::title()` di level tampilan (Blade), JANGAN mengubah data asli yang tersimpan di database — cukup ubah cara tampilnya saja.
- Terapkan konsisten untuk SEMUA baris di tabel history, tidak hanya yang terlihat di screenshot saat ini.

### 3. Rapikan Spacing Badge Risk
- Beri sedikit jarak vertikal (`mt-1` atau `gap-1` dalam flex-col) antara teks "Low Risk"/"Medium Risk"/"High Risk" dan badge pill kecil ("LOW"/"MEDIUM"/"HIGH") di kolom RISK, supaya tidak terlihat menempel.

### 4. Verifikasi Konsistensi Visual Keseluruhan
- Pastikan warna header tabel (`bg-slate-900/50`), border baris (`border-b border-slate-700/30`), dan hover effect (`hover:bg-slate-700/20`) SUDAH konsisten dengan tabel di Leaderboard yang sudah selesai dikerjakan sebelumnya — jika ada perbedaan kecil, samakan.
- Pastikan ikon cuaca kecil di kolom WEATHER (ikon awan/matahari/dst) menggunakan set ikon yang sama dengan yang dipakai di Dashboard dan Leaderboard (Heroicons, dengan nama yang sudah diverifikasi valid — jangan menebak nama ikon baru, gunakan ikon yang sudah terbukti bekerja di halaman lain).

## Ketentuan Teknis
- **Hanya ubah file/route/component untuk halaman History** — JANGAN sentuh file Dashboard, Comparison, atau Leaderboard.
- Tidak perlu query/logic baru — data yang sudah tampil di tabel history (dari `weather_histories`) tetap dipakai apa adanya, ini murni perbaikan tampilan.
- Jika perlu clear cache setelah perubahan, jalankan SATU PER SATU (bukan digabung `&&` dalam satu baris):
```bash
  docker compose exec php php artisan view:clear
```
```bash
  docker compose exec php php artisan cache:clear
```
- Tidak ada migration baru, tidak ada perubahan Docker.

## Deliverable
- Halaman History dengan card tabel terpisah dari card filter tanggal.
- Nama kota tampil Title Case di semua baris.
- Spacing badge risk lebih rapi.
- Konsistensi visual penuh dengan Dashboard & Leaderboard.
- Screenshot hasil akhir (desktop + mobile) untuk verifikasi.
- Screenshot Dashboard, Comparison, Leaderboard SEBELUM dan SESUDAH untuk membuktikan ketiganya tidak ikut berubah/rusak.

## Checklist Verifikasi
- [ ] Perubahan hanya di halaman History, Dashboard/Comparison/Leaderboard tidak berubah.
- [ ] Card filter tanggal dan card tabel sudah terpisah dengan jarak yang jelas.
- [ ] Nama kota tampil Title Case ("Tel Aviv", "Bandung", "Jakarta") di semua baris tabel.
- [ ] Badge risk (Low/Medium/High) punya spacing yang rapi, tidak menempel dengan teks di atasnya.
- [ ] Style tabel (header, border, hover) konsisten dengan Leaderboard.
- [ ] Ikon cuaca di kolom WEATHER menggunakan ikon yang sudah terverifikasi valid (tidak menyebabkan error seperti kasus Leaderboard sebelumnya).
- [ ] Tidak ada migration baru dibuat.
- [ ] Tidak ada perubahan Docker.
- [ ] Tampilan tetap responsive di mobile.
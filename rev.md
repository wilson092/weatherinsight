# Prompt: Perbaiki UX Guest — Tombol Login/Sign Up di Navbar & Notifikasi Saat Redirect ke Login

## Konteks
Alur akses sudah benar secara logic (Dashboard & Comparison bisa diakses tanpa login, Leaderboard & History redirect ke `/login` untuk guest). Namun ada 2 masalah UX yang membuat pengalaman guest membingungkan (screenshot `dashboard-guest.png` terlampir):

1. **Navbar tidak menampilkan tombol Login/Sign Up untuk guest** — saat ini pojok kanan atas navbar kosong/tidak ada indikasi apapun soal status login, padahal sebelumnya sudah diminta ada perbedaan tampilan guest vs logged-in user.
2. **Redirect ke `/login` terjadi tanpa pemberitahuan** — saat guest klik menu "History" atau "Leaderboard", browser langsung pindah ke halaman login TANPA pesan apapun yang menjelaskan alasannya. User akan bingung kenapa tiba-tiba dilempar ke form login.

## A. Tambahkan Tombol Login/Sign Up di Navbar untuk Guest

- Untuk user yang BELUM login, tampilkan di pojok kanan navbar (menggantikan area yang sekarang kosong):
  - Tombol **"Login"** (style outline/ghost, teks saja atau border tipis) 
  - Tombol **"Sign Up"** (style solid/filled, warna aksen — bisa pakai cyan sesuai tema aplikasi, atau warna lain yang kontras tapi tetap konsisten dengan branding WeatherInsight).
  - Contoh referensi styling yang diinginkan: tombol "Login" simple text/outline di kiri, tombol "Sign Up" solid berwarna di kanan, berdampingan dengan sedikit gap (lihat referensi `signup-button-ref.png` — style pill rounded dengan warna solid untuk tombol utama).
- Untuk user yang SUDAH login, tetap tampilkan seperti sekarang: nama/avatar + dropdown "User Account" dengan opsi logout.
- Terapkan di SEMUA halaman yang punya navbar (Dashboard, Comparison, Leaderboard, History) — kondisi guest vs logged-in harus konsisten di keempat halaman ini.

## B. Tambahkan Notifikasi Saat Redirect ke Login

Saat guest mencoba akses halaman protected (Leaderboard/History) dan di-redirect ke `/login`, tampilkan pesan yang jelas menjelaskan alasannya:

### 1. Flash Message di Halaman Login
- Gunakan Laravel session flash message (`session()->flash('message', '...')`) yang di-set SEBELUM redirect terjadi (bisa lewat custom middleware/exception handler untuk `AuthenticationException`, atau override method `redirectTo()` di middleware `Authenticate` bawaan Laravel).
- Pesan yang ditampilkan di halaman login, contoh: **"Silakan login terlebih dahulu untuk mengakses halaman ini."** — tampilkan sebagai banner/alert kecil di atas form login (misal dengan style `bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-lg p-3`, konsisten dengan tema aplikasi).
- Pesan ini HANYA muncul kalau redirect terjadi karena mencoba akses halaman protected — JANGAN muncul kalau user memang sengaja klik "Login" dari navbar (state kosong seperti biasa untuk kasus ini).

### 2. Implementasi Teknis
- Cek/override middleware `Authenticate` Laravel (biasanya di `app/Http/Middleware/Authenticate.php`), method `redirectTo()`:
```php
protected function redirectTo($request)
{
    if (! $request->expectsJson()) {
        session()->flash('auth_message', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        return route('login');
    }
}
```
- Di `login.blade.php`, tampilkan flash message ini jika ada:
```blade
@if (session('auth_message'))
    <div class="bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-lg p-3 mb-4 text-sm">
        {{ session('auth_message') }}
    </div>
@endif
```

## Ketentuan Teknis
- Tidak perlu migration baru — ini murni perubahan Blade/middleware/session.
- Tidak ada perubahan Docker.
- Pastikan flash message TIDAK muncul terus-menerus (hanya sekali saat pertama kali redirect terjadi, standard behavior session flash Laravel yang otomatis hilang setelah 1 request).
- Test alur: guest buka Dashboard (harus lihat tombol Login/Sign Up di navbar) → klik "History" → redirect ke `/login` DENGAN pesan flash muncul → berhasil login → kembali ke History (intended redirect, sudah diimplementasikan sebelumnya) → cek navbar sekarang menampilkan status logged-in.

## Deliverable
- Tombol Login/Sign Up muncul di navbar untuk guest, di semua halaman (Dashboard, Comparison, Leaderboard, History).
- Flash message informatif muncul di halaman login saat redirect terjadi akibat mencoba akses halaman protected.
- Screenshot hasil akhir: navbar guest (dengan tombol Login/Sign Up), halaman login dengan flash message setelah klik History/Leaderboard sebagai guest.

## Checklist Verifikasi
- [ ] Navbar guest menampilkan tombol "Login" dan "Sign Up" di semua halaman.
- [ ] Navbar user yang sudah login tetap menampilkan "User Account" seperti sebelumnya.
- [ ] Klik "History"/"Leaderboard" sebagai guest → redirect ke `/login` DENGAN pesan flash yang jelas.
- [ ] Klik "Login" langsung dari navbar (bukan karena redirect) → halaman login TANPA flash message (state normal).
- [ ] Setelah berhasil login dari redirect, user kembali ke halaman yang tadinya dituju (History/Leaderboard).
- [ ] Tidak ada migration baru, tidak ada perubahan Docker.
# Prompt: Atur Alur Akses (Public vs Protected Routes) + Integrasi Login Google (Socialite)

## ⚠️ BATASAN PENTING
- **Migration BOLEH dibuat** untuk task ini SECARA KHUSUS jika memang dibutuhkan untuk kolom OAuth (misal `google_id`, `avatar`) di tabel `users` — ini pengecualian dari larangan sebelumnya karena memang esensial untuk fitur Google Login. Konfirmasi dulu ke saya sebelum menjalankan migration jika ada perubahan skema pada tabel yang sudah ada.
- **JANGAN mengubah konfigurasi Docker** kecuali menambahkan environment variable baru (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`) ke file `.env` — ini BUKAN perubahan Docker, cukup tambah variable environment aplikasi.
- Tidak perlu rebuild/restart container kecuali untuk install package baru via composer (`composer require laravel/socialite`), yang bisa dijalankan di dalam container yang sudah berjalan.

## Konteks
Aplikasi WeatherInsight akan di-hosting ke VPS untuk publik. Saat ini semua halaman (Dashboard, Comparison, Leaderboard, History) kemungkinan bisa diakses tanpa login (atau butuh login untuk semua, perlu dicek kondisi saat ini). Saya ingin mengatur alur akses sebagai berikut, dan menambahkan opsi login via Google.

## A. Atur Alur Akses: Public vs Protected Routes

### 1. Halaman Publik (Bisa Diakses TANPA Login)
- **Dashboard** (`/` atau `/dashboard`) — bisa diakses siapa saja, termasuk pengunjung yang belum login. Fitur pencarian kota, cuaca real-time, hourly forecast, weather map, risk assessment tetap berfungsi penuh tanpa perlu akun.
- **Comparison** (`/comparison`) — sama, bisa diakses publik tanpa login.

### 2. Halaman Protected (WAJIB Login)
- **Leaderboard** (`/leaderboard`) — jika user belum login dan mencoba akses, redirect ke halaman `/login`.
- **History** (`/history`) — sama, wajib login.

### 3. Redirect Setelah Login
- Jika user belum login mencoba mengakses `/leaderboard` atau `/history`, sistem harus redirect ke `/login`, DAN setelah berhasil login, user diarahkan KEMBALI ke halaman yang tadinya ingin diakses (bukan selalu ke Dashboard).
- Implementasi: gunakan mekanisme `intended URL` bawaan Laravel (`redirect()->intended()`) yang sudah otomatis menangani ini jika middleware `auth` diterapkan dengan benar pada route Leaderboard dan History.

### 4. Middleware yang Perlu Diterapkan
- Terapkan middleware `auth` (Laravel default) pada route/group route untuk Leaderboard dan History.
- Route Dashboard dan Comparison TIDAK perlu middleware `auth` — biarkan bisa diakses guest.
- Cek `routes/web.php` dan sesuaikan grouping route sesuai pembagian ini.

### 5. Navbar — Tampilkan Status Login
- Jika user belum login: tampilkan tombol "Login" atau "Sign In" di navbar (pojok kanan, menggantikan area "User Account" yang sekarang selalu tampil).
- Jika user sudah login: tetap tampilkan seperti sekarang ("User Account" dengan nama/avatar + tombol logout).
- Menu navbar "Leaderboard" dan "History" tetap TERLIHAT untuk guest (jangan disembunyikan), tapi begitu diklik akan redirect ke login jika belum authenticated — ini lebih baik daripada menyembunyikan menu (supaya guest tahu fitur ini ada dan tertarik untuk daftar).

## B. Integrasi Login via Google (Laravel Socialite)

### 1. Install Package
```bash
docker compose exec php composer require laravel/socialite
```

### 2. Setup Google OAuth Credentials
- Daftarkan aplikasi di [Google Cloud Console](https://console.cloud.google.com/) → buat OAuth 2.0 Client ID → dapatkan `Client ID` dan `Client Secret`.
- Tambahkan ke file `.env`:
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=https://weather.test/auth/google/callback
(Sesuaikan `GOOGLE_REDIRECT_URI` dengan domain final saat sudah di-hosting ke VPS, gunakan domain production, bukan `weather.test` yang cuma untuk local development.)

- Tambahkan konfigurasi di `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

### 3. Migration Tambahan (Jika Diperlukan)
- Cek tabel `users` — jika belum ada kolom untuk menyimpan `google_id` dan `avatar` (opsional, untuk foto profil dari Google), buat migration baru khusus untuk ini:
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->nullable()->unique();
    $table->string('avatar')->nullable();
    $table->string('password')->nullable()->change(); // agar user Google tidak wajib punya password
});
```

### 4. Buat Route & Controller untuk Google OAuth
```php
// routes/web.php
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
```

```php
// app/Http/Controllers/GoogleAuthController.php
public function redirect()
{
    return Socialite::driver('google')->redirect();
}

public function callback()
{
    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
        ]
    );

    Auth::login($user);

    return redirect()->intended('/dashboard');
}
```

### 5. Update Halaman Login (Tambah Tombol "Sign in with Google")
- Di halaman login (`login.blade.php` atau sejenis), tambahkan tombol "Sign in with Google" di bawah tombol "Sign In" yang sudah ada, dipisahkan dengan divider "atau" ("or continue with").
- Style tombol: putih dengan border, logo Google berwarna di kiri teks, mengikuti pola umum tombol "Sign in with Google" (bisa pakai icon Google resmi via CDN/SVG).
- Tombol ini mengarah ke route `/auth/google` yang sudah dibuat.
- Tetap pertahankan form email/password manual yang sudah ada sebagai alternatif — Google Sign-In sebagai TAMBAHAN, bukan pengganti.

## C. Polish Halaman Login (Opsional, Sambil Dikerjakan)
- 2 elemen putih pill kosong di pojok kiri atas dan kiri bawah (kemungkinan dekorasi awan) terlihat tidak jelas bentuknya — perjelas jadi bentuk awan yang lebih terlihat, atau hapus jika tidak jadi dipakai.
- Pastikan halaman login tetap konsisten secara branding (logo cloud cyan, nama "WeatherInsight") dengan tema aplikasi secara keseluruhan.

## Ketentuan Teknis
- Migration HANYA untuk menambah kolom `google_id`, `avatar`, dan membuat `password` nullable di tabel `users` — tidak ada perubahan skema lain.
- Install package via composer di dalam container yang sudah berjalan, jangan rebuild image.
- Tambahkan environment variable Google OAuth ke `.env`, bukan hardcode di kode.
- Test alur lengkap: guest buka Dashboard (harus bisa) → guest coba klik Leaderboard (harus redirect ke login) → login pakai Google → harus balik ke halaman Leaderboard (bukan ke Dashboard) → logout → ulangi test untuk History.

## Deliverable
- Route Dashboard & Comparison bebas diakses tanpa login.
- Route Leaderboard & History wajib login, redirect ke halaman asal setelah login berhasil.
- Tombol "Sign in with Google" berfungsi penuh di halaman login.
- Migration baru (jika diperlukan) untuk kolom `google_id`, `avatar` di tabel `users`.
- Screenshot: halaman login dengan tombol Google baru, alur redirect saat guest coba akses Leaderboard/History.

## Checklist Verifikasi
- [ ] Dashboard bisa diakses tanpa login.
- [ ] Comparison bisa diakses tanpa login.
- [ ] Leaderboard redirect ke `/login` jika belum login, dan kembali ke Leaderboard setelah berhasil login.
- [ ] History redirect ke `/login` jika belum login, dan kembali ke History setelah berhasil login.
- [ ] Tombol "Sign in with Google" muncul di halaman login dan berfungsi (redirect ke Google, lalu kembali dan otomatis login).
- [ ] User yang login via Google otomatis tersimpan/terupdate di tabel `users` dengan `google_id` terisi.
- [ ] Form login email/password manual tetap berfungsi seperti biasa.
- [ ] Navbar menampilkan status login yang sesuai (tombol Login untuk guest, User Account untuk yang sudah login).
- [ ] Environment variable Google OAuth sudah ditambahkan ke `.env` (development dulu, nanti disesuaikan saat deploy ke VPS).
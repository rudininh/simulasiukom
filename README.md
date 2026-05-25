# Simulasi CAT BKN

Aplikasi simulasi ujian CAT berbasis Laravel 12, Blade, Bootstrap 5, MySQL, dan JavaScript vanilla.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buat database MySQL bernama `simulasiukom` terlebih dahulu, atau sesuaikan nilai `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.

## Akun Seeder

- Admin: `admin@example.com` / `password`
- Peserta: `peserta@example.com` / `password`

## Fitur

- Login peserta dengan captcha session.
- Register peserta.
- Dashboard, daftar simulasi, detail ujian.
- Halaman ujian dengan timer countdown, navigasi soal, progress bar, autosave AJAX, konfirmasi selesai, dan auto-finish saat waktu habis.
- Hasil simulasi, skor kategori TWK/TIU/TKP, badge lulus/tidak lulus.
- Riwayat ujian dan profil peserta.
- Admin panel sederhana untuk peserta, ujian, kategori, soal, import CSV, rekap hasil, dan reset hasil peserta.

## Format Import CSV Soal

Urutan kolom:

```csv
pertanyaan,opsi_a,opsi_b,opsi_c,opsi_d,opsi_e,jawaban_benar,skor,order_number
```

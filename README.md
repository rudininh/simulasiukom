# SIMULASI CAT UJI KOMPETENSI MANAJEMEN ASN

Aplikasi simulasi CAT untuk Uji Kompetensi Jabatan Manajemen ASN. Stack: Laravel 12, Blade, Bootstrap 5, MySQL, JavaScript vanilla, dan Font Awesome.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buat database MySQL `simulasiukom`, atau sesuaikan konfigurasi `DB_*` di `.env`.

## Local Domain Laragon

Project disiapkan untuk URL:

```text
http://simulasiukom.test
```

Jika domain belum terbuka, pastikan entry berikut ada di `C:\Windows\System32\drivers\etc\hosts` dengan hak Administrator:

```text
127.0.0.1      simulasiukom.test
```

Vhost Apache Laragon mengarah ke:

```text
C:/laragon/www/simulasiukom/public
```

## Akun Default

- Admin: `admin@example.com` / `password`
- Peserta: `peserta@example.com` / `password`

## Alur Peserta

1. Login melalui `/login`.
2. Buka `/dashboard`.
3. Pilih simulasi pada `/simulasi`.
4. Baca detail ujian, lalu klik `Mulai Ujian Sekarang`.
5. Jawab soal A-E. Jawaban tersimpan otomatis.
6. Klik `Selesaikan Ujian` atau tunggu timer habis.
7. Lihat hasil, riwayat, dan cetak hasil dari halaman hasil.

## Bank Regulasi

Admin dapat membuka `/admin/regulations` untuk upload regulasi PDF, DOCX, atau TXT. Sistem menyimpan metadata regulasi dan mencoba mengekstrak teks ke kolom `extracted_text`.

## Generate Soal dari Regulasi

1. Upload regulasi terlebih dahulu.
2. Buka `/admin/question-generator`.
3. Pilih regulasi, ujian tujuan, kategori, jumlah soal, tingkat kesulitan, dan tipe soal.
4. Klik `Generate Draft Soal`.
5. Jika `AI_API_KEY` kosong, sistem memakai dummy generator lokal.
6. Jika `AI_API_KEY` diisi, sistem mengirim prompt ke OpenAI sesuai `config/ai.php`.
7. Review draft di `/admin/generated-questions`.
8. Klik `Approve` agar soal masuk ke bank soal aktif, atau `Reject` untuk menolak.

## Import Soal CSV

Menu: `/admin/questions`

Format kolom:

```csv
exam_title,category_code,question_text,option_a,option_b,option_c,option_d,option_e,correct_answer,explanation,source_reference,score,difficulty
```

Contoh `category_code`: `REGULASI_ASN`, `MANAJEMEN_ASN`, `KEPEMIMPINAN`, `PELAYANAN_PUBLIK`, `STUDI_KASUS`.

## Admin Panel

Menu admin:
- Dashboard Admin
- Manajemen Peserta
- Manajemen Ujian
- Kategori Ujian
- Bank Soal
- Bank Regulasi
- Generate Soal
- Draft Soal AI
- Hasil Ujian
- Rekap Nilai
- Pengaturan Aplikasi

## AI Configuration

```env
AI_PROVIDER=openai
AI_API_KEY=
AI_MODEL=gpt-4o-mini
AI_MAX_TOKENS=3000
AI_TEMPERATURE=0.3
```

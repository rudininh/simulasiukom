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

### Upload dan Preview PDF

1. Buka `/admin/regulations`.
2. Isi judul, nomor regulasi, tahun, kategori, prioritas, deskripsi, dan catatan penggunaan.
3. Upload file PDF/DOCX/TXT.
4. Klik `Detail` untuk melihat informasi regulasi.
5. Klik `Lihat PDF/File` atau `Preview` untuk membaca PDF langsung di aplikasi.
6. Klik `Download PDF/File` untuk mengunduh file asli.

File disimpan di `storage/app/public/regulations` dan dibuka melalui `public/storage`, jadi pastikan `php artisan storage:link` sudah dijalankan.

### Ekstrak Teks PDF/DOCX/TXT

Dari halaman detail regulasi:

1. Klik `Ekstrak Teks`.
2. Untuk TXT, sistem membaca isi file langsung.
3. Untuk DOCX, sistem membaca isi `word/document.xml`.
4. Untuk PDF text-based, sistem mencoba `pdftotext`.
5. Jika teks PDF kosong atau terlalu pendek, status menjadi `need_ocr`.

Command manual:

```bash
php artisan regulation:extract {regulation_id}
```

### OCR PDF Scan

Jika PDF berupa scan/gambar, jalankan OCR dari halaman detail regulasi dengan tombol `OCR PDF`, atau melalui command:

```bash
php artisan regulation:ocr {regulation_id}
```

ENV OCR:

```env
OCR_ENABLED=true
OCR_LANGUAGE=ind
OCR_DPI=300
OCR_MAX_PAGES=200
OCR_BINARY=tesseract
PDF_TO_IMAGE_BINARY=pdftoppm
PDF_TO_TEXT_BINARY=pdftotext
```

Dependency Ubuntu:

```bash
sudo apt install tesseract-ocr tesseract-ocr-ind poppler-utils
```

Dependency Windows:
- Install Tesseract OCR.
- Install Poppler for Windows.
- Tambahkan folder `tesseract.exe`, `pdftoppm.exe`, dan `pdftotext.exe` ke PATH, atau isi path lengkap di `.env`.

### Hasil Ekstraksi dan Ringkasan

Halaman `/admin/regulations/{regulation}/text` menampilkan:
- Status ekstraksi/OCR.
- Metode ekstraksi.
- Jumlah halaman.
- Jumlah karakter.
- Teks hasil ekstraksi.
- Accordion teks per halaman.
- Tombol salin teks, download TXT, generate ringkasan, dan generate soal.

Ringkasan regulasi dapat dibuat dari tombol `Generate Ringkasan Regulasi`. Untuk regulasi izin cerai, ringkasan menyoroti izin perkawinan, izin perceraian, surat keterangan, pemeriksaan pejabat, konsekuensi disiplin, dan prosedur administrasi.

## Generate Soal dari Regulasi

1. Upload regulasi terlebih dahulu.
2. Buka `/admin/question-generator`.
3. Pilih regulasi, ujian tujuan, kategori, jumlah soal, tingkat kesulitan, dan tipe soal.
4. Klik `Generate Draft Soal`.
5. Jika `AI_API_KEY` kosong, sistem memakai dummy generator lokal.
6. Jika `AI_API_KEY` diisi, sistem mengirim prompt ke OpenAI sesuai `config/ai.php`.
7. Review draft di `/admin/generated-questions`.
8. Klik `Approve` agar soal masuk ke bank soal aktif, atau `Reject` untuk menolak.

### Generate Soal dari PDF Regulasi

1. Buka detail regulasi `/admin/regulations/{regulation}`.
2. Pastikan teks tersedia dari ekstraksi atau OCR.
3. Klik `Generate Soal dari PDF`.
4. Pilih ujian, kategori, jumlah soal, tingkat kesulitan, tipe soal, rentang halaman opsional, dan kata kunci fokus.
5. Klik `Generate Draft Soal`.
6. Draft masuk ke `/admin/generated-questions`.
7. Admin wajib review, edit bila perlu, lalu approve.

Jika kategori yang dipilih adalah `PERKAWINAN_PERCERAIAN_ASN`, prompt generator otomatis memakai fokus izin perkawinan, izin perceraian, surat keterangan, pemeriksaan pejabat, kewenangan pejabat, konsekuensi disiplin, dan studi kasus administrasi kepegawaian.

Prompt khusus juga tersedia untuk `ANGKA_KREDIT_JF`, `PENSIUN_PEMBERHENTIAN_PNS`, `PENGADAAN_ASN`, `CUTI_ASN`, dan `PANGKAT_PROMOSI_MUTASI_KARIER`. Untuk `ANGKA_KREDIT_JF`, tipe `Hitungan angka kredit` membuat pembahasan berisi langkah perhitungan.

### Review Draft Soal AI

Halaman `/admin/generated-questions` menyediakan filter regulasi, ujian, kategori, kesulitan, tipe soal, status approval, status validasi, dan keyword. Admin dapat:
- Edit draft soal.
- Approve atau reject per soal.
- Bulk approve, reject, delete, atau assign ujian/kategori.

Saat approve, soal masuk ke Bank Soal dengan `regulation_id`, `explanation`, `source_reference`, `difficulty`, `question_type`, dan `source_page`.

## Regulasi Izin Cerai PNS/ASN

Seeder menambahkan klaster `Perkawinan, Perceraian, dan Izin Keluarga ASN` dengan regulasi:
- PP Nomor 10 Tahun 1983.
- PP Nomor 45 Tahun 1990.
- SE BAKN/BKN Nomor 48/SE/1990.

Kategori soal baru:

```text
PERKAWINAN_PERCERAIAN_ASN - Perkawinan dan Perceraian ASN
```

Materi meliputi izin perkawinan PNS, izin perceraian PNS, surat keterangan perceraian, prosedur permohonan izin, pemeriksaan atasan/pejabat, alasan perceraian, hak dan kewajiban, dampak disiplin, dan studi kasus izin cerai ASN.

## Import Soal CSV

Menu: `/admin/questions`

Format kolom:

```csv
exam_title,category_code,question_text,option_a,option_b,option_c,option_d,option_e,correct_answer,explanation,source_reference,score,difficulty
```

Contoh `category_code`: `REGULASI_ASN`, `MANAJEMEN_ASN`, `KINERJA_KOMPETENSI_ASN`, `KEPEMIMPINAN_MANAJERIAL`, `PELAYANAN_PUBLIK_ETIKA`, `DISIPLIN_ETIKA_NETRALITAS`, `PERKAWINAN_PERCERAIAN_ASN`, `PENSIUN_PEMBERHENTIAN_PNS`, `PENGADAAN_ASN`, `CUTI_ASN`, `PANGKAT_PROMOSI_MUTASI_KARIER`, `ANGKA_KREDIT_JF`.

## Sinkronisasi Kategori dan Komposisi 100 Soal

Jalankan command berikut setelah update aplikasi atau setelah menambah course baru:

```bash
php artisan exam:sync-categories
```

Command ini menambahkan 12 kategori final ke setiap course aktif dan menyetel total komposisi menjadi 100 soal:
- Regulasi ASN: 8
- Manajemen ASN: 8
- Kinerja dan Kompetensi ASN: 8
- Kepemimpinan dan Manajerial: 7
- Pelayanan Publik dan Etika Birokrasi: 7
- Disiplin, Etika, dan Netralitas ASN: 7
- Perkawinan dan Perceraian ASN: 6
- Pensiun dan Pemberhentian PNS: 7
- Pengadaan ASN: 8
- Cuti ASN: 7
- Pangkat, Promosi, Mutasi, dan Karier ASN: 12
- Angka Kredit dan Kenaikan Jenjang Jabatan Fungsional: 15

Admin tetap bisa mengubah jumlah soal per kategori dari menu `Kategori Ujian`.

## Persiapan Penuh Simulasi ASN

Untuk mereset/nonaktifkan course lama, menginput seluruh 46 regulasi rencana, menyiapkan 4 simulasi final, membuat 400 soal aktif, dan menjalankan validasi readiness:

```bash
php artisan asn:prepare-full-simulation
```

Command ini aman dijalankan ulang. Course lama yang tidak termasuk konsep final Manajemen ASN dinonaktifkan, user/admin dan Bank Regulasi tidak dihapus, dan backup ringkas disimpan ke `storage/app/asn-backups`.

Validasi kesiapan:

```bash
php artisan asn:validate-simulation-readiness
```

Output `READY` berarti 4 course aktif tersedia, masing-masing berisi tepat 100 soal aktif, komposisi kategori sesuai, opsi A-E lengkap, pembahasan tersedia, dan scoring dinamis siap.

Reset course lama saja:

```bash
php artisan exam:reset-manajemen-asn
php artisan exam:sync-categories
php artisan exam:seed-questions
```

Seeder regulasi saja:

```bash
php artisan db:seed --class=RegulationSeeder
```

## Download PDF Regulasi dari Internet

Admin dapat mengisi `official_url` berupa halaman detail peraturan atau `pdf_url` berupa link download langsung. Untuk `peraturan.bpk.go.id`, jika `pdf_url` kosong sistem akan membuka halaman `official_url`, mencari link yang mengandung `/Download/`, lalu mengunduh PDF tersebut.

```bash
php artisan regulation:download-pdf {regulation_id}
php artisan regulation:download-pdfs
php artisan regulation:download-bpk
```

Contoh:

```bash
php artisan regulation:download-pdf 1
```

Contoh `official_url`:

```text
https://peraturan.bpk.go.id/Details/269470/uu-no-20-tahun-2023
```

Contoh `pdf_url`:

```text
https://peraturan.bpk.go.id/Download/326904/UU%20Nomor%2020%20Tahun%202023.pdf
```

PDF tersimpan di `storage/app/public/regulations/{tahun}/{slug-title}.pdf`, metadata `pdf_url`, `file_path`, `mime_type`, `file_size`, `download_status`, dan `downloaded_at` diperbarui otomatis. Setelah download berhasil sistem mencoba ekstraksi teks; jika PDF tidak memiliki teks yang cukup, status ekstraksi menjadi `need_ocr`.

Preview PDF tersedia di halaman detail admin dan peserta. Admin bisa download semua file PDF; peserta hanya bisa download jika opsi `Peserta boleh download file regulasi` diaktifkan.

Pastikan storage link sudah tersedia:

```bash
php artisan storage:link
```

Sumber resmi yang diprioritaskan saat mengisi URL manual adalah `peraturan.bpk.go.id`, `jdih.bkn.go.id`, `jdih.menpan.go.id`, `jdih.setneg.go.id`, dan JDIH instansi resmi lain. Jika URL belum bisa dipastikan, regulasi tetap tersimpan dengan status `manual_required` dan admin dapat upload PDF manual.

Command massal lain:

```bash
php artisan regulation:extract-all
php artisan regulation:ocr-all
```

Peserta dapat preview regulasi aktif. Peserta hanya dapat download file jika admin mengaktifkan opsi `Peserta boleh download file regulasi`.

## Regulasi Default

`RegulationSeeder` menginput daftar regulasi Manajemen ASN/Kepegawaian, termasuk UU ASN, Manajemen PNS/PPPK, Kinerja dan Kompetensi ASN, Jabatan Fungsional dan Angka Kredit, Pangkat/Karier, Disiplin, Pelayanan Publik, Pengadaan, Cuti, Pensiun, Perkawinan/Perceraian ASN, SPBE, kesejahteraan, dan regulasi lokal.

Seeder tidak membuat duplikasi aktif: data lama tidak dihapus, tetapi alias/duplikat lama yang tidak canonical dapat dinonaktifkan agar peserta melihat daftar regulasi yang rapi.

## Command Regulasi

```bash
php artisan db:seed --class=RegulationSeeder
php artisan asn:prepare-full-simulation
php artisan asn:prepare-full-simulation --download-pdfs
php artisan asn:prepare-full-simulation --skip-download
php artisan asn:prepare-full-simulation --extract
php artisan asn:prepare-full-simulation --ocr
php artisan asn:validate-simulation-readiness
php artisan exam:reset-manajemen-asn
php artisan exam:sync-categories
php artisan exam:seed-questions
php artisan regulation:download-pdf {regulation_id}
php artisan regulation:download-pdfs
php artisan regulation:download-bpk
php artisan regulation:extract {regulation_id}
php artisan regulation:extract-all
php artisan regulation:ocr {regulation_id}
php artisan regulation:ocr-all
php artisan regulation:summarize {regulation_id}
```

`regulation:extract` membaca TXT/DOCX/PDF text-based. Jika PDF tidak memiliki teks yang cukup, status regulasi menjadi `need_ocr`. `regulation:ocr` menjalankan OCR PDF scan menggunakan Tesseract dan Poppler. `regulation:summarize` membuat ringkasan dari hasil ekstraksi/OCR.

Dependency OCR Ubuntu:

```bash
sudo apt install tesseract-ocr tesseract-ocr-ind poppler-utils
```

## Scoring Dinamis per Kategori

Saat ujian selesai, sistem mengisi tabel `exam_attempt_category_scores` berdasarkan kategori pada database. Halaman hasil, cetak hasil, riwayat, dan rekap admin membaca skor kategori dari tabel ini, sehingga penambahan kategori baru tidak perlu hardcode ulang di halaman hasil.

## Admin Panel

Menu admin:
- Dashboard Admin
- Manajemen Peserta
- Manajemen Ujian
- Kategori Ujian
- Bank Soal
- Bank Regulasi
- Upload Regulasi
- OCR Regulasi
- Generate Soal dari PDF
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

Jika `AI_API_KEY` kosong, aplikasi tetap berjalan memakai dummy generator lokal.

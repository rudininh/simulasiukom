<?php

namespace App\Support;

class AsnCatalog
{
    public static function examCategories(): array
    {
        return [
            ['code' => 'REGULASI_ASN', 'name' => 'Regulasi ASN', 'question_count' => 8],
            ['code' => 'MANAJEMEN_ASN', 'name' => 'Manajemen ASN', 'question_count' => 8],
            ['code' => 'KINERJA_KOMPETENSI_ASN', 'name' => 'Kinerja dan Kompetensi ASN', 'question_count' => 8],
            ['code' => 'KEPEMIMPINAN_MANAJERIAL', 'name' => 'Kepemimpinan dan Manajerial', 'question_count' => 7],
            ['code' => 'PELAYANAN_PUBLIK_ETIKA', 'name' => 'Pelayanan Publik dan Etika Birokrasi', 'question_count' => 7],
            ['code' => 'DISIPLIN_ETIKA_NETRALITAS', 'name' => 'Disiplin, Etika, dan Netralitas ASN', 'question_count' => 7],
            ['code' => 'PERKAWINAN_PERCERAIAN_ASN', 'name' => 'Perkawinan dan Perceraian ASN', 'question_count' => 6],
            ['code' => 'PENSIUN_PEMBERHENTIAN_PNS', 'name' => 'Pensiun dan Pemberhentian PNS', 'question_count' => 7],
            ['code' => 'PENGADAAN_ASN', 'name' => 'Pengadaan ASN', 'question_count' => 8],
            ['code' => 'CUTI_ASN', 'name' => 'Cuti ASN', 'question_count' => 7],
            ['code' => 'PANGKAT_PROMOSI_MUTASI_KARIER', 'name' => 'Pangkat, Promosi, Mutasi, dan Karier ASN', 'question_count' => 12],
            ['code' => 'ANGKA_KREDIT_JF', 'name' => 'Angka Kredit dan Kenaikan Jenjang Jabatan Fungsional', 'question_count' => 15],
        ];
    }

    public static function regulationCategories(): array
    {
        return [
            'Payung Hukum ASN',
            'Manajemen PNS',
            'Manajemen PPPK',
            'Kinerja dan Kompetensi ASN',
            'Jabatan Fungsional dan Angka Kredit',
            'Pangkat, Kenaikan Pangkat, dan Karier PNS',
            'Promosi, Mutasi, dan Manajemen Talenta ASN',
            'Disiplin, Etika, dan Netralitas ASN',
            'Pelayanan Publik dan Administrasi Pemerintahan',
            'Digitalisasi Pemerintahan dan SPBE',
            'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja',
            'Perkawinan, Perceraian, dan Izin Keluarga ASN',
            'Pensiun dan Pemberhentian PNS',
            'Pengadaan ASN',
            'Cuti ASN',
            'Regulasi Lokal/Instansi',
        ];
    }

    public static function defaultRegulations(): array
    {
        return [
            ['UU Nomor 20 Tahun 2023', 2023, 'UU Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara', 'Payung Hukum ASN', 'Sangat Wajib', 'ASN, sistem merit, nilai dasar, kode etik, kode perilaku, manajemen ASN, dan digitalisasi ASN.'],
            ['PP Nomor 11 Tahun 2017', 2017, 'PP Nomor 11 Tahun 2017 tentang Manajemen PNS', 'Manajemen PNS', 'Sangat Wajib', 'Pengadaan PNS, pangkat, jabatan, karier, promosi, mutasi, kinerja, disiplin, pemberhentian, pensiun, dan cuti.'],
            ['PP Nomor 17 Tahun 2020', 2020, 'PP Nomor 17 Tahun 2020 tentang Perubahan atas PP Nomor 11 Tahun 2017', 'Manajemen PNS', 'Sangat Wajib', 'Perubahan ketentuan Manajemen PNS pada PP Nomor 11 Tahun 2017.'],
            ['PP Nomor 49 Tahun 2018', 2018, 'PP Nomor 49 Tahun 2018 tentang Manajemen PPPK', 'Manajemen PPPK', 'Sangat Wajib', 'Pengadaan PPPK, perjanjian kerja, hak/kewajiban PPPK, kinerja, pengembangan kompetensi, cuti PPPK, dan pemutusan hubungan kerja.'],
            ['PermenPANRB Nomor 6 Tahun 2022', 2022, 'PermenPANRB Nomor 6 Tahun 2022 tentang Pengelolaan Kinerja Pegawai ASN', 'Kinerja dan Kompetensi ASN', 'Sangat Wajib', 'Pengelolaan kinerja pegawai ASN, ekspektasi kinerja, dialog kinerja, evaluasi, dan tindak lanjut.'],
            ['PP Nomor 30 Tahun 2019', 2019, 'PP Nomor 30 Tahun 2019 tentang Penilaian Kinerja PNS', 'Kinerja dan Kompetensi ASN', 'Wajib', 'Penilaian kinerja PNS, sasaran kinerja, perilaku kerja, dan pejabat penilai kinerja.'],
            ['PermenPANRB Nomor 38 Tahun 2017', 2017, 'PermenPANRB Nomor 38 Tahun 2017 tentang Standar Kompetensi Jabatan ASN', 'Kinerja dan Kompetensi ASN', 'Wajib', 'Standar kompetensi jabatan ASN, kompetensi manajerial, sosial kultural, dan teknis.'],
            ['Peraturan LAN Nomor 10 Tahun 2018', 2018, 'Peraturan LAN Nomor 10 Tahun 2018 tentang Pengembangan Kompetensi PNS', 'Kinerja dan Kompetensi ASN', 'Wajib', 'Pengembangan kompetensi PNS dan pemenuhan kebutuhan kompetensi jabatan.'],
            ['Peraturan LAN Nomor 15 Tahun 2020', 2020, 'Peraturan LAN Nomor 15 Tahun 2020 tentang Pengembangan Kompetensi PPPK', 'Kinerja dan Kompetensi ASN', 'Pendukung', 'Pengembangan kompetensi PPPK sesuai kebutuhan jabatan dan organisasi.'],
            ['PermenPANRB Nomor 1 Tahun 2023', 2023, 'PermenPANRB Nomor 1 Tahun 2023 tentang Jabatan Fungsional', 'Jabatan Fungsional dan Angka Kredit', 'Sangat Wajib', 'Jabatan fungsional, tugas jabatan, pengangkatan, kinerja, angka kredit, dan kenaikan jenjang.'],
            ['Peraturan BKN Nomor 3 Tahun 2023', 2023, 'Peraturan BKN Nomor 3 Tahun 2023 tentang Angka Kredit, Kenaikan Pangkat, dan Jenjang Jabatan Fungsional', 'Jabatan Fungsional dan Angka Kredit', 'Sangat Wajib Teknis', 'Angka kredit, kenaikan pangkat JF, kenaikan jenjang JF, konversi predikat kinerja, angka kredit kumulatif, dan perhitungan angka kredit.'],
            ['Peraturan BKN Nomor 2 Tahun 2025', 2025, 'Peraturan BKN Nomor 2 Tahun 2025 tentang Kenaikan Pangkat Reguler PNS', 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'Sangat Penting', 'Digunakan untuk materi kenaikan pangkat reguler PNS.'],
            ['Peraturan BKN Nomor 4 Tahun 2025', 2025, 'Peraturan BKN Nomor 4 Tahun 2025 tentang Periodisasi Kenaikan Pangkat PNS', 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'Sangat Penting', 'Digunakan untuk materi periodisasi kenaikan pangkat PNS.'],
            ['Peraturan BKN Nomor 5 Tahun 2019', 2019, 'Peraturan BKN Nomor 5 Tahun 2019 tentang Tata Cara Pelaksanaan Mutasi', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib', 'Digunakan untuk materi mutasi PNS, perpindahan pegawai, dan kebutuhan organisasi.'],
            ['PermenPANRB Nomor 3 Tahun 2020', 2020, 'PermenPANRB Nomor 3 Tahun 2020 tentang Manajemen Talenta ASN', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib Strategis', 'Digunakan untuk materi talent pool, suksesi, karier, dan promosi berbasis talenta.'],
            ['PermenPANRB Nomor 20 Tahun 2025', 2025, 'PermenPANRB Nomor 20 Tahun 2025 tentang Perubahan atas PermenPANRB Nomor 3 Tahun 2020 tentang Manajemen Talenta ASN', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib Terbaru', 'Digunakan sebagai pembaruan materi manajemen talenta ASN.'],
            ['PermenPANRB Nomor 17 Tahun 2021', 2021, 'PermenPANRB Nomor 17 Tahun 2021 tentang Penyetaraan Jabatan Administrasi ke Dalam Jabatan Fungsional', 'Jabatan Fungsional dan Angka Kredit', 'Pendukung', 'Penyetaraan jabatan administrasi ke dalam jabatan fungsional dan implikasi karier ASN.'],
            ['PP Nomor 94 Tahun 2021', 2021, 'PP Nomor 94 Tahun 2021 tentang Disiplin PNS', 'Disiplin, Etika, dan Netralitas ASN', 'Sangat Wajib', 'Kewajiban, larangan, hukuman disiplin, dan prosedur penjatuhan hukuman disiplin PNS.'],
            ['Peraturan BKN Nomor 6 Tahun 2022', 2022, 'Peraturan BKN Nomor 6 Tahun 2022 tentang Pelaksanaan PP Nomor 94 Tahun 2021', 'Disiplin, Etika, dan Netralitas ASN', 'Wajib Teknis', 'Ketentuan teknis pelaksanaan disiplin PNS berdasarkan PP Nomor 94 Tahun 2021.'],
            ['PP Nomor 42 Tahun 2004', 2004, 'PP Nomor 42 Tahun 2004 tentang Pembinaan Jiwa Korps dan Kode Etik PNS', 'Disiplin, Etika, dan Netralitas ASN', 'Wajib', 'Jiwa korps, kode etik PNS, pembinaan etika, dan perilaku aparatur.'],
            ['UU Nomor 28 Tahun 1999', 1999, 'UU Nomor 28 Tahun 1999 tentang Penyelenggaraan Negara yang Bersih dan Bebas dari KKN', 'Disiplin, Etika, dan Netralitas ASN', 'Tambahan Penting', 'Asas penyelenggaraan negara yang bersih, bebas KKN, integritas, dan akuntabilitas.'],
            ['UU Nomor 25 Tahun 2009', 2009, 'UU Nomor 25 Tahun 2009 tentang Pelayanan Publik', 'Pelayanan Publik dan Administrasi Pemerintahan', 'Sangat Wajib', 'Hak dan kewajiban pelayanan publik, standar pelayanan, pengaduan, dan akuntabilitas layanan.'],
            ['UU Nomor 30 Tahun 2014', 2014, 'UU Nomor 30 Tahun 2014 tentang Administrasi Pemerintahan', 'Pelayanan Publik dan Administrasi Pemerintahan', 'Wajib', 'Keputusan/tindakan administrasi pemerintahan, kewenangan, diskresi, dan asas umum pemerintahan yang baik.'],
            ['UU Nomor 14 Tahun 2008', 2008, 'UU Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik', 'Pelayanan Publik dan Administrasi Pemerintahan', 'Tambahan', 'Keterbukaan informasi publik, hak pemohon informasi, kewajiban badan publik, dan pengecualian informasi.'],
            ['Perpres Nomor 95 Tahun 2018', 2018, 'Perpres Nomor 95 Tahun 2018 tentang Sistem Pemerintahan Berbasis Elektronik', 'Digitalisasi Pemerintahan dan SPBE', 'Tambahan', 'Penyelenggaraan SPBE, tata kelola, layanan digital, dan integrasi sistem pemerintahan.'],
            ['Perpres Nomor 132 Tahun 2022', 2022, 'Perpres Nomor 132 Tahun 2022 tentang Arsitektur SPBE Nasional', 'Digitalisasi Pemerintahan dan SPBE', 'Tambahan', 'Arsitektur SPBE Nasional sebagai acuan integrasi proses bisnis, data, aplikasi, infrastruktur, dan keamanan SPBE.'],
            ['Perpres Nomor 21 Tahun 2023', 2023, 'Perpres Nomor 21 Tahun 2023 tentang Hari Kerja dan Jam Kerja Instansi Pemerintah dan Pegawai ASN', 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'Wajib Pendukung', 'Hari kerja, jam kerja, fleksibilitas kerja, dan kewajiban kehadiran pegawai ASN.'],
            ['PP Nomor 5 Tahun 2024', 2024, 'PP Nomor 5 Tahun 2024 tentang Perubahan Gaji PNS', 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'Pendukung', 'Perubahan gaji PNS dan implikasi administrasi penghasilan pegawai.'],
            ['Perpres Nomor 98 Tahun 2020', 2020, 'Perpres Nomor 98 Tahun 2020 tentang Gaji dan Tunjangan PPPK', 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'Pendukung', 'Gaji dan tunjangan PPPK, kelas jabatan, dan hak penghasilan PPPK.'],
            ['PP Nomor 10 Tahun 1983', 1983, 'PP Nomor 10 Tahun 1983 tentang Izin Perkawinan dan Perceraian bagi PNS', 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'Wajib', 'Izin perkawinan dan perceraian bagi PNS, prosedur, kewenangan pejabat, dan konsekuensi disiplin.'],
            ['PP Nomor 45 Tahun 1990', 1990, 'PP Nomor 45 Tahun 1990 tentang Perubahan atas PP Nomor 10 Tahun 1983', 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'Wajib', 'Perubahan ketentuan izin perkawinan dan perceraian bagi PNS.'],
            ['SE BAKN/BKN Nomor 48/SE/1990', 1990, 'SE BAKN/BKN Nomor 48/SE/1990 tentang Petunjuk Pelaksanaan PP Nomor 45 Tahun 1990', 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'Penting Teknis', 'Petunjuk teknis pelaksanaan izin perkawinan dan perceraian PNS.'],
            ['UU Nomor 11 Tahun 1969', 1969, 'Undang-Undang Nomor 11 Tahun 1969 tentang Pensiun Pegawai dan Pensiun Janda/Duda Pegawai', 'Pensiun dan Pemberhentian PNS', 'Wajib', 'Digunakan untuk materi pensiun pegawai, pensiun janda/duda, dan hak pensiun.'],
            ['Peraturan BKN Nomor 2 Tahun 2018', 2018, 'Peraturan BKN Nomor 2 Tahun 2018 tentang Pedoman Pemberian Pertimbangan Teknis Pensiun PNS dan Pensiun Janda/Duda PNS', 'Pensiun dan Pemberhentian PNS', 'Wajib Teknis', 'Digunakan untuk materi pertimbangan teknis pensiun PNS, pensiun janda/duda, proses administrasi, dan kewenangan BKN/Kanreg BKN.'],
            ['Peraturan BKN Nomor 3 Tahun 2020', 2020, 'Peraturan BKN Nomor 3 Tahun 2020 tentang Petunjuk Teknis Pemberhentian Pegawai Negeri Sipil', 'Pensiun dan Pemberhentian PNS', 'Wajib Teknis', 'Digunakan untuk materi jenis pemberhentian PNS, prosedur pemberhentian, pemberhentian sementara, pengaktifan kembali, kewenangan pejabat, dan hak kepegawaian.'],
            ['PermenPANRB Nomor 6 Tahun 2024', 2024, 'PermenPANRB Nomor 6 Tahun 2024 tentang Pengadaan Pegawai ASN', 'Pengadaan ASN', 'Sangat Wajib', 'Digunakan sebagai dasar utama materi pengadaan ASN, baik PNS maupun PPPK.'],
            ['Peraturan BKN Nomor 14 Tahun 2018', 2018, 'Peraturan BKN Nomor 14 Tahun 2018 tentang Petunjuk Teknis Pengadaan PNS', 'Pengadaan ASN', 'Wajib Teknis', 'Digunakan untuk materi teknis pengadaan PNS, tahapan seleksi, pengangkatan CPNS, dan penetapan NIP.'],
            ['Peraturan BKN Nomor 1 Tahun 2019', 2019, 'Peraturan BKN Nomor 1 Tahun 2019 tentang Petunjuk Teknis Pengadaan PPPK', 'Pengadaan ASN', 'Wajib Teknis', 'Digunakan untuk materi teknis pengadaan PPPK, pelamaran, seleksi, pengumuman, dan penetapan NI PPPK.'],
            ['Peraturan BKN Nomor 18 Tahun 2020', 2020, 'Peraturan BKN Nomor 18 Tahun 2020 tentang Perubahan atas Peraturan BKN Nomor 1 Tahun 2019 tentang Petunjuk Teknis Pengadaan PPPK', 'Pengadaan ASN', 'Wajib Teknis', 'Digunakan sebagai pembaruan teknis pengadaan PPPK.'],
            ['Peraturan BKN Nomor 5 Tahun 2024', 2024, 'Peraturan BKN Nomor 5 Tahun 2024 tentang Prosedur Penyelenggaraan Seleksi dengan Metode Computer Assisted Test BKN', 'Pengadaan ASN', 'Wajib Teknis', 'Digunakan untuk materi prosedur seleksi dengan metode CAT BKN, persiapan seleksi, pelaksanaan seleksi, tata tertib, hasil seleksi, dan pelaporan.'],
            ['Peraturan BKN Nomor 9 Tahun 2021', 2021, 'Peraturan BKN Nomor 9 Tahun 2021 tentang Pedoman Penyusunan Pertimbangan Teknis Kebutuhan ASN', 'Pengadaan ASN', 'Penting', 'Digunakan untuk materi perencanaan kebutuhan ASN dan dasar pengadaan ASN.'],
            ['Peraturan BKN Nomor 24 Tahun 2017', 2017, 'Peraturan BKN Nomor 24 Tahun 2017 tentang Tata Cara Pemberian Cuti PNS', 'Cuti ASN', 'Wajib Teknis', 'Digunakan untuk materi cuti PNS, jenis cuti, prosedur permohonan cuti, pejabat berwenang, dan hak cuti PNS.'],
            ['Peraturan BKN Nomor 7 Tahun 2021', 2021, 'Peraturan BKN Nomor 7 Tahun 2021 tentang Perubahan atas Peraturan BKN Nomor 24 Tahun 2017 tentang Tata Cara Pemberian Cuti PNS', 'Cuti ASN', 'Wajib Teknis', 'Digunakan sebagai pembaruan ketentuan cuti PNS.'],
            ['Peraturan BKN Nomor 7 Tahun 2022', 2022, 'Peraturan BKN Nomor 7 Tahun 2022 tentang Tata Cara Pemberian Cuti PPPK', 'Cuti ASN', 'Wajib Teknis', 'Digunakan untuk materi cuti PPPK, jenis cuti PPPK, prosedur pengajuan cuti, pejabat berwenang, dan batasan cuti PPPK.'],
            ['Regulasi Lokal/Instansi', null, 'Regulasi Lokal/Instansi', 'Regulasi Lokal/Instansi', 'Disesuaikan', 'Perwali/Perbup/Pergub tentang disiplin ASN, TPP, jam kerja, presensi, kode etik, mutasi, promosi, pola karier, standar kompetensi jabatan, dan pedoman teknis BKPSDM/BKD.'],
        ];
    }

    public static function regulationUrls(): array
    {
        return [
            'UU Nomor 20 Tahun 2023' => [
                'official_url' => 'https://peraturan.bpk.go.id/Details/269470/uu-no-20-tahun-2023',
                'pdf_url' => 'https://peraturan.bpk.go.id/Download/326904/UU%20Nomor%2020%20Tahun%202023.pdf',
            ],
        ];
    }
}

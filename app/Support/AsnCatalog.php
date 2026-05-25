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
            ['Peraturan BKN Nomor 3 Tahun 2023', 2023, 'Peraturan BKN Nomor 3 Tahun 2023 tentang Angka Kredit, Kenaikan Pangkat, dan Jenjang Jabatan Fungsional', 'Jabatan Fungsional dan Angka Kredit', 'Sangat Wajib Teknis', 'Dasar utama soal hitung-hitungan angka kredit, kenaikan pangkat JF, kenaikan jenjang JF, konversi predikat kinerja, dan angka kredit kumulatif.'],
            ['Peraturan BKN Nomor 2 Tahun 2025', 2025, 'Peraturan BKN Nomor 2 Tahun 2025 tentang Kenaikan Pangkat Reguler PNS', 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'Sangat Penting', 'Digunakan untuk materi kenaikan pangkat reguler PNS.'],
            ['Peraturan BKN Nomor 4 Tahun 2025', 2025, 'Peraturan BKN Nomor 4 Tahun 2025 tentang Periodisasi Kenaikan Pangkat PNS', 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'Sangat Penting', 'Digunakan untuk materi periodisasi kenaikan pangkat PNS.'],
            ['Peraturan BKN Nomor 5 Tahun 2019', 2019, 'Peraturan BKN Nomor 5 Tahun 2019 tentang Tata Cara Pelaksanaan Mutasi', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib', 'Digunakan untuk materi mutasi PNS, perpindahan pegawai, dan kebutuhan organisasi.'],
            ['PermenPANRB Nomor 3 Tahun 2020', 2020, 'PermenPANRB Nomor 3 Tahun 2020 tentang Manajemen Talenta ASN', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib Strategis', 'Digunakan untuk materi talent pool, suksesi, karier, dan promosi berbasis talenta.'],
            ['PermenPANRB Nomor 20 Tahun 2025', 2025, 'PermenPANRB Nomor 20 Tahun 2025 tentang Perubahan atas PermenPANRB Nomor 3 Tahun 2020 tentang Manajemen Talenta ASN', 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'Wajib Terbaru', 'Digunakan sebagai pembaruan materi manajemen talenta ASN.'],
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
        ];
    }
}

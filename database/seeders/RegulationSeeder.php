<?php

namespace Database\Seeders;

use App\Models\Regulation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegulationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::regulations() as $item) {
            $regulation = Regulation::where('title', $item['title'])
                ->where('year', $item['year'])
                ->first();

            if (!$regulation) {
                $regulation = Regulation::where([
                    'title' => $item['title'],
                    'regulation_number' => $item['regulation_number'],
                    'year' => $item['year'],
                ])->first();
            }

            if (!$regulation) {
                $regulation = Regulation::where('year', $item['year'])
                    ->where('title', 'like', $this->titlePrefix($item['title']).'%')
                    ->where('title', 'like', '%Nomor '.$item['regulation_number'].' Tahun '.$item['year'].'%')
                    ->first();
            }

            $hasSource = !empty($item['official_url']) || !empty($item['pdf_url']);
            $data = [
                'regulation_number' => $item['regulation_number'],
                'year' => $item['year'],
                'category' => $item['category'],
                'priority' => $item['priority'],
                'status' => $item['status'],
                'is_active' => true,
                'official_url' => $item['official_url'],
                'pdf_url' => $item['pdf_url'],
                'usage_notes' => $item['usage_notes'],
                'description' => $item['usage_notes'],
                'ocr_language' => config('ocr.language', 'ind'),
                'summary' => $item['usage_notes'],
                'keywords' => array_values(array_unique(array_filter([
                    $item['category'],
                    $item['priority'],
                    'Manajemen ASN',
                    Str::before($item['title'], ' tentang '),
                ]))),
            ];

            if ($regulation) {
                if (!$regulation->file_path) {
                    $data['download_status'] = $hasSource ? 'pending' : 'manual_required';
                    $data['download_error'] = $hasSource ? null : 'official_url/pdf_url belum tersedia';
                }

                $regulation->update($data);
                $this->deactivateDuplicateAliases($regulation, $item);
                continue;
            }

            $regulation = Regulation::create(array_merge($data, [
                'title' => $item['title'],
                'download_status' => $hasSource ? 'pending' : 'manual_required',
                'download_error' => $hasSource ? null : 'official_url/pdf_url belum tersedia',
                'extraction_status' => 'pending',
            ]));
            $this->deactivateDuplicateAliases($regulation, $item);
        }
    }

    private function deactivateDuplicateAliases(Regulation $canonical, array $item): void
    {
        Regulation::where('id', '<>', $canonical->id)
            ->where('year', $item['year'])
            ->where('title', 'like', $this->titlePrefix($item['title']).'%')
            ->where('title', 'like', '%Nomor '.$item['regulation_number'].' Tahun '.$item['year'].'%')
            ->update(['is_active' => false]);

        if (str_starts_with($item['title'], 'Undang-Undang Nomor')) {
            Regulation::where('id', '<>', $canonical->id)
                ->where('year', $item['year'])
                ->where('title', 'like', 'UU Nomor '.$item['regulation_number'].' Tahun '.$item['year'].'%')
                ->update(['is_active' => false]);
        }

        if (str_starts_with($item['title'], 'Peraturan Pemerintah Nomor')) {
            Regulation::where('id', '<>', $canonical->id)
                ->where('year', $item['year'])
                ->where('title', 'like', 'PP Nomor '.$item['regulation_number'].' Tahun '.$item['year'].'%')
                ->update(['is_active' => false]);
        }
    }

    private function titlePrefix(string $title): string
    {
        return trim(Str::before($title, ' Nomor '));
    }

    public static function regulations(): array
    {
        return [
            ['title' => 'Undang-Undang Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara', 'regulation_number' => '20', 'year' => 2023, 'category' => 'Payung Hukum ASN', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/269470/uu-no-20-tahun-2023', 'pdf_url' => 'https://peraturan.bpk.go.id/Download/326904/UU%20Nomor%2020%20Tahun%202023.pdf', 'usage_notes' => 'Payung hukum utama ASN, sistem merit, nilai dasar, kode etik, kode perilaku, manajemen ASN, dan digitalisasi ASN.'],
            ['title' => 'Undang-Undang Nomor 5 Tahun 2014 tentang Aparatur Sipil Negara', 'regulation_number' => '5', 'year' => 2014, 'category' => 'Payung Hukum ASN', 'priority' => 'Referensi Lama', 'status' => 'Dicabut/diganti dengan UU Nomor 20 Tahun 2023', 'official_url' => 'https://peraturan.bpk.go.id/details/38580/uu-no-5-tahun-2014', 'pdf_url' => null, 'usage_notes' => 'Referensi lama untuk perbandingan.'],
            ['title' => 'Peraturan Pemerintah Nomor 11 Tahun 2017 tentang Manajemen Pegawai Negeri Sipil', 'regulation_number' => '11', 'year' => 2017, 'category' => 'Manajemen PNS', 'priority' => 'Sangat Wajib', 'status' => 'Diubah dengan PP Nomor 17 Tahun 2020', 'official_url' => 'https://peraturan.bpk.go.id/Details/5831/pp-no-11-tahun-2017', 'pdf_url' => null, 'usage_notes' => 'Manajemen PNS, pengadaan, pangkat, jabatan, karier, promosi, mutasi, kinerja, disiplin, pemberhentian, pensiun, dan cuti.'],
            ['title' => 'Peraturan Pemerintah Nomor 17 Tahun 2020 tentang Perubahan atas PP Nomor 11 Tahun 2017', 'regulation_number' => '17', 'year' => 2020, 'category' => 'Manajemen PNS', 'priority' => 'Sangat Wajib', 'status' => 'Mengubah PP Nomor 11 Tahun 2017', 'official_url' => 'https://peraturan.bpk.go.id/Details/134462/pp-no-17-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Pembaruan ketentuan Manajemen PNS.'],
            ['title' => 'Peraturan Pemerintah Nomor 49 Tahun 2018 tentang Manajemen PPPK', 'regulation_number' => '49', 'year' => 2018, 'category' => 'Manajemen PPPK', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Manajemen PPPK, pengadaan PPPK, perjanjian kerja, hak dan kewajiban PPPK, cuti PPPK, dan pemutusan hubungan kerja.'],
            ['title' => 'PermenPANRB Nomor 6 Tahun 2022 tentang Pengelolaan Kinerja Pegawai ASN', 'regulation_number' => '6', 'year' => 2022, 'category' => 'Kinerja dan Kompetensi ASN', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/202232/permen-pan-rb-no-6-tahun-2022', 'pdf_url' => null, 'usage_notes' => 'SKP, ekspektasi pimpinan, evaluasi kinerja, dan pembinaan kinerja ASN.'],
            ['title' => 'Peraturan Pemerintah Nomor 30 Tahun 2019 tentang Penilaian Kinerja PNS', 'regulation_number' => '30', 'year' => 2019, 'category' => 'Kinerja dan Kompetensi ASN', 'priority' => 'Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/107573/pp-no-30-tahun-2019', 'pdf_url' => null, 'usage_notes' => 'Penilaian kinerja PNS.'],
            ['title' => 'PermenPANRB Nomor 38 Tahun 2017 tentang Standar Kompetensi Jabatan ASN', 'regulation_number' => '38', 'year' => 2017, 'category' => 'Kinerja dan Kompetensi ASN', 'priority' => 'Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/132633/permen-pan-rb-no-38-tahun-2017', 'pdf_url' => null, 'usage_notes' => 'Kompetensi manajerial, sosial kultural, dan teknis ASN.'],
            ['title' => 'Peraturan LAN Nomor 10 Tahun 2018 tentang Pengembangan Kompetensi PNS', 'regulation_number' => '10', 'year' => 2018, 'category' => 'Kinerja dan Kompetensi ASN', 'priority' => 'Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/227407/peraturan-lan-no-10-tahun-2018', 'pdf_url' => null, 'usage_notes' => 'Pengembangan kompetensi PNS.'],
            ['title' => 'Peraturan LAN Nomor 15 Tahun 2020 tentang Pengembangan Kompetensi PPPK', 'regulation_number' => '15', 'year' => 2020, 'category' => 'Kinerja dan Kompetensi ASN', 'priority' => 'Pendukung', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/190395/peraturan-lan-no-15-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Pengembangan kompetensi PPPK.'],
            ['title' => 'PermenPANRB Nomor 1 Tahun 2023 tentang Jabatan Fungsional', 'regulation_number' => '1', 'year' => 2023, 'category' => 'Jabatan Fungsional dan Angka Kredit', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/240815/permen-pan-rb-no-1-tahun-2023', 'pdf_url' => null, 'usage_notes' => 'Jabatan fungsional, kategori, jenjang, pengangkatan, pengelolaan kinerja, angka kredit, kenaikan pangkat, dan kenaikan jenjang.'],
            ['title' => 'Peraturan BKN Nomor 3 Tahun 2023 tentang Angka Kredit, Kenaikan Pangkat dan Jenjang Jabatan Fungsional', 'regulation_number' => '3', 'year' => 2023, 'category' => 'Jabatan Fungsional dan Angka Kredit', 'priority' => 'Sangat Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/254777/peraturan-bkn-nomor-3-tahun-2023', 'pdf_url' => null, 'usage_notes' => 'Dasar utama soal angka kredit, kenaikan pangkat JF, kenaikan jenjang JF, konversi predikat kinerja, angka kredit kumulatif, dan soal hitung-hitungan.'],
            ['title' => 'Peraturan BKN Nomor 2 Tahun 2025 tentang Kenaikan Pangkat Reguler PNS', 'regulation_number' => '2', 'year' => 2025, 'category' => 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'priority' => 'Sangat Penting', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/319844/peraturan-bkn-no-2-tahun-2025', 'pdf_url' => null, 'usage_notes' => 'Kenaikan pangkat reguler PNS.'],
            ['title' => 'Peraturan BKN Nomor 4 Tahun 2025 tentang Periodisasi Kenaikan Pangkat PNS', 'regulation_number' => '4', 'year' => 2025, 'category' => 'Pangkat, Kenaikan Pangkat, dan Karier PNS', 'priority' => 'Sangat Penting', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/330957/peraturan-bkn-no-4-tahun-2025', 'pdf_url' => null, 'usage_notes' => 'Periodisasi kenaikan pangkat PNS.'],
            ['title' => 'Peraturan BKN Nomor 5 Tahun 2019 tentang Tata Cara Pelaksanaan Mutasi', 'regulation_number' => '5', 'year' => 2019, 'category' => 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'priority' => 'Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Home/Details/140307/peraturan-bkn-no-5-tahun-2019', 'pdf_url' => null, 'usage_notes' => 'Mutasi PNS, perpindahan pegawai, kebutuhan organisasi.'],
            ['title' => 'PermenPANRB Nomor 3 Tahun 2020 tentang Manajemen Talenta ASN', 'regulation_number' => '3', 'year' => 2020, 'category' => 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'priority' => 'Wajib Strategis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/143662/permen-pan-rb-no-3-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Talent pool, suksesi, karier, promosi berbasis talenta.'],
            ['title' => 'PermenPANRB Nomor 20 Tahun 2025 tentang Perubahan atas PermenPANRB Nomor 3 Tahun 2020', 'regulation_number' => '20', 'year' => 2025, 'category' => 'Promosi, Mutasi, dan Manajemen Talenta ASN', 'priority' => 'Wajib Terbaru', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/345784/permen-panrb-no-20-tahun-2025', 'pdf_url' => null, 'usage_notes' => 'Pembaruan manajemen talenta ASN.'],
            ['title' => 'PermenPANRB Nomor 17 Tahun 2021 tentang Penyetaraan Jabatan Administrasi ke Dalam Jabatan Fungsional', 'regulation_number' => '17', 'year' => 2021, 'category' => 'Jabatan Fungsional dan Angka Kredit', 'priority' => 'Pendukung', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Penyetaraan jabatan administrasi ke jabatan fungsional.'],
            ['title' => 'Peraturan Pemerintah Nomor 94 Tahun 2021 tentang Disiplin PNS', 'regulation_number' => '94', 'year' => 2021, 'category' => 'Disiplin, Etika, dan Netralitas ASN', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/177031/pp-no-94-tahun-2021', 'pdf_url' => null, 'usage_notes' => 'Kewajiban, larangan, hukuman disiplin, pejabat berwenang menghukum, dan upaya administratif.'],
            ['title' => 'Peraturan BKN Nomor 6 Tahun 2022 tentang Pelaksanaan PP Nomor 94 Tahun 2021', 'regulation_number' => '6', 'year' => 2022, 'category' => 'Disiplin, Etika, dan Netralitas ASN', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/225406/peraturan-bkn-no-6-tahun-2022', 'pdf_url' => null, 'usage_notes' => 'Teknis pelaksanaan disiplin PNS.'],
            ['title' => 'Peraturan Pemerintah Nomor 42 Tahun 2004 tentang Pembinaan Jiwa Korps dan Kode Etik PNS', 'regulation_number' => '42', 'year' => 2004, 'category' => 'Disiplin, Etika, dan Netralitas ASN', 'priority' => 'Wajib', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Kode etik PNS dan jiwa korps.'],
            ['title' => 'Undang-Undang Nomor 28 Tahun 1999 tentang Penyelenggaraan Negara yang Bersih dan Bebas dari KKN', 'regulation_number' => '28', 'year' => 1999, 'category' => 'Disiplin, Etika, dan Netralitas ASN', 'priority' => 'Tambahan Penting', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Integritas, anti-KKN, konflik kepentingan.'],
            ['title' => 'Undang-Undang Nomor 25 Tahun 2009 tentang Pelayanan Publik', 'regulation_number' => '25', 'year' => 2009, 'category' => 'Pelayanan Publik dan Administrasi Pemerintahan', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/38748/uu-no-25-tahun-2009', 'pdf_url' => null, 'usage_notes' => 'Pelayanan publik, standar pelayanan, hak dan kewajiban, pengaduan.'],
            ['title' => 'Undang-Undang Nomor 30 Tahun 2014 tentang Administrasi Pemerintahan', 'regulation_number' => '30', 'year' => 2014, 'category' => 'Pelayanan Publik dan Administrasi Pemerintahan', 'priority' => 'Wajib', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Keputusan/tindakan pemerintahan, diskresi, penyalahgunaan wewenang.'],
            ['title' => 'Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik', 'regulation_number' => '14', 'year' => 2008, 'category' => 'Pelayanan Publik dan Administrasi Pemerintahan', 'priority' => 'Tambahan', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Transparansi, informasi publik, akuntabilitas.'],
            ['title' => 'Perpres Nomor 95 Tahun 2018 tentang Sistem Pemerintahan Berbasis Elektronik', 'regulation_number' => '95', 'year' => 2018, 'category' => 'Digitalisasi Pemerintahan dan SPBE', 'priority' => 'Tambahan', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'SPBE, digitalisasi birokrasi, layanan digital.'],
            ['title' => 'Perpres Nomor 132 Tahun 2022 tentang Arsitektur SPBE Nasional', 'regulation_number' => '132', 'year' => 2022, 'category' => 'Digitalisasi Pemerintahan dan SPBE', 'priority' => 'Tambahan', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Arsitektur SPBE nasional.'],
            ['title' => 'Perpres Nomor 21 Tahun 2023 tentang Hari Kerja dan Jam Kerja Instansi Pemerintah dan Pegawai ASN', 'regulation_number' => '21', 'year' => 2023, 'category' => 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'priority' => 'Wajib Pendukung', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/247257/perpres-no-21-tahun-2023', 'pdf_url' => null, 'usage_notes' => 'Hari kerja, jam kerja, presensi, disiplin waktu.'],
            ['title' => 'Peraturan Pemerintah Nomor 5 Tahun 2024 tentang Perubahan Gaji PNS', 'regulation_number' => '5', 'year' => 2024, 'category' => 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'priority' => 'Pendukung', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/276755/pp-no-5-tahun-2024', 'pdf_url' => null, 'usage_notes' => 'Gaji PNS.'],
            ['title' => 'Perpres Nomor 98 Tahun 2020 tentang Gaji dan Tunjangan PPPK', 'regulation_number' => '98', 'year' => 2020, 'category' => 'Kesejahteraan, Gaji, Tunjangan, dan Jam Kerja', 'priority' => 'Pendukung', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/147306/perpres-no-98-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Gaji dan tunjangan PPPK.'],
            ['title' => 'Peraturan Pemerintah Nomor 10 Tahun 1983 tentang Izin Perkawinan dan Perceraian bagi PNS', 'regulation_number' => '10', 'year' => 1983, 'category' => 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'priority' => 'Wajib', 'status' => 'Diubah dengan PP Nomor 45 Tahun 1990', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Izin perkawinan dan perceraian PNS.'],
            ['title' => 'Peraturan Pemerintah Nomor 45 Tahun 1990 tentang Perubahan atas PP Nomor 10 Tahun 1983', 'regulation_number' => '45', 'year' => 1990, 'category' => 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'priority' => 'Wajib', 'status' => 'Berlaku sebagai perubahan PP Nomor 10 Tahun 1983', 'official_url' => 'https://peraturan.bpk.go.id/Details/60959/pp-no-45-tahun-1990', 'pdf_url' => null, 'usage_notes' => 'Perubahan ketentuan izin perkawinan dan perceraian PNS.'],
            ['title' => 'SE BAKN/BKN Nomor 48/SE/1990 tentang Petunjuk Pelaksanaan PP Nomor 45 Tahun 1990', 'regulation_number' => '48/SE/1990', 'year' => 1990, 'category' => 'Perkawinan, Perceraian, dan Izin Keluarga ASN', 'priority' => 'Penting Teknis', 'status' => 'Perlu upload manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Petunjuk teknis izin perkawinan dan perceraian PNS.'],
            ['title' => 'Undang-Undang Nomor 11 Tahun 1969 tentang Pensiun Pegawai dan Pensiun Janda/Duda Pegawai', 'regulation_number' => '11', 'year' => 1969, 'category' => 'Pensiun dan Pemberhentian PNS', 'priority' => 'Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/49112/uu-no-11-tahun-1969', 'pdf_url' => null, 'usage_notes' => 'Pensiun pegawai dan pensiun janda/duda pegawai.'],
            ['title' => 'Peraturan BKN Nomor 2 Tahun 2018 tentang Pedoman Pemberian Pertimbangan Teknis Pensiun PNS dan Pensiun Janda/Duda PNS', 'regulation_number' => '2', 'year' => 2018, 'category' => 'Pensiun dan Pemberhentian PNS', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/140469/peraturan-bkn-no-2-tahun-2018', 'pdf_url' => null, 'usage_notes' => 'Pertimbangan teknis pensiun PNS dan pensiun janda/duda PNS.'],
            ['title' => 'Peraturan BKN Nomor 3 Tahun 2020 tentang Petunjuk Teknis Pemberhentian Pegawai Negeri Sipil', 'regulation_number' => '3', 'year' => 2020, 'category' => 'Pensiun dan Pemberhentian PNS', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/140295/peraturan-bkn-no-3-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Jenis pemberhentian PNS, prosedur pemberhentian, pemberhentian sementara, pengaktifan kembali, kewenangan pejabat, dan hak kepegawaian.'],
            ['title' => 'PermenPANRB Nomor 6 Tahun 2024 tentang Pengadaan Pegawai ASN', 'regulation_number' => '6', 'year' => 2024, 'category' => 'Pengadaan ASN', 'priority' => 'Sangat Wajib', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/294203/permen-panrb-no-6', 'pdf_url' => null, 'usage_notes' => 'Pengadaan ASN terdiri dari PNS dan PPPK, panitia pengadaan, tahapan seleksi, SKD, SKB, pengawasan, dan pelaporan.'],
            ['title' => 'Peraturan BKN Nomor 14 Tahun 2018 tentang Petunjuk Teknis Pengadaan PNS', 'regulation_number' => '14', 'year' => 2018, 'category' => 'Pengadaan ASN', 'priority' => 'Wajib Teknis', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Teknis pengadaan PNS, tahapan seleksi, pengangkatan CPNS, dan penetapan NIP.'],
            ['title' => 'Peraturan BKN Nomor 1 Tahun 2019 tentang Petunjuk Teknis Pengadaan PPPK', 'regulation_number' => '1', 'year' => 2019, 'category' => 'Pengadaan ASN', 'priority' => 'Wajib Teknis', 'status' => 'Diubah dengan Peraturan BKN Nomor 18 Tahun 2020', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Teknis pengadaan PPPK, pelamaran, seleksi, pengumuman, dan penetapan NI PPPK.'],
            ['title' => 'Peraturan BKN Nomor 18 Tahun 2020 tentang Perubahan atas Peraturan BKN Nomor 1 Tahun 2019', 'regulation_number' => '18', 'year' => 2020, 'category' => 'Pengadaan ASN', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/166401/peraturan-bkn-no-18-tahun-2020', 'pdf_url' => null, 'usage_notes' => 'Pembaruan teknis pengadaan PPPK.'],
            ['title' => 'Peraturan BKN Nomor 5 Tahun 2024 tentang Prosedur Penyelenggaraan Seleksi dengan Metode CAT BKN', 'regulation_number' => '5', 'year' => 2024, 'category' => 'Pengadaan ASN', 'priority' => 'Wajib Teknis', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Prosedur seleksi dengan metode CAT BKN.'],
            ['title' => 'Peraturan BKN Nomor 9 Tahun 2021 tentang Pedoman Penyusunan Pertimbangan Teknis Kebutuhan ASN', 'regulation_number' => '9', 'year' => 2021, 'category' => 'Pengadaan ASN', 'priority' => 'Penting', 'status' => 'Perlu cek/manual', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Perencanaan kebutuhan ASN.'],
            ['title' => 'Peraturan BKN Nomor 24 Tahun 2017 tentang Tata Cara Pemberian Cuti PNS', 'regulation_number' => '24', 'year' => 2017, 'category' => 'Cuti ASN', 'priority' => 'Wajib Teknis', 'status' => 'Diubah dengan Peraturan BKN Nomor 7 Tahun 2021', 'official_url' => null, 'pdf_url' => null, 'usage_notes' => 'Cuti PNS, jenis cuti, prosedur permohonan cuti, pejabat berwenang, dan hak cuti PNS.'],
            ['title' => 'Peraturan BKN Nomor 7 Tahun 2021 tentang Perubahan atas Peraturan BKN Nomor 24 Tahun 2017', 'regulation_number' => '7', 'year' => 2021, 'category' => 'Cuti ASN', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/177987/perka-bkn-no-7-tahun-2021', 'pdf_url' => null, 'usage_notes' => 'Pembaruan tata cara pemberian cuti PNS.'],
            ['title' => 'Peraturan BKN Nomor 7 Tahun 2022 tentang Tata Cara Pemberian Cuti PPPK', 'regulation_number' => '7', 'year' => 2022, 'category' => 'Cuti ASN', 'priority' => 'Wajib Teknis', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/225412/peraturan-bkn-no-7-tahun-2022', 'pdf_url' => null, 'usage_notes' => 'Cuti PPPK, jenis cuti PPPK, prosedur pengajuan cuti, pejabat berwenang, dan batasan cuti PPPK.'],
            ['title' => 'Perwali Kota Banjarmasin Nomor 94 Tahun 2021 tentang Tata Cara Penegakan Disiplin PNS di Lingkungan Pemerintah Kota Banjarmasin', 'regulation_number' => '94', 'year' => 2021, 'category' => 'Regulasi Lokal/Instansi', 'priority' => 'Lokal Penting', 'status' => 'Berlaku', 'official_url' => 'https://peraturan.bpk.go.id/Details/249423/perwali-kota-banjarmasin-no-94-tahun-2021', 'pdf_url' => null, 'usage_notes' => 'Regulasi lokal tentang penegakan disiplin PNS di Pemerintah Kota Banjarmasin.'],
        ];
    }
}

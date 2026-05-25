<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\Regulation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator CAT',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'peserta@example.com'], [
            'name' => 'Rudini Nor Habibi',
            'username' => 'peserta',
            'password' => Hash::make('password'),
            'role' => 'peserta',
            'phone' => '081234567890',
            'institution' => 'Instansi Pemerintah',
            'position_name' => 'Analis SDM Aparatur',
            'work_unit' => 'Bidang Pengembangan Kompetensi',
            'employee_number' => '198901012020121001',
        ]);

        $regulation = Regulation::updateOrCreate(['title' => 'Undang-Undang Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara'], [
            'regulation_number' => 'UU Nomor 20 Tahun 2023',
            'year' => 2023,
            'category' => 'Regulasi ASN',
            'priority' => 'Wajib',
            'description' => 'Regulasi utama mengenai Aparatur Sipil Negara, manajemen ASN, sistem merit, nilai dasar, kode etik, dan pengembangan kompetensi.',
            'usage_notes' => 'Sumber utama materi regulasi ASN dan manajemen ASN.',
            'extracted_text' => 'UU ASN Tahun 2023 menegaskan bahwa manajemen ASN diselenggarakan berdasarkan sistem merit, nilai dasar ASN, kode etik dan kode perilaku, pengembangan kompetensi, manajemen kinerja, netralitas, serta pelayanan publik yang profesional dan akuntabel.',
            'extraction_status' => 'extracted',
            'extraction_method' => 'seed',
            'extracted_at' => now(),
            'summary' => 'Pokok pengaturan meliputi manajemen ASN, sistem merit, nilai dasar, kode etik, pengembangan kompetensi, kinerja, dan pelayanan publik.',
            'keywords' => ['sistem merit', 'manajemen ASN', 'nilai dasar ASN', 'kode etik'],
            'status' => 'active',
            'uploaded_by' => $admin->id,
        ]);

        $divorceRegulations = [
            ['PP Nomor 10 Tahun 1983', 'Peraturan Pemerintah Nomor 10 Tahun 1983 tentang Izin Perkawinan dan Perceraian bagi Pegawai Negeri Sipil', 1983, 'Diubah', 'Regulasi dasar mengenai izin perkawinan dan perceraian bagi PNS.'],
            ['PP Nomor 45 Tahun 1990', 'Peraturan Pemerintah Nomor 45 Tahun 1990 tentang Perubahan atas Peraturan Pemerintah Nomor 10 Tahun 1983 tentang Izin Perkawinan dan Perceraian bagi Pegawai Negeri Sipil', 1990, 'Berlaku sebagai perubahan PP 10 Tahun 1983', 'Regulasi perubahan yang penting untuk prosedur izin perkawinan dan perceraian PNS.'],
            ['SE BAKN/BKN Nomor 48/SE/1990', 'Surat Edaran tentang Petunjuk Pelaksanaan PP Nomor 45 Tahun 1990 tentang Izin Perkawinan dan Perceraian bagi PNS', 1990, 'Petunjuk pelaksanaan', 'Digunakan sebagai bahan teknis dan studi kasus prosedur izin cerai.'],
        ];

        foreach ($divorceRegulations as [$number, $title, $year, $statusText, $note]) {
            Regulation::updateOrCreate(['title' => $title], [
                'regulation_number' => $number,
                'year' => $year,
                'category' => 'Perkawinan, Perceraian, dan Izin Keluarga ASN',
                'priority' => str_contains($number, 'SE') ? 'Penting' : 'Wajib',
                'description' => $note,
                'usage_notes' => 'Materi izin perkawinan PNS, izin perceraian PNS, surat keterangan perceraian, pemeriksaan pejabat, dan konsekuensi disiplin.',
                'extracted_text' => 'Regulasi izin perkawinan dan perceraian PNS mengatur kewajiban memperoleh izin atau surat keterangan dari pejabat berwenang sebelum perceraian, prosedur permohonan izin, pemeriksaan alasan oleh atasan atau pejabat, pertimbangan administratif, hak dan kewajiban setelah perceraian, dampak kepegawaian, serta hukuman disiplin jika PNS melanggar ketentuan.',
                'extraction_status' => 'extracted',
                'extraction_method' => 'seed',
                'extracted_at' => now(),
                'summary' => "Pokok pengaturan: izin perkawinan dan perceraian PNS.\nKewajiban: memperoleh izin atau surat keterangan dari pejabat.\nProsedur: pengajuan, pemeriksaan alasan, pertimbangan pejabat, dan keputusan.\nKonsekuensi: pelanggaran prosedur dapat berdampak pada disiplin PNS.",
                'keywords' => ['izin perkawinan', 'izin perceraian', 'surat keterangan', 'pejabat berwenang', 'hukuman disiplin'],
                'status' => 'active',
                'uploaded_by' => $admin->id,
            ]);
        }

        $exams = [
            ['Simulasi CAT Uji Kompetensi Jabatan Administrator', 90, 100, 70, 'Jabatan Administrator'],
            ['Simulasi CAT Uji Kompetensi Jabatan Pengawas', 90, 100, 70, 'Jabatan Pengawas'],
            ['Simulasi CAT Uji Kompetensi Jabatan Fungsional Manajemen ASN', 75, 80, 70, 'Jabatan Fungsional Manajemen ASN'],
            ['Simulasi CAT Uji Kompetensi Jabatan Pimpinan Tinggi Pratama', 120, 120, 75, 'Jabatan Pimpinan Tinggi Pratama'],
        ];

        foreach ($exams as [$title, $duration, $total, $passing, $type]) {
            $exam = Exam::updateOrCreate(['title' => $title], [
                'description' => 'Simulasi ini dirancang untuk mengukur pemahaman peserta terhadap regulasi ASN, manajemen ASN, kepemimpinan birokrasi, pelayanan publik, etika birokrasi, dan studi kasus jabatan Manajemen ASN.',
                'exam_type' => $type,
                'duration_minutes' => $duration,
                'total_questions' => $total,
                'passing_grade' => $passing,
                'available_from' => '2026-01-01',
                'available_until' => '2026-12-31',
                'regulation_basis' => 'UU ASN Tahun 2023',
                'is_active' => true,
            ]);
            $this->seedCategories($exam, $total);
        }

        $administrator = Exam::where('title', 'Simulasi CAT Uji Kompetensi Jabatan Administrator')->first();
        if ($administrator->questions()->count() < 100) {
            $this->seedAdministratorQuestions($administrator, $regulation);
        }
    }

    private function seedCategories(Exam $exam, int $total): void
    {
        $counts = $total === 100
            ? [20, 20, 15, 15, 15, 15]
            : [ceil($total * .20), ceil($total * .20), ceil($total * .15), floor($total * .15), floor($total * .15), $total - ceil($total * .20) - ceil($total * .20) - ceil($total * .15) - floor($total * .15) - floor($total * .15)];

        $categories = [
            ['REGULASI_ASN', 'Regulasi ASN', $counts[0]],
            ['MANAJEMEN_ASN', 'Manajemen ASN', $counts[1]],
            ['KEPEMIMPINAN', 'Kepemimpinan dan Manajerial', $counts[2]],
            ['PELAYANAN_PUBLIK', 'Pelayanan Publik dan Etika Birokrasi', $counts[3]],
            ['STUDI_KASUS', 'Studi Kasus Manajemen ASN', $counts[4]],
            ['PERKAWINAN_PERCERAIAN_ASN', 'Perkawinan dan Perceraian ASN', $counts[5]],
        ];

        foreach ($categories as [$code, $name, $count]) {
            ExamCategory::updateOrCreate(['exam_id' => $exam->id, 'code' => $code], [
                'exam_id' => $exam->id,
                'name' => $name,
                'question_count' => $count,
                'passing_score' => null,
                'weight' => 1,
            ]);
        }
    }

    private function seedAdministratorQuestions(Exam $exam, Regulation $regulation): void
    {
        $categories = $exam->categories->keyBy('code');
        $divorceRegulation = Regulation::where('category', 'Perkawinan, Perceraian, dan Izin Keluarga ASN')->first() ?: $regulation;
        $topics = [
            'REGULASI_ASN' => [
                'sistem merit', 'nilai dasar ASN', 'kode etik ASN', 'netralitas ASN', 'kedudukan ASN',
                'fungsi ASN', 'peran ASN', 'manajemen ASN', 'hak dan kewajiban ASN', 'kode perilaku ASN',
            ],
            'MANAJEMEN_ASN' => [
                'perencanaan kebutuhan ASN', 'pengadaan ASN', 'pengembangan kompetensi', 'manajemen kinerja', 'promosi dan mutasi',
                'disiplin ASN', 'pemberhentian ASN', 'digitalisasi manajemen ASN', 'talent management', 'penilaian kinerja',
            ],
            'KEPEMIMPINAN' => [
                'pengambilan keputusan', 'manajemen perubahan', 'koordinasi lintas unit', 'komunikasi birokrasi', 'pengendalian tugas',
                'delegasi pekerjaan', 'penyelesaian konflik', 'kepemimpinan sektor publik', 'orientasi hasil', 'kolaborasi',
            ],
            'PELAYANAN_PUBLIK' => [
                'integritas', 'akuntabilitas', 'anti korupsi', 'konflik kepentingan', 'profesionalisme ASN',
                'standar pelayanan', 'responsivitas layanan', 'transparansi', 'keadilan layanan', 'etika birokrasi',
            ],
            'STUDI_KASUS' => [
                'mutasi pegawai', 'disiplin ASN', 'konflik kepentingan', 'kinerja pegawai', 'pelayanan publik',
                'penyalahgunaan wewenang', 'penempatan pegawai', 'evaluasi bawahan', 'keluhan masyarakat', 'koordinasi program',
            ],
            'PERKAWINAN_PERCERAIAN_ASN' => [
                'izin perkawinan PNS', 'izin perceraian PNS', 'surat keterangan perceraian', 'prosedur permohonan izin', 'pemeriksaan atasan',
                'alasan perceraian', 'hak dan kewajiban PNS', 'dampak disiplin', 'studi kasus izin cerai ASN', 'kewenangan pejabat',
            ],
        ];

        $order = 1;
        foreach ($categories as $code => $category) {
            for ($i = 1; $i <= $category->question_count; $i++) {
                $topic = $topics[$code][($i - 1) % count($topics[$code])];
                if ($code === 'PERKAWINAN_PERCERAIAN_ASN') {
                    $this->createDivorceQuestion($exam, $category, $divorceRegulation, $order++, $i, true);
                    continue;
                }

                $casePrefix = $code === 'STUDI_KASUS'
                    ? "Seorang pejabat administrator menghadapi kasus {$topic} di unit kerjanya."
                    : "Dalam konteks {$topic},";

                Question::create([
                    'exam_id' => $exam->id,
                    'exam_category_id' => $category->id,
                    'regulation_id' => $regulation->id,
                    'question_text' => "{$casePrefix} tindakan yang paling sesuai dengan prinsip Uji Kompetensi Jabatan Manajemen ASN adalah ...",
                    'option_a' => 'Mengambil keputusan berdasarkan kedekatan personal dan kebiasaan lama',
                    'option_b' => 'Mengutamakan kualifikasi, kompetensi, kinerja, integritas, dan kebutuhan organisasi',
                    'option_c' => 'Menunda keputusan sampai ada arahan informal dari pihak luar',
                    'option_d' => 'Mengabaikan dokumentasi agar proses birokrasi terlihat lebih cepat',
                    'option_e' => 'Memilih opsi yang paling menguntungkan kelompok tertentu',
                    'correct_answer' => 'B',
                    'explanation' => 'Prinsip manajemen ASN menekankan objektivitas, sistem merit, akuntabilitas, integritas, dan kesesuaian dengan kebutuhan organisasi.',
                    'source_reference' => 'UU ASN Tahun 2023',
                    'score' => 1,
                    'difficulty' => $code === 'STUDI_KASUS' ? 'case' : (($i % 3 === 0) ? 'hard' : (($i % 2 === 0) ? 'medium' : 'easy')),
                    'order_number' => $order++,
                    'is_active' => true,
                ]);
            }
        }

        $divorceCategory = $categories['PERKAWINAN_PERCERAIAN_ASN'];
        for ($i = $divorceCategory->question_count + 1; $i <= 20; $i++) {
            $this->createDivorceQuestion($exam, $divorceCategory, $divorceRegulation, $order++, $i, false);
        }
    }

    private function createDivorceQuestion(Exam $exam, ExamCategory $category, Regulation $regulation, int $order, int $index, bool $active): void
    {
        $templates = [
            [
                'Seorang PNS yang akan melakukan perceraian wajib terlebih dahulu memperoleh apa dari pejabat yang berwenang?',
                ['Surat tugas luar daerah', 'Izin atau surat keterangan dari pejabat', 'Persetujuan rekan kerja', 'Surat cuti tahunan', 'Rekomendasi bendahara gaji'],
                'B',
                'Ketentuan izin perkawinan dan perceraian PNS mengatur bahwa PNS yang akan melakukan perceraian wajib memperoleh izin atau surat keterangan terlebih dahulu dari pejabat yang berwenang.',
            ],
            [
                'Dalam menangani permohonan izin cerai PNS, tindakan awal yang paling tepat dilakukan pejabat/atasan adalah ...',
                ['Langsung menyetujui permohonan tanpa pemeriksaan', 'Menolak semua permohonan perceraian PNS', 'Memeriksa alasan, memanggil pihak terkait, dan menilai kesesuaian dengan ketentuan', 'Menyerahkan seluruh proses kepada rekan kerja pemohon', 'Menghapus data kepegawaian pemohon'],
                'C',
                'Permohonan izin cerai perlu diperiksa secara administratif dan substantif sesuai ketentuan, termasuk alasan dan kelengkapan dokumen.',
            ],
            [
                'Jika seorang PNS melakukan perceraian tanpa mengikuti prosedur izin yang ditentukan, aspek kepegawaian yang dapat timbul adalah ...',
                ['Pemberian penghargaan otomatis', 'Penghapusan kewajiban kerja', 'Konsekuensi disiplin sesuai ketentuan kepegawaian', 'Kenaikan pangkat otomatis', 'Pembebasan dari evaluasi kinerja'],
                'C',
                'Pelanggaran terhadap ketentuan izin perkawinan dan perceraian PNS dapat berdampak pada aspek disiplin kepegawaian.',
            ],
        ];

        $extraTopics = [
            'kelengkapan dokumen permohonan izin cerai',
            'kewenangan pejabat dalam memberikan izin',
            'pemeriksaan alasan perceraian oleh atasan',
            'surat keterangan untuk perceraian',
            'hak dan kewajiban setelah perceraian',
            'keterkaitan pelanggaran prosedur dengan disiplin PNS',
            'pertimbangan atasan dalam studi kasus rumah tangga PNS',
            'larangan mengabaikan prosedur administratif',
            'peran unit pengelola kepegawaian',
            'pencatatan administrasi kepegawaian setelah putusan cerai',
            'penilaian alasan yang dapat dipertimbangkan',
            'perlindungan akuntabilitas keputusan pejabat',
            'pemeriksaan pihak terkait sebelum keputusan',
            'penyusunan rekomendasi izin cerai',
            'evaluasi dampak perceraian terhadap kewajiban PNS',
            'penerapan PP 10 Tahun 1983 dan PP 45 Tahun 1990',
            'penggunaan SE BAKN/BKN sebagai petunjuk teknis',
        ];

        if ($index <= 3) {
            [$text, $options, $answer, $explanation] = $templates[$index - 1];
        } else {
            $topic = $extraTopics[($index - 4) % count($extraTopics)];
            $text = "Dalam studi kasus {$topic}, keputusan pejabat pengelola kepegawaian yang paling tepat adalah ...";
            $options = [
                'Mengabaikan permohonan karena urusan rumah tangga bersifat pribadi sepenuhnya',
                'Memproses secara administratif, memeriksa alasan, dan mendokumentasikan pertimbangan sesuai ketentuan',
                'Menyetujui permohonan hanya berdasarkan hubungan personal dengan pemohon',
                'Meminta pemohon menyelesaikan sendiri tanpa pencatatan kepegawaian',
                'Memberikan hukuman sebelum pemeriksaan dilakukan',
            ];
            $answer = 'B';
            $explanation = 'Pejabat harus memproses permohonan izin cerai secara tertib, objektif, terdokumentasi, dan sesuai regulasi izin perkawinan dan perceraian PNS.';
        }

        Question::create([
            'exam_id' => $exam->id,
            'exam_category_id' => $category->id,
            'regulation_id' => $regulation->id,
            'question_text' => $text,
            'option_a' => $options[0],
            'option_b' => $options[1],
            'option_c' => $options[2],
            'option_d' => $options[3],
            'option_e' => $options[4],
            'correct_answer' => $answer,
            'explanation' => $explanation,
            'source_reference' => 'PP 10 Tahun 1983 jo. PP 45 Tahun 1990',
            'question_type' => $index % 2 === 0 ? 'Analisis kasus' : 'Prosedur administratif',
            'source_page' => null,
            'score' => 1,
            'difficulty' => $index % 3 === 0 ? 'case' : 'medium',
            'order_number' => $order,
            'is_active' => $active,
        ]);
    }
}

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
            'description' => 'Regulasi utama mengenai Aparatur Sipil Negara, manajemen ASN, sistem merit, nilai dasar, kode etik, dan pengembangan kompetensi.',
            'extracted_text' => 'UU ASN Tahun 2023 menegaskan bahwa manajemen ASN diselenggarakan berdasarkan sistem merit, nilai dasar ASN, kode etik dan kode perilaku, pengembangan kompetensi, manajemen kinerja, netralitas, serta pelayanan publik yang profesional dan akuntabel.',
            'status' => 'active',
            'uploaded_by' => $admin->id,
        ]);

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
            ? [25, 25, 20, 15, 15]
            : [ceil($total * .25), ceil($total * .25), ceil($total * .2), floor($total * .15), $total - (ceil($total * .25) * 2) - ceil($total * .2) - floor($total * .15)];

        $categories = [
            ['REGULASI_ASN', 'Regulasi ASN', $counts[0]],
            ['MANAJEMEN_ASN', 'Manajemen ASN', $counts[1]],
            ['KEPEMIMPINAN', 'Kepemimpinan dan Manajerial', $counts[2]],
            ['PELAYANAN_PUBLIK', 'Pelayanan Publik dan Etika Birokrasi', $counts[3]],
            ['STUDI_KASUS', 'Studi Kasus Manajemen ASN', $counts[4]],
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
        ];

        $order = 1;
        foreach ($categories as $code => $category) {
            for ($i = 1; $i <= $category->question_count; $i++) {
                $topic = $topics[$code][($i - 1) % count($topics[$code])];
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
    }
}

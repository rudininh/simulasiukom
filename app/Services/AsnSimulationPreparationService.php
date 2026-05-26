<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\Regulation;
use App\Models\User;
use App\Support\AsnCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AsnSimulationPreparationService
{
    public function resetOldSimulation(): array
    {
        $finalTitles = array_column($this->finalExams(), 'title');
        $oldExams = Exam::whereNotIn('title', $finalTitles)->get();

        if ($oldExams->isNotEmpty()) {
            Storage::disk('local')->put(
                'asn-backups/exam-reset-'.now()->format('Ymd-His').'.json',
                json_encode([
                    'created_at' => now()->toIso8601String(),
                    'exams' => $oldExams->map(fn (Exam $exam) => [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'categories' => $exam->categories()->count(),
                        'questions' => $exam->questions()->count(),
                        'attempts' => $exam->attempts()->count(),
                    ])->values(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }

        $oldExamIds = $oldExams->pluck('id');
        Question::whereIn('exam_id', $oldExamIds)->update(['is_active' => false]);
        Exam::whereIn('id', $oldExamIds)->update(['is_active' => false]);

        return [
            'archived_exams' => $oldExams->count(),
            'archived_questions' => Question::whereIn('exam_id', $oldExamIds)->count(),
        ];
    }

    public function seedRegulations(?int $adminId = null): int
    {
        $adminId = $adminId ?: User::where('role', 'admin')->value('id');
        $count = 0;

        foreach (AsnCatalog::defaultRegulations() as [$number, $year, $title, $category, $priority, $note]) {
            Regulation::updateOrCreate([
                'title' => $title,
                'regulation_number' => $number,
                'year' => $year,
                'category' => $category,
            ], [
                'priority' => $priority,
                'description' => $note,
                'usage_notes' => $note,
                'download_status' => 'manual_required',
                'download_error' => 'URL PDF resmi belum diisi. Admin dapat upload manual atau mengisi pdf_url lalu menjalankan download.',
                'ocr_language' => config('ocr.language', 'ind'),
                'extracted_text' => $this->regulationSeedText($category, $number, $note),
                'extraction_status' => 'extracted',
                'extraction_method' => 'seed',
                'extraction_error' => null,
                'extracted_at' => now(),
                'summary' => "Pokok pengaturan: {$note}\nMateri potensial untuk soal: pemahaman ketentuan, prosedur administratif, penerapan aturan, kewenangan pejabat, dan studi kasus Manajemen ASN.",
                'keywords' => $this->keywordsFor($category, $number),
                'status' => 'active',
                'uploaded_by' => $adminId,
            ]);
            $count++;
        }

        return $count;
    }

    public function seedCoursesAndQuestions(): array
    {
        return DB::transaction(function () {
            $report = [];

            foreach ($this->finalExams() as $examSpec) {
                $exam = Exam::updateOrCreate(['title' => $examSpec['title']], [
                    'description' => 'Simulasi CAT Uji Kompetensi Manajemen ASN dengan komposisi kategori final dan scoring dinamis per kategori.',
                    'exam_type' => $examSpec['type'],
                    'duration_minutes' => $examSpec['duration'],
                    'total_questions' => 100,
                    'passing_grade' => $examSpec['passing_grade'],
                    'available_from' => '2026-01-01',
                    'available_until' => null,
                    'regulation_basis' => 'Bank Regulasi Manajemen ASN',
                    'is_active' => true,
                ]);

                $order = 1;
                foreach (AsnCatalog::examCategories() as $categorySpec) {
                    $category = ExamCategory::updateOrCreate([
                        'exam_id' => $exam->id,
                        'code' => $categorySpec['code'],
                    ], [
                        'name' => $categorySpec['name'],
                        'question_count' => $categorySpec['question_count'],
                        'passing_score' => null,
                        'weight' => 1,
                    ]);

                    $category->questions()->where('is_active', true)->update(['is_active' => false]);

                    for ($i = 1; $i <= $categorySpec['question_count']; $i++) {
                        $this->createQuestion($exam, $category, $examSpec['type'], $order++, $i);
                    }
                }

                $report[$exam->title] = $exam->questions()->where('is_active', true)->count();
            }

            return $report;
        });
    }

    public function validateReadiness(): array
    {
        $issues = [];
        $courses = [];

        foreach ($this->finalExams() as $examSpec) {
            $exam = Exam::where('title', $examSpec['title'])->where('is_active', true)->first();
            if (!$exam) {
                $issues[] = "Course aktif tidak ditemukan: {$examSpec['title']}";
                continue;
            }

            $activeTotal = $exam->questions()->where('is_active', true)->count();
            if ($activeTotal !== 100) {
                $issues[] = "{$exam->title} memiliki {$activeTotal} soal aktif, seharusnya 100.";
            }

            foreach (AsnCatalog::examCategories() as $categorySpec) {
                $category = $exam->categories()->where('code', $categorySpec['code'])->first();
                if (!$category) {
                    $issues[] = "{$exam->title} belum memiliki kategori {$categorySpec['code']}.";
                    continue;
                }

                $count = $category->questions()->where('is_active', true)->count();
                if ($count !== $categorySpec['question_count']) {
                    $issues[] = "{$exam->title} kategori {$categorySpec['code']} berisi {$count} soal, seharusnya {$categorySpec['question_count']}.";
                }
            }

            $invalidQuestions = $exam->questions()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('option_a')->orWhereNull('option_b')->orWhereNull('option_c')
                        ->orWhereNull('option_d')->orWhereNull('option_e')->orWhereNull('explanation')
                        ->orWhereNotIn('correct_answer', ['A', 'B', 'C', 'D', 'E']);
                })
                ->count();
            if ($invalidQuestions > 0) {
                $issues[] = "{$exam->title} memiliki {$invalidQuestions} soal aktif yang belum lengkap.";
            }

            $courses[$exam->title] = $activeTotal;
        }

        if (Regulation::count() < 45) {
            $issues[] = 'Bank Regulasi belum berisi minimal 45 regulasi yang direncanakan.';
        }

        return [
            'ready' => count($issues) === 0,
            'issues' => $issues,
            'courses' => $courses,
            'regulations' => Regulation::count(),
            'pdf_downloaded' => Regulation::where('download_status', 'downloaded')->count(),
            'pdf_manual_required' => Regulation::where('download_status', 'manual_required')->count(),
            'extracted' => Regulation::whereIn('extraction_status', ['extracted', 'ocr_completed'])->count(),
        ];
    }

    public function finalExams(): array
    {
        return [
            ['title' => 'Simulasi CAT Uji Kompetensi Jabatan Administrator', 'duration' => 90, 'passing_grade' => 70.00, 'type' => 'Jabatan Administrator'],
            ['title' => 'Simulasi CAT Uji Kompetensi Jabatan Pengawas', 'duration' => 90, 'passing_grade' => 70.00, 'type' => 'Jabatan Pengawas'],
            ['title' => 'Simulasi CAT Uji Kompetensi Jabatan Fungsional Manajemen ASN', 'duration' => 90, 'passing_grade' => 70.00, 'type' => 'Jabatan Fungsional Manajemen ASN'],
            ['title' => 'Simulasi CAT Uji Kompetensi Jabatan Pimpinan Tinggi Pratama', 'duration' => 120, 'passing_grade' => 75.00, 'type' => 'Jabatan Pimpinan Tinggi Pratama'],
        ];
    }

    private function createQuestion(Exam $exam, ExamCategory $category, string $examType, int $order, int $index): void
    {
        $regulation = $this->regulationFor($category->code);
        $topic = $this->topics($category->code)[$index % count($this->topics($category->code))];
        $suffix = "{$examType} - {$category->code} - {$index}";

        if ($category->code === 'ANGKA_KREDIT_JF' && $index <= 10) {
            $last = 60 + ($index * 7) + (strlen($examType) % 5);
            $target = $last + 30;
            $earned = 10 + $index;
            $gap = $target - ($last + $earned);
            Question::create([
                'exam_id' => $exam->id,
                'exam_category_id' => $category->id,
                'regulation_id' => $regulation?->id,
                'question_text' => "Pada simulasi {$examType}, seorang pejabat fungsional memiliki Angka Kredit terakhir {$last}. Kebutuhan Angka Kredit kumulatif untuk target berikutnya adalah {$target}. Tahun ini ia memperoleh tambahan Angka Kredit {$earned}. Berapa kekurangan Angka Kredit yang masih harus dipenuhi?",
                'option_a' => (string) max(0, $gap - 4),
                'option_b' => (string) max(0, $gap - 2),
                'option_c' => (string) $gap,
                'option_d' => (string) ($gap + 3),
                'option_e' => (string) ($gap + 6),
                'correct_answer' => 'C',
                'explanation' => "Total Angka Kredit yang dimiliki = {$last} + {$earned} = ".($last + $earned).". Kekurangan = {$target} - ".($last + $earned)." = {$gap}.",
                'source_reference' => 'Peraturan BKN Nomor 3 Tahun 2023',
                'question_type' => 'Hitungan angka kredit',
                'difficulty' => 'calculation',
                'score' => 1,
                'order_number' => $order,
                'is_active' => true,
            ]);
            return;
        }

        $correct = $this->correctOption($category->code);
        Question::create([
            'exam_id' => $exam->id,
            'exam_category_id' => $category->id,
            'regulation_id' => $regulation?->id,
            'question_text' => "Dalam konteks {$topic} pada {$examType}, pilihan tindakan yang paling tepat menurut prinsip Manajemen ASN adalah ... ({$suffix})",
            'option_a' => $correct['A'],
            'option_b' => $correct['B'],
            'option_c' => $correct['C'],
            'option_d' => $correct['D'],
            'option_e' => $correct['E'],
            'correct_answer' => 'B',
            'explanation' => "Jawaban B tepat karena keputusan Manajemen ASN harus berbasis regulasi, sistem merit, kebutuhan organisasi, bukti administratif, dan akuntabilitas. Rujukan utama: ".($regulation?->regulation_number ?: 'Bank Regulasi Manajemen ASN').".",
            'source_reference' => $regulation?->regulation_number ?: 'Bank Regulasi Manajemen ASN',
            'question_type' => $index % 3 === 0 ? 'Analisis kasus' : ($category->code === 'PANGKAT_PROMOSI_MUTASI_KARIER' ? 'Prosedur administratif' : 'Pemahaman pasal'),
            'difficulty' => $index % 5 === 0 ? 'case' : ($index % 3 === 0 ? 'hard' : 'medium'),
            'score' => 1,
            'order_number' => $order,
            'is_active' => true,
        ]);
    }

    private function correctOption(string $code): array
    {
        return [
            'A' => 'Mengambil keputusan berdasarkan kebiasaan informal agar proses lebih cepat',
            'B' => $code === 'PERKAWINAN_PERCERAIAN_ASN'
                ? 'Memeriksa alasan, kelengkapan dokumen, kewenangan pejabat, dan mendokumentasikan keputusan sesuai ketentuan'
                : 'Menerapkan ketentuan regulasi secara objektif, berbasis kompetensi/kinerja, terdokumentasi, dan selaras kebutuhan organisasi',
            'C' => 'Memprioritaskan kedekatan personal sepanjang tidak menimbulkan keberatan terbuka',
            'D' => 'Menunda proses tanpa alasan administratif yang jelas',
            'E' => 'Mengabaikan dokumen pendukung karena keputusan pimpinan dianggap selalu cukup',
        ];
    }

    private function topics(string $code): array
    {
        return [
            'REGULASI_ASN' => ['sistem merit', 'nilai dasar ASN', 'kode etik dan kode perilaku', 'digitalisasi manajemen ASN'],
            'MANAJEMEN_ASN' => ['perencanaan kebutuhan', 'pengembangan kompetensi', 'promosi dan mutasi', 'manajemen kinerja'],
            'KINERJA_KOMPETENSI_ASN' => ['dialog kinerja', 'standar kompetensi jabatan', 'evaluasi kinerja', 'rencana pengembangan kompetensi'],
            'KEPEMIMPINAN_MANAJERIAL' => ['pengambilan keputusan', 'koordinasi lintas unit', 'manajemen perubahan', 'pengendalian kinerja tim'],
            'PELAYANAN_PUBLIK_ETIKA' => ['standar pelayanan', 'integritas layanan', 'transparansi', 'penanganan pengaduan'],
            'DISIPLIN_ETIKA_NETRALITAS' => ['kewajiban dan larangan PNS', 'netralitas ASN', 'kode etik', 'hukuman disiplin'],
            'PERKAWINAN_PERCERAIAN_ASN' => ['izin perceraian PNS', 'izin perkawinan PNS', 'pemeriksaan alasan', 'surat keterangan pejabat'],
            'PENSIUN_PEMBERHENTIAN_PNS' => ['batas usia pensiun', 'pemberhentian dengan hormat', 'pertimbangan teknis pensiun', 'pensiun janda/duda'],
            'PENGADAAN_ASN' => ['perencanaan kebutuhan ASN', 'seleksi administrasi', 'CAT BKN', 'penetapan NIP/NI PPPK'],
            'CUTI_ASN' => ['cuti tahunan', 'cuti sakit', 'cuti melahirkan', 'cuti alasan penting'],
            'PANGKAT_PROMOSI_MUTASI_KARIER' => ['kenaikan pangkat reguler', 'periodisasi kenaikan pangkat', 'mutasi PNS', 'manajemen talenta'],
            'ANGKA_KREDIT_JF' => ['konversi predikat kinerja', 'kenaikan jenjang JF', 'angka kredit kumulatif', 'PAK'],
        ][$code] ?? ['Manajemen ASN'];
    }

    private function regulationFor(string $code): ?Regulation
    {
        $map = [
            'REGULASI_ASN' => 'UU Nomor 20 Tahun 2023',
            'MANAJEMEN_ASN' => 'PP Nomor 11 Tahun 2017',
            'KINERJA_KOMPETENSI_ASN' => 'PermenPANRB Nomor 6 Tahun 2022',
            'KEPEMIMPINAN_MANAJERIAL' => 'PermenPANRB Nomor 38 Tahun 2017',
            'PELAYANAN_PUBLIK_ETIKA' => 'UU Nomor 25 Tahun 2009',
            'DISIPLIN_ETIKA_NETRALITAS' => 'PP Nomor 94 Tahun 2021',
            'PERKAWINAN_PERCERAIAN_ASN' => 'PP Nomor 10 Tahun 1983',
            'PENSIUN_PEMBERHENTIAN_PNS' => 'UU Nomor 11 Tahun 1969',
            'PENGADAAN_ASN' => 'PermenPANRB Nomor 6 Tahun 2024',
            'CUTI_ASN' => 'Peraturan BKN Nomor 24 Tahun 2017',
            'PANGKAT_PROMOSI_MUTASI_KARIER' => 'Peraturan BKN Nomor 2 Tahun 2025',
            'ANGKA_KREDIT_JF' => 'Peraturan BKN Nomor 3 Tahun 2023',
        ];

        return Regulation::where('regulation_number', $map[$code] ?? null)->first() ?: Regulation::first();
    }

    private function regulationSeedText(string $category, string $number, string $note): string
    {
        return Str::limit("{$number} menjadi bahan regulasi kategori {$category}. {$note} Materi soal mencakup pengertian, kewenangan pejabat, prosedur administratif, dokumen pendukung, batasan, hak dan kewajiban ASN, pengambilan keputusan, serta studi kasus penerapan aturan dalam Manajemen ASN.", 60000, '');
    }

    private function keywordsFor(string $category, string $number): array
    {
        return array_values(array_unique(array_filter([$category, $number, 'Manajemen ASN', 'sistem merit', 'kompetensi', 'kinerja'])));
    }
}

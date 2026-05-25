<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\GeneratedQuestion;
use App\Models\Question;
use App\Models\Regulation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class QuestionGeneratorService
{
    public function generate(Regulation $regulation, Exam $exam, ExamCategory $category, int $count, string $difficulty, string $questionType, int $userId, ?string $regulationText = null, ?string $keywords = null, ?int $sourcePage = null, ?int $chunkIndex = null): array
    {
        $text = trim(strip_tags($regulationText ?: $regulation->extracted_text ?: $regulation->description ?: $regulation->title));
        $text = Str::limit(preg_replace('/\s+/', ' ', $text), 12000, '');

        $items = config('ai.api_key')
            ? $this->generateWithOpenAi($text, $category->name, $category->code, $count, $difficulty, $questionType, $keywords)
            : $this->generateDummy($text, $category->name, $count, $difficulty, $questionType, $regulation, $keywords);

        $saved = [];
        foreach ($this->validateItems($items, $exam, $category) as $item) {
            $saved[] = GeneratedQuestion::create([
                'regulation_id' => $regulation->id,
                'exam_id' => $exam->id,
                'exam_category_id' => $category->id,
                'question_text' => $item['question_text'],
                'option_a' => $item['option_a'],
                'option_b' => $item['option_b'],
                'option_c' => $item['option_c'],
                'option_d' => $item['option_d'],
                'option_e' => $item['option_e'],
                'correct_answer' => $item['correct_answer'],
                'explanation' => $item['explanation'] ?? 'Pembahasan belum tersedia.',
                'source_reference' => !empty($item['source_reference']) ? $item['source_reference'] : 'Rujukan belum terdeteksi',
                'source_page' => $item['source_page'] ?? $sourcePage,
                'source_chunk_index' => $chunkIndex,
                'difficulty' => $item['difficulty'] ?? $difficulty,
                'question_type' => $item['question_type'] ?? $questionType,
                'validation_status' => $item['validation_status'],
                'validation_notes' => $item['validation_notes'],
                'ai_model' => config('ai.api_key') ? config('ai.model') : 'dummy-local',
                'ai_raw_response' => json_encode($item, JSON_UNESCAPED_UNICODE),
                'status' => 'draft',
                'created_by' => $userId,
            ]);
        }

        return $saved;
    }

    private function generateWithOpenAi(string $text, string $category, string $categoryCode, int $count, string $difficulty, string $questionType, ?string $keywords): array
    {
        $prompt = $this->specialPrompt($categoryCode, $text, $category, $count, $difficulty, $questionType, $keywords);
        if (!$prompt) {
        $prompt = <<<PROMPT
Kamu adalah penyusun soal simulasi CAT untuk Uji Kompetensi Jabatan Manajemen ASN.

Buat soal pilihan ganda berdasarkan teks regulasi yang diberikan.

Ketentuan:
1. Soal harus relevan dengan regulasi ASN dan manajemen ASN.
2. Soal harus menggunakan bahasa Indonesia formal.
3. Soal harus memiliki 5 pilihan jawaban: A, B, C, D, dan E.
4. Hanya ada satu jawaban paling tepat.
5. Hindari soal yang terlalu mudah ditebak.
6. Hindari pilihan jawaban yang terlalu panjang.
7. Jika memungkinkan, cantumkan rujukan pasal, ayat, atau bagian regulasi.
8. Buat pembahasan singkat untuk jawaban benar.
9. Jangan membuat soal di luar isi regulasi.
10. Format output harus JSON valid.

Parameter:
- Kategori soal: {$category}
- Jumlah soal: {$count}
- Tingkat kesulitan: {$difficulty}
- Tipe soal: {$questionType}

Teks regulasi:
{$text}

Kembalikan hanya JSON dengan format:
[
  {
    "question_text": "...",
    "option_a": "...",
    "option_b": "...",
    "option_c": "...",
    "option_d": "...",
    "option_e": "...",
    "correct_answer": "A",
    "explanation": "...",
    "source_reference": "...",
    "difficulty": "...",
    "question_type": "..."
  }
]
PROMPT;
        }

        $response = Http::withToken(config('ai.api_key'))
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('ai.model'),
                'temperature' => config('ai.temperature'),
                'max_tokens' => config('ai.max_tokens'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Kembalikan hanya JSON valid tanpa markdown.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('AI provider gagal merespons: '.$response->body());
        }

        $content = $response->json('choices.0.message.content', '[]');
        $content = trim(preg_replace('/^```json|```$/m', '', $content));
        $json = json_decode($content, true);

        if (!is_array($json)) {
            throw new RuntimeException('Response AI bukan JSON valid.');
        }

        return $json;
    }

    private function generateDummy(string $text, string $category, int $count, string $difficulty, string $questionType, Regulation $regulation, ?string $keywords): array
    {
        $items = [];
        $basis = Str::limit($text ?: 'regulasi ASN dan prinsip manajemen ASN', 160);
        $context = mb_strtolower($category.' '.$keywords.' '.$regulation->title);
        $isDivorce = str_contains($context, 'perceraian') || str_contains($context, 'perkawinan');
        $isCredit = str_contains($context, 'angka kredit') || str_contains($context, 'jabatan fungsional');
        for ($i = 1; $i <= $count; $i++) {
            if ($isCredit || $questionType === 'Hitungan angka kredit') {
                $last = 80 + ($i * 3);
                $target = $last + 20;
                $earned = 8 + $i;
                $gap = max(0, $target - ($last + $earned));
                $items[] = [
                    'question_text' => "Seorang pejabat fungsional memiliki Angka Kredit terakhir {$last}. Untuk memenuhi kebutuhan kumulatif {$target}, ia memperoleh tambahan Angka Kredit {$earned}. Berapa kekurangan Angka Kredit yang masih harus dipenuhi?",
                    'option_a' => (string) max(0, $gap - 4),
                    'option_b' => (string) max(0, $gap - 2),
                    'option_c' => (string) $gap,
                    'option_d' => (string) ($gap + 3),
                    'option_e' => (string) ($gap + 6),
                    'correct_answer' => 'C',
                    'explanation' => "Total Angka Kredit = {$last} + {$earned} = ".($last + $earned).". Kebutuhan = {$target}. Kekurangan = {$target} - ".($last + $earned)." = {$gap}.",
                    'source_reference' => $regulation->regulation_number ?: 'Rujukan belum terdeteksi',
                    'difficulty' => $difficulty,
                    'question_type' => 'Hitungan angka kredit',
                ];
                continue;
            }

            $items[] = $isDivorce
                ? [
                    'question_text' => "Berdasarkan {$regulation->title}, tindakan pejabat yang paling tepat saat menerima permohonan izin cerai PNS pada kasus nomor {$i} adalah ...",
                    'option_a' => 'Langsung menyetujui tanpa pemeriksaan agar proses cepat',
                    'option_b' => 'Memeriksa alasan, kelengkapan dokumen, memanggil pihak terkait, dan menilai sesuai ketentuan',
                    'option_c' => 'Menolak semua permohonan perceraian tanpa melihat alasan',
                    'option_d' => 'Menyerahkan keputusan kepada rekan kerja pemohon',
                    'option_e' => 'Menghapus data kepegawaian pemohon dari sistem',
                    'correct_answer' => 'B',
                    'explanation' => "Permohonan izin cerai perlu diperiksa secara administratif dan substantif sebelum pejabat mengambil keputusan. Ringkasan sumber: {$basis}",
                    'source_reference' => $regulation->regulation_number ?: 'Rujukan belum terdeteksi',
                    'difficulty' => $difficulty,
                    'question_type' => $questionType,
                ]
                : [
                    'question_text' => "Berdasarkan {$regulation->title}, aspek apa yang paling tepat diperhatikan dalam {$category} pada situasi manajerial ASN nomor {$i}?",
                    'option_a' => 'Mengutamakan kedekatan personal dalam penempatan pegawai',
                    'option_b' => 'Menerapkan kualifikasi, kompetensi, kinerja, integritas, dan kebutuhan organisasi',
                    'option_c' => 'Menunda keputusan sampai ada tekanan eksternal',
                    'option_d' => 'Mengabaikan dokumentasi agar proses lebih cepat',
                    'option_e' => 'Memprioritaskan arahan informal tanpa dasar aturan',
                    'correct_answer' => 'B',
                    'explanation' => "Jawaban B tepat karena pengelolaan ASN harus objektif, akuntabel, dan selaras dengan kebutuhan organisasi. Ringkasan sumber: {$basis}",
                    'source_reference' => $regulation->regulation_number ?: 'Rujukan belum terdeteksi',
                    'difficulty' => $difficulty,
                    'question_type' => $questionType,
                ];
        }

        return $items;
    }

    private function validateItems(array $items, Exam $exam, ExamCategory $category): array
    {
        $validated = [];
        foreach ($items as $item) {
            $required = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer'];
            $notes = [];
            $status = 'valid';
            foreach ($required as $field) {
                if (!isset($item[$field]) || trim((string) $item[$field]) === '') {
                    $notes[] = "{$field} kosong";
                    $status = 'invalid';
                }
            }

            if (!in_array($item['correct_answer'] ?? '', ['A', 'B', 'C', 'D', 'E'], true)) {
                $notes[] = 'Jawaban benar tidak valid';
                $status = 'invalid';
            }

            $options = array_map(fn ($field) => trim((string) ($item[$field] ?? '')), ['option_a', 'option_b', 'option_c', 'option_d', 'option_e']);
            if (count(array_unique($options)) < 5) {
                $notes[] = 'Ada pilihan jawaban identik';
                $status = $status === 'invalid' ? 'invalid' : 'warning';
            }

            $questionText = trim((string) ($item['question_text'] ?? ''));
            if (mb_strlen($questionText) < 20 || mb_strlen($questionText) > 1200) {
                $notes[] = 'Panjang soal perlu ditinjau';
                $status = $status === 'invalid' ? 'invalid' : 'warning';
            }

            if (GeneratedQuestion::where('question_text', $questionText)->exists() || Question::where('question_text', $questionText)->exists()) {
                $notes[] = 'Soal duplikat dengan draft/bank soal';
                $status = 'invalid';
            }

            if ($status === 'invalid') {
                continue;
            }

            $item['explanation'] = $item['explanation'] ?? 'Pembahasan belum tersedia.';
            $item['source_reference'] = !empty($item['source_reference']) ? $item['source_reference'] : 'Rujukan belum terdeteksi';
            $item['validation_status'] = $status;
            $item['validation_notes'] = implode('; ', $notes) ?: null;
            $validated[] = $item;
        }

        return $validated;
    }

    private function divorcePrompt(string $text, string $category, int $count, string $difficulty, string $questionType, ?string $keywords): string
    {
        return <<<PROMPT
Kamu adalah penyusun soal simulasi CAT untuk Uji Kompetensi Jabatan Manajemen ASN.

Buat soal pilihan ganda berdasarkan regulasi tentang izin perkawinan dan perceraian bagi PNS/ASN.

Fokus materi:
1. Izin perkawinan PNS.
2. Izin perceraian PNS.
3. Kewajiban memperoleh izin atau surat keterangan dari pejabat.
4. Prosedur pengajuan permintaan izin.
5. Pemeriksaan alasan perceraian.
6. Kewenangan pejabat.
7. Alasan yang dapat dipertimbangkan.
8. Konsekuensi disiplin jika PNS melanggar ketentuan.
9. Studi kasus administrasi kepegawaian.
10. Pengambilan keputusan yang tepat oleh pejabat pengelola kepegawaian.

Ketentuan soal:
- Bahasa Indonesia formal.
- Cocok untuk uji kompetensi Manajemen ASN.
- Soal tidak boleh keluar dari isi regulasi.
- Buat opsi A sampai E.
- Hanya ada satu jawaban paling tepat.
- Sertakan pembahasan singkat.
- Sertakan rujukan pasal/ayat/bagian jika dapat ditemukan.
- Untuk tipe studi kasus, buat kasus realistis di instansi pemerintah.
- Jangan membuat soal yang terlalu mudah ditebak.
- Jangan membuat pilihan jawaban yang terlalu panjang.

Parameter:
Kategori: {$category}
Jumlah soal: {$count}
Tingkat kesulitan: {$difficulty}
Tipe soal: {$questionType}
Fokus kata kunci: {$keywords}

Teks regulasi:
{$text}

Kembalikan hanya JSON valid dengan format:
[
  {
    "question_text": "...",
    "option_a": "...",
    "option_b": "...",
    "option_c": "...",
    "option_d": "...",
    "option_e": "...",
    "correct_answer": "A",
    "explanation": "...",
    "source_reference": "...",
    "difficulty": "...",
    "question_type": "..."
  }
]
PROMPT;
    }

    private function specialPrompt(string $categoryCode, string $text, string $category, int $count, string $difficulty, string $questionType, ?string $keywords): ?string
    {
        $focus = match ($categoryCode) {
            'ANGKA_KREDIT_JF' => "- Angka Kredit Jabatan Fungsional\n- Angka Kredit kumulatif\n- Konversi predikat kinerja ke Angka Kredit\n- Kebutuhan Angka Kredit untuk kenaikan jenjang\n- Kebutuhan Angka Kredit untuk kenaikan pangkat\n- Angka Kredit tambahan\n- Angka Kredit pemeliharaan\n- Kenaikan jenjang Jabatan Fungsional\n- Kenaikan pangkat Jabatan Fungsional\n- Simulasi perhitungan Angka Kredit\n- Studi kasus PAK\n- Analisis kelayakan naik jenjang atau naik pangkat",
            'PENSIUN_PEMBERHENTIAN_PNS' => "- Batas usia pensiun\n- Pensiun PNS\n- Pensiun janda/duda\n- Pemberhentian dengan hormat\n- Pemberhentian tidak dengan hormat\n- Pemberhentian sementara\n- Pengaktifan kembali\n- Pertimbangan teknis pensiun\n- Kewenangan BKN/Kanreg BKN\n- Studi kasus pemberhentian PNS",
            'PENGADAAN_ASN' => "- Perencanaan kebutuhan ASN\n- Pengadaan PNS\n- Pengadaan PPPK\n- Seleksi administrasi\n- Seleksi kompetensi\n- CAT BKN\n- SKD\n- SKB\n- Pengangkatan CPNS\n- Pengangkatan PPPK\n- Penetapan NIP/NI PPPK\n- Kewenangan panitia pengadaan\n- Studi kasus pengadaan ASN",
            'CUTI_ASN' => "- Cuti PNS\n- Cuti PPPK\n- Cuti tahunan\n- Cuti besar\n- Cuti sakit\n- Cuti melahirkan\n- Cuti karena alasan penting\n- Cuti bersama\n- Cuti di luar tanggungan negara\n- Pejabat berwenang memberikan cuti\n- Dokumen pendukung cuti\n- Perbedaan cuti PNS dan PPPK\n- Studi kasus cuti ASN",
            'PANGKAT_PROMOSI_MUTASI_KARIER' => "- Pangkat dan golongan ruang\n- Kenaikan pangkat reguler\n- Kenaikan pangkat pilihan\n- Periodisasi kenaikan pangkat\n- Promosi jabatan\n- Mutasi\n- Pola karier\n- Manajemen talenta\n- Sistem merit\n- Standar kompetensi jabatan\n- Talent pool\n- Succession planning\n- Konflik kepentingan dalam promosi\n- Studi kasus promosi/mutasi ASN",
            'PERKAWINAN_PERCERAIAN_ASN' => "- Izin perkawinan PNS\n- Izin perceraian PNS\n- Surat keterangan perceraian\n- Prosedur permohonan izin\n- Pemeriksaan oleh atasan/pejabat\n- Konsekuensi disiplin\n- Studi kasus izin cerai ASN",
            default => null,
        };

        if (!$focus) {
            return null;
        }

        $extra = $categoryCode === 'ANGKA_KREDIT_JF'
            ? "\nKetentuan khusus:\n- Jika membuat soal hitungan, angka dalam soal harus konsisten.\n- Pembahasan harus menunjukkan langkah perhitungan.\n- Pastikan hanya ada satu jawaban benar.\n- Jangan membuat rumus yang tidak ada dasar regulasinya.\n- Jika regulasi tidak memuat angka spesifik, buat soal berbasis konsep/prosedur, bukan mengarang angka.\n"
            : '';

        return <<<PROMPT
Kamu adalah penyusun soal simulasi CAT untuk Uji Kompetensi Jabatan Manajemen ASN.

Buat soal pilihan ganda berbasis regulasi untuk kategori {$category}.

Fokus materi:
{$focus}
{$extra}
Ketentuan:
- Bahasa Indonesia formal.
- Cocok untuk uji kompetensi Manajemen ASN.
- Soal tidak boleh keluar dari isi regulasi.
- Buat opsi A sampai E.
- Hanya ada satu jawaban paling tepat.
- Sertakan pembahasan singkat.
- Sertakan rujukan pasal/ayat/bagian jika dapat ditemukan.
- Jangan membuat soal yang terlalu mudah ditebak.

Parameter:
Kategori: {$category}
Jumlah soal: {$count}
Tingkat kesulitan: {$difficulty}
Tipe soal: {$questionType}
Fokus kata kunci: {$keywords}

Teks regulasi:
{$text}

Kembalikan hanya JSON valid dengan format:
[
  {
    "question_text": "...",
    "option_a": "...",
    "option_b": "...",
    "option_c": "...",
    "option_d": "...",
    "option_e": "...",
    "correct_answer": "A",
    "explanation": "...",
    "source_reference": "...",
    "difficulty": "...",
    "question_type": "{$questionType}"
  }
]
PROMPT;
    }
}

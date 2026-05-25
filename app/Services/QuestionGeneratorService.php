<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\GeneratedQuestion;
use App\Models\Regulation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class QuestionGeneratorService
{
    public function generate(Regulation $regulation, Exam $exam, ExamCategory $category, int $count, string $difficulty, string $questionType, int $userId): array
    {
        $text = trim(strip_tags($regulation->extracted_text ?: $regulation->description ?: $regulation->title));
        $text = Str::limit(preg_replace('/\s+/', ' ', $text), 12000, '');

        $items = config('ai.api_key')
            ? $this->generateWithOpenAi($text, $category->name, $count, $difficulty, $questionType)
            : $this->generateDummy($text, $category->name, $count, $difficulty, $questionType, $regulation);

        $saved = [];
        foreach ($this->validateItems($items) as $item) {
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
                'explanation' => $item['explanation'] ?? null,
                'source_reference' => $item['source_reference'] ?? null,
                'difficulty' => $item['difficulty'] ?? $difficulty,
                'question_type' => $item['question_type'] ?? $questionType,
                'status' => 'draft',
                'created_by' => $userId,
            ]);
        }

        return $saved;
    }

    private function generateWithOpenAi(string $text, string $category, int $count, string $difficulty, string $questionType): array
    {
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

    private function generateDummy(string $text, string $category, int $count, string $difficulty, string $questionType, Regulation $regulation): array
    {
        $items = [];
        $basis = Str::limit($text ?: 'regulasi ASN dan prinsip manajemen ASN', 160);
        for ($i = 1; $i <= $count; $i++) {
            $items[] = [
                'question_text' => "Berdasarkan {$regulation->title}, aspek apa yang paling tepat diperhatikan dalam {$category} pada situasi manajerial ASN nomor {$i}?",
                'option_a' => 'Mengutamakan kedekatan personal dalam penempatan pegawai',
                'option_b' => 'Menerapkan kualifikasi, kompetensi, kinerja, integritas, dan kebutuhan organisasi',
                'option_c' => 'Menunda keputusan sampai ada tekanan eksternal',
                'option_d' => 'Mengabaikan dokumentasi agar proses lebih cepat',
                'option_e' => 'Memprioritaskan arahan informal tanpa dasar aturan',
                'correct_answer' => 'B',
                'explanation' => "Jawaban B tepat karena pengelolaan ASN harus objektif, akuntabel, dan selaras dengan kebutuhan organisasi. Ringkasan sumber: {$basis}",
                'source_reference' => $regulation->regulation_number ?: 'Bank Regulasi',
                'difficulty' => $difficulty,
                'question_type' => $questionType,
            ];
        }

        return $items;
    }

    private function validateItems(array $items): array
    {
        return array_values(array_filter($items, function ($item) {
            $required = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer'];
            foreach ($required as $field) {
                if (!isset($item[$field]) || trim((string) $item[$field]) === '') {
                    return false;
                }
            }

            return in_array($item['correct_answer'], ['A', 'B', 'C', 'D', 'E'], true);
        }));
    }
}

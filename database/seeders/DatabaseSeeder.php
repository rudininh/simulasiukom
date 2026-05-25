<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
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
            'institution' => 'BKN',
        ]);

        $skd = Exam::updateOrCreate(['title' => 'Simulasi CAT SKD CPNS'], [
            'description' => 'Simulasi CAT SKD CPNS',
            'duration_minutes' => 90,
            'total_questions' => 110,
            'passing_grade' => 285,
            'available_from' => '2026-04-01',
            'available_until' => '2026-12-31',
            'is_active' => true,
        ]);

        Exam::updateOrCreate(['title' => 'Simulasi CAT Seleksi Kompetensi PPPK'], [
            'description' => 'Simulasi CAT Seleksi Kompetensi PPPK',
            'duration_minutes' => 60,
            'total_questions' => 60,
            'passing_grade' => 200,
            'available_from' => '2026-04-01',
            'available_until' => '2026-12-31',
            'is_active' => true,
        ]);

        $categories = [
            ['name' => 'Tes Wawasan Kebangsaan', 'code' => 'TWK', 'question_count' => 35],
            ['name' => 'Tes Intelegensi Umum', 'code' => 'TIU', 'question_count' => 30],
            ['name' => 'Tes Karakteristik Pribadi', 'code' => 'TKP', 'question_count' => 45],
        ];

        foreach ($categories as $category) {
            ExamCategory::updateOrCreate(['exam_id' => $skd->id, 'code' => $category['code']], $category + ['exam_id' => $skd->id]);
        }

        if ($skd->questions()->count() >= 110) {
            return;
        }

        $twk = ExamCategory::where('exam_id', $skd->id)->where('code', 'TWK')->first();
        $tiu = ExamCategory::where('exam_id', $skd->id)->where('code', 'TIU')->first();
        $tkp = ExamCategory::where('exam_id', $skd->id)->where('code', 'TKP')->first();

        $samples = [
            [1, $twk, 'Apa fungsi DPR menurut UUD 1945?', ['Mengawasi pemerintah', 'Membuat undang-undang', 'Menjalankan yudikatif', 'Mewakili rakyat', 'Bercanda gurau'], 'B', 5],
            [19, $twk, 'Apa arti lambang rantai pada Pancasila?', ['Keadilan sosial', 'Persatuan', 'Kemanusiaan', 'Persaudaraan', 'Kedamaian'], 'C', 5],
            [33, $twk, 'Organisasi PBB yang melakukan pengawasan di bidang ketenagakerjaan dan perburuhan adalah ...', ['WHO', 'UNESCO', 'JOBWORLD', 'ILO', 'UNHCR'], 'D', 5],
        ];

        foreach ($samples as [$order, $category, $text, $options, $answer, $score]) {
            Question::updateOrCreate(['exam_id' => $skd->id, 'order_number' => $order], [
                'exam_category_id' => $category->id,
                'question_text' => $text,
                'option_a' => $options[0],
                'option_b' => $options[1],
                'option_c' => $options[2],
                'option_d' => $options[3],
                'option_e' => $options[4],
                'correct_answer' => $answer,
                'score' => $score,
                'is_active' => true,
            ]);
        }

        for ($i = 1; $i <= 110; $i++) {
            if (Question::where('exam_id', $skd->id)->where('order_number', $i)->exists()) {
                continue;
            }
            $category = $i <= 35 ? $twk : ($i <= 65 ? $tiu : $tkp);
            Question::create([
                'exam_id' => $skd->id,
                'exam_category_id' => $category->id,
                'question_text' => "Contoh soal {$category->code} nomor {$i}. Pilih jawaban yang paling tepat.",
                'option_a' => "Pilihan A untuk soal {$i}",
                'option_b' => "Pilihan B untuk soal {$i}",
                'option_c' => "Pilihan C untuk soal {$i}",
                'option_d' => "Pilihan D untuk soal {$i}",
                'option_e' => "Pilihan E untuk soal {$i}",
                'correct_answer' => ['A', 'B', 'C', 'D', 'E'][$i % 5],
                'score' => $category->code === 'TKP' ? 3 : 5,
                'order_number' => $i,
                'is_active' => true,
            ]);
        }
    }
}

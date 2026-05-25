<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Support\AsnCatalog;
use Illuminate\Console\Command;

class SyncExamCategories extends Command
{
    protected $signature = 'exam:sync-categories {--all : Sinkronkan juga ujian nonaktif}';

    protected $description = 'Sinkronkan kategori final dan komposisi 100 soal untuk setiap course/simulasi.';

    public function handle(): int
    {
        $exams = Exam::query()
            ->when(!$this->option('all'), fn ($query) => $query->where('is_active', true))
            ->get();

        $legacyCodes = [
            'KEPEMIMPINAN' => 'KEPEMIMPINAN_MANAJERIAL',
            'PELAYANAN_PUBLIK' => 'PELAYANAN_PUBLIK_ETIKA',
            'STUDI_KASUS' => 'KINERJA_KOMPETENSI_ASN',
        ];

        foreach ($exams as $exam) {
            $created = 0;
            $updated = 0;
            $deactivated = 0;
            $finalCodes = array_column(AsnCatalog::examCategories(), 'code');

            foreach ($legacyCodes as $oldCode => $newCode) {
                $old = ExamCategory::where('exam_id', $exam->id)->where('code', $oldCode)->first();
                $target = collect(AsnCatalog::examCategories())->firstWhere('code', $newCode);
                if ($old && $target && !ExamCategory::where('exam_id', $exam->id)->where('code', $newCode)->exists()) {
                    $old->update([
                        'code' => $newCode,
                        'name' => $target['name'],
                        'question_count' => $target['question_count'],
                    ]);
                    $updated++;
                }
            }

            foreach (AsnCatalog::examCategories() as $category) {
                $model = ExamCategory::firstOrNew([
                    'exam_id' => $exam->id,
                    'code' => $category['code'],
                ]);

                if (!$model->exists) {
                    $created++;
                } elseif ((int) $model->question_count !== (int) $category['question_count'] || $model->name !== $category['name']) {
                    $updated++;
                }

                $model->fill([
                    'name' => $category['name'],
                    'question_count' => $category['question_count'],
                    'passing_score' => $model->passing_score,
                    'weight' => $model->weight ?: 1,
                ])->save();

                $activeQuestions = Question::where('exam_id', $exam->id)
                    ->where('exam_category_id', $model->id)
                    ->where('is_active', true)
                    ->orderBy('order_number')
                    ->orderBy('id')
                    ->get();

                if ($activeQuestions->count() > $category['question_count']) {
                    $ids = $activeQuestions->slice($category['question_count'])->pluck('id');
                    $deactivated += $ids->count();
                    Question::whereIn('id', $ids)->update(['is_active' => false]);
                }
            }

            $legacyCategoryIds = ExamCategory::where('exam_id', $exam->id)
                ->whereNotIn('code', $finalCodes)
                ->pluck('id');
            if ($legacyCategoryIds->isNotEmpty()) {
                ExamCategory::whereIn('id', $legacyCategoryIds)->update(['question_count' => 0]);
                $deactivated += Question::where('exam_id', $exam->id)
                    ->whereIn('exam_category_id', $legacyCategoryIds)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $exam->update(['total_questions' => array_sum(array_column(AsnCatalog::examCategories(), 'question_count'))]);
            $this->line("{$exam->title}: {$created} kategori dibuat, {$updated} kategori diperbarui, {$deactivated} soal lama dinonaktifkan.");
        }

        $this->info('Sinkronisasi kategori selesai.');

        return self::SUCCESS;
    }
}

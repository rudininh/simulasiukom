<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_attempt_category_scores')) {
            Schema::create('exam_attempt_category_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
                $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('score')->default(0);
                $table->unsignedInteger('total_questions')->default(0);
                $table->unsignedInteger('total_answered')->default(0);
                $table->unsignedInteger('total_correct')->default(0);
                $table->unsignedInteger('total_wrong')->default(0);
                $table->timestamps();
                $table->unique(['exam_attempt_id', 'exam_category_id'], 'attempt_category_unique');
            });
        }

        if (!Schema::hasTable('exam_attempt_category_scores')) {
            return;
        }

        $finishedAttempts = DB::table('exam_attempts')
            ->select('id', 'exam_id')
            ->whereIn('status', ['finished', 'expired'])
            ->get();

        if ($finishedAttempts->isEmpty()) {
            return;
        }

        $activeQuestionCounts = DB::table('questions')
            ->select('exam_category_id', DB::raw('count(*) as total_questions'))
            ->where('is_active', 1)
            ->groupBy('exam_category_id')
            ->pluck('total_questions', 'exam_category_id');

        $categoriesByExam = DB::table('exam_categories')
            ->select('id', 'exam_id')
            ->whereIn('exam_id', $finishedAttempts->pluck('exam_id')->unique())
            ->get()
            ->groupBy('exam_id');

        $answerStats = DB::table('exam_answers')
            ->join('questions', 'exam_answers.question_id', '=', 'questions.id')
            ->join('exam_attempts', 'exam_answers.exam_attempt_id', '=', 'exam_attempts.id')
            ->whereIn('exam_attempts.status', ['finished', 'expired'])
            ->select(
                'exam_answers.exam_attempt_id',
                'questions.exam_category_id',
                DB::raw('SUM(exam_answers.score_obtained) as score'),
                DB::raw('SUM(CASE WHEN exam_answers.selected_answer IS NOT NULL THEN 1 ELSE 0 END) as total_answered'),
                DB::raw('SUM(CASE WHEN exam_answers.is_correct = 1 THEN 1 ELSE 0 END) as total_correct'),
                DB::raw('SUM(CASE WHEN exam_answers.selected_answer IS NOT NULL AND exam_answers.is_correct = 0 THEN 1 ELSE 0 END) as total_wrong')
            )
            ->groupBy('exam_answers.exam_attempt_id', 'questions.exam_category_id')
            ->get()
            ->groupBy('exam_attempt_id')
            ->map(function ($items) {
                return $items->keyBy('exam_category_id');
            });

        foreach ($finishedAttempts as $attempt) {
            $categories = $categoriesByExam[$attempt->exam_id] ?? collect();

            foreach ($categories as $category) {
                $totalQuestions = (int) ($activeQuestionCounts[$category->id] ?? 0);

                if ($totalQuestions <= 0) {
                    continue;
                }

                $stats = $answerStats[$attempt->id][$category->id] ?? null;

                DB::table('exam_attempt_category_scores')->updateOrInsert(
                    [
                        'exam_attempt_id' => $attempt->id,
                        'exam_category_id' => $category->id,
                    ],
                    [
                        'score' => (int) ($stats->score ?? 0),
                        'total_questions' => $totalQuestions,
                        'total_answered' => (int) ($stats->total_answered ?? 0),
                        'total_correct' => (int) ($stats->total_correct ?? 0),
                        'total_wrong' => (int) ($stats->total_wrong ?? 0),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_category_scores');
    }
};

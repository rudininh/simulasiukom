<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_attempt_category_scores')) {
            return;
        }

        if (!Schema::hasTable('exam_attempts') || !Schema::hasTable('exam_categories') || !Schema::hasTable('questions') || !Schema::hasTable('exam_answers')) {
            return;
        }

        DB::statement("
            INSERT INTO exam_attempt_category_scores
                (exam_attempt_id, exam_category_id, score, total_questions, total_answered, total_correct, total_wrong, created_at, updated_at)
            SELECT
                at.id AS exam_attempt_id,
                ec.id AS exam_category_id,
                COALESCE(agg.score, 0) AS score,
                COALESCE(qc.total_questions, 0) AS total_questions,
                COALESCE(agg.total_answered, 0) AS total_answered,
                COALESCE(agg.total_correct, 0) AS total_correct,
                COALESCE(agg.total_wrong, 0) AS total_wrong,
                NOW() AS created_at,
                NOW() AS updated_at
            FROM exam_attempts at
            INNER JOIN exam_categories ec ON ec.exam_id = at.exam_id
            INNER JOIN (
                SELECT exam_category_id, COUNT(*) AS total_questions
                FROM questions
                WHERE is_active = 1
                GROUP BY exam_category_id
            ) qc ON qc.exam_category_id = ec.id
            LEFT JOIN (
                SELECT
                    ea.exam_attempt_id,
                    q.exam_category_id,
                    SUM(ea.score_obtained) AS score,
                    SUM(CASE WHEN ea.selected_answer IS NOT NULL THEN 1 ELSE 0 END) AS total_answered,
                    SUM(CASE WHEN ea.is_correct = 1 THEN 1 ELSE 0 END) AS total_correct,
                    SUM(CASE WHEN ea.selected_answer IS NOT NULL AND ea.is_correct = 0 THEN 1 ELSE 0 END) AS total_wrong
                FROM exam_answers ea
                INNER JOIN questions q ON q.id = ea.question_id
                GROUP BY ea.exam_attempt_id, q.exam_category_id
            ) agg ON agg.exam_attempt_id = at.id AND agg.exam_category_id = ec.id
            WHERE at.status IN ('finished', 'expired')
            ON DUPLICATE KEY UPDATE
                score = VALUES(score),
                total_questions = VALUES(total_questions),
                total_answered = VALUES(total_answered),
                total_correct = VALUES(total_correct),
                total_wrong = VALUES(total_wrong),
                updated_at = VALUES(updated_at)
        ");
    }

    public function down(): void
    {
        DB::table('exam_attempt_category_scores')->delete();
    }
};

<?php

namespace App\Http\Controllers;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptCategoryScore;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttemptController extends Controller
{
    public function show(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $attempt->load(['exam.questions.category', 'answers']);
        $this->expireIfNeeded($attempt);

        if ($attempt->status !== 'ongoing') {
            return redirect()->route('results.show', $attempt);
        }

        $questions = $attempt->exam->questions()->with('category')->where('is_active', true)->orderBy('order_number')->get();
        $answers = $attempt->answers->keyBy('question_id');

        return view('attempts.show', [
            'attempt' => $attempt,
            'questions' => $questions,
            'answers' => $answers,
            'remainingSeconds' => $attempt->remainingSeconds(),
        ]);
    }

    public function answer(Request $request, ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $this->expireIfNeeded($attempt);
        abort_if($attempt->status !== 'ongoing', 422);

        $data = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_answer' => ['nullable', 'in:A,B,C,D,E'],
        ]);

        $question = Question::where('exam_id', $attempt->exam_id)->findOrFail($data['question_id']);
        $isCorrect = ($data['selected_answer'] ?? null) === $question->correct_answer;
        ExamAnswer::updateOrCreate(
            ['exam_attempt_id' => $attempt->id, 'question_id' => $question->id],
            [
                'selected_answer' => $data['selected_answer'] ?? null,
                'is_correct' => $isCorrect,
                'score_obtained' => $isCorrect ? $question->score : 0,
                'answered_at' => !empty($data['selected_answer']) ? now() : null,
            ]
        );

        return response()->json([
            'answered' => $attempt->answers()->whereNotNull('selected_answer')->count(),
            'total' => $attempt->exam->total_questions,
        ]);
    }

    public function finish(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $this->finishAttempt($attempt, 'finished');

        return request()->expectsJson()
            ? response()->json(['redirect' => route('results.show', $attempt)])
            : redirect()->route('results.show', $attempt);
    }

    public function result(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        if ($attempt->status === 'ongoing') {
            $this->expireIfNeeded($attempt);
        }

        $attempt->load('exam.categories', 'user', 'categoryScores.category');
        return view('attempts.result', compact('attempt'));
    }

    public function print(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $attempt->load('exam.categories', 'user', 'categoryScores.category');

        return view('attempts.print', compact('attempt'));
    }

    public function history()
    {
        $attempts = ExamAttempt::with('exam')->where('user_id', auth()->id())->whereIn('status', ['finished', 'expired'])->latest()->get();
        return view('attempts.history', compact('attempts'));
    }

    public function reset(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $attempt->answers()->delete();
        $attempt->delete();

        return redirect()->route('exams.index')->with('success', 'Hasil ujian direset.');
    }

    private function authorizeAttempt(ExamAttempt $attempt): void
    {
        abort_unless(auth()->user()->isAdmin() || $attempt->user_id === auth()->id(), 403);
    }

    private function expireIfNeeded(ExamAttempt $attempt): void
    {
        if ($attempt->status === 'ongoing' && $attempt->remainingSeconds() <= 0) {
            $this->finishAttempt($attempt, 'expired');
        }
    }

    private function finishAttempt(ExamAttempt $attempt, string $status): void
    {
        DB::transaction(function () use ($attempt, $status) {
            $attempt->load('answers.question.category', 'exam.categories.questions');
            $categoryStats = [];
            foreach ($attempt->exam->categories as $category) {
                $categoryStats[$category->id] = [
                    'category' => $category,
                    'score' => 0,
                    'total_questions' => $category->questions()->where('is_active', true)->count(),
                    'total_answered' => 0,
                    'total_correct' => 0,
                    'total_wrong' => 0,
                ];
            }

            foreach ($attempt->answers as $answer) {
                $categoryId = $answer->question->exam_category_id;
                if (!isset($categoryStats[$categoryId])) {
                    continue;
                }
                $categoryStats[$categoryId]['score'] += $answer->score_obtained;
                $categoryStats[$categoryId]['total_answered'] += $answer->selected_answer ? 1 : 0;
                if ($answer->is_correct) {
                    $categoryStats[$categoryId]['total_correct']++;
                } elseif ($answer->selected_answer) {
                    $categoryStats[$categoryId]['total_wrong']++;
                }
            }

            $total = collect($categoryStats)->sum('score');
            $correct = collect($categoryStats)->sum('total_correct');
            $finishedAt = now();
            $answered = $attempt->answers->whereNotNull('selected_answer')->count();

            foreach ($categoryStats as $categoryId => $stats) {
                ExamAttemptCategoryScore::updateOrCreate(
                    ['exam_attempt_id' => $attempt->id, 'exam_category_id' => $categoryId],
                    [
                        'score' => $stats['score'],
                        'total_questions' => $stats['total_questions'],
                        'total_answered' => $stats['total_answered'],
                        'total_correct' => $stats['total_correct'],
                        'total_wrong' => $stats['total_wrong'],
                    ]
                );
            }

            $legacyScore = function (string $code) use ($categoryStats): int {
                $stats = collect($categoryStats)->first(fn ($item) => $item['category']->code === $code);
                return $stats ? (int) $stats['score'] : 0;
            };
            $attempt->update([
                'finished_at' => $attempt->finished_at ?: $finishedAt,
                'duration_seconds' => $attempt->started_at->diffInSeconds($attempt->finished_at ?: $finishedAt),
                'score_regulasi_asn' => $legacyScore('REGULASI_ASN'),
                'score_manajemen_asn' => $legacyScore('MANAJEMEN_ASN'),
                'score_kepemimpinan' => $legacyScore('KEPEMIMPINAN'),
                'score_pelayanan_publik' => $legacyScore('PELAYANAN_PUBLIK'),
                'score_studi_kasus' => $legacyScore('STUDI_KASUS'),
                'score_perkawinan_perceraian_asn' => $legacyScore('PERKAWINAN_PERCERAIAN_ASN'),
                'score_total' => $total,
                'total_answered' => $answered,
                'total_correct' => $correct,
                'total_wrong' => max(0, $answered - $correct),
                'competency_status' => $total >= $attempt->exam->passing_grade ? 'kompeten' : 'belum_kompeten',
                'status' => $status,
            ]);
        });
    }
}

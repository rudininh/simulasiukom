<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Regulation;
use App\Services\QuestionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegulationQuestionGeneratorController extends Controller
{
    public function create(Regulation $regulation)
    {
        return view('admin.regulations.generate_questions', [
            'regulation' => $regulation,
            'exams' => Exam::with('categories')->orderBy('title')->get(),
            'categories' => ExamCategory::with('exam')->get(),
        ]);
    }

    public function store(Request $request, Regulation $regulation, QuestionGeneratorService $service)
    {
        $data = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'exam_category_id' => ['required', 'exists:exam_categories,id'],
            'count' => ['required', 'integer', 'min:1', 'max:20'],
            'difficulty' => ['required', 'in:easy,medium,hard,case'],
            'question_type' => ['required', 'string', 'max:100'],
            'page_from' => ['nullable', 'integer', 'min:1'],
            'page_until' => ['nullable', 'integer', 'min:1'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'use_ocr_text' => ['nullable', 'boolean'],
        ]);

        if (!$regulation->extracted_text && !$regulation->pages()->whereNotNull('ocr_text')->exists()) {
            return back()->withErrors(['regulation' => 'Teks regulasi belum tersedia. Jalankan ekstraksi teks atau OCR terlebih dahulu.']);
        }

        $text = $this->resolveText($regulation, $data);
        if (mb_strlen(trim($text)) < 300) {
            return back()->withErrors(['regulation' => 'Teks regulasi terlalu pendek untuk generate soal. Periksa hasil OCR atau upload file lain.']);
        }

        $chunks = str_split($text, 9000);
        $exam = Exam::findOrFail($data['exam_id']);
        $category = ExamCategory::findOrFail($data['exam_category_id']);
        $remaining = (int) $data['count'];
        $saved = [];

        foreach ($chunks as $index => $chunk) {
            if ($remaining <= 0) {
                break;
            }
            $take = min($remaining, 10);
            $saved = array_merge($saved, $service->generate(
                $regulation,
                $exam,
                $category,
                $take,
                $data['difficulty'],
                $data['question_type'],
                auth()->id(),
                $chunk,
                $data['keywords'] ?? null,
                $data['page_from'] ?? null,
                $index + 1
            ));
            $remaining -= $take;
        }

        return redirect()->route('admin.generated-questions.index')->with('success', count($saved).' draft soal berhasil dibuat dari PDF regulasi.');
    }

    private function resolveText(Regulation $regulation, array $data): string
    {
        if (!empty($data['page_from']) || !empty($data['page_until'])) {
            $from = $data['page_from'] ?? 1;
            $until = $data['page_until'] ?? $from;
            return $regulation->pages()
                ->whereBetween('page_number', [$from, $until])
                ->get()
                ->map(fn ($page) => !empty($data['use_ocr_text']) ? ($page->ocr_text ?: $page->text) : ($page->text ?: $page->ocr_text))
                ->implode("\n\n");
        }

        return $regulation->extracted_text ?: $regulation->pages()->get()->map(fn ($page) => $page->ocr_text ?: $page->text)->implode("\n\n");
    }
}

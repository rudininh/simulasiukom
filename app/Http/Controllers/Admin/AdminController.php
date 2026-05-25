<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamCategory;
use App\Models\GeneratedQuestion;
use App\Models\Question;
use App\Models\Regulation;
use App\Models\User;
use App\Services\QuestionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'users' => User::where('role', 'peserta')->count(),
            'exams' => Exam::where('is_active', true)->count(),
            'questions' => Question::count(),
            'regulations' => Regulation::count(),
            'drafts' => GeneratedQuestion::where('status', 'draft')->count(),
            'attempts' => ExamAttempt::whereIn('status', ['finished', 'expired'])->count(),
            'kompeten' => ExamAttempt::where('competency_status', 'kompeten')->count(),
            'belumKompeten' => ExamAttempt::where('competency_status', 'belum_kompeten')->count(),
            'questionsByCategory' => Question::query()
                ->select('exam_categories.name', DB::raw('count(*) as total'))
                ->join('exam_categories', 'questions.exam_category_id', '=', 'exam_categories.id')
                ->groupBy('exam_categories.name')
                ->orderBy('exam_categories.name')
                ->get(),
            'regulationsByCategory' => Regulation::query()
                ->select('category', DB::raw('count(*) as total'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('category')
                ->get(),
            'draftsByCategory' => GeneratedQuestion::query()
                ->select('exam_categories.name', DB::raw('count(*) as total'))
                ->join('exam_categories', 'generated_questions.exam_category_id', '=', 'exam_categories.id')
                ->where('generated_questions.status', 'draft')
                ->groupBy('exam_categories.name')
                ->orderBy('exam_categories.name')
                ->get(),
            'readyRegulations' => Regulation::whereNotNull('extracted_text')->where('extracted_text', '<>', '')->count(),
        ]);
    }

    public function users()
    {
        return view('admin.users.index', ['users' => User::latest()->paginate(15)]);
    }

    public function exams()
    {
        return view('admin.exams.index', ['exams' => Exam::latest()->paginate(15)]);
    }

    public function storeExam(Request $request)
    {
        Exam::create($this->examData($request));
        return back()->with('success', 'Ujian disimpan.');
    }

    public function updateExam(Request $request, Exam $exam)
    {
        $exam->update($this->examData($request));
        return back()->with('success', 'Ujian diperbarui.');
    }

    public function destroyExam(Exam $exam)
    {
        $exam->delete();
        return back()->with('success', 'Ujian dihapus.');
    }

    public function categories()
    {
        return view('admin.categories.index', [
            'categories' => ExamCategory::with('exam')->latest()->paginate(15),
            'exams' => Exam::orderBy('title')->get(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        ExamCategory::create($this->categoryData($request));
        return back()->with('success', 'Kategori disimpan.');
    }

    public function updateCategory(Request $request, ExamCategory $category)
    {
        $category->update($this->categoryData($request));
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroyCategory(ExamCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    public function questions()
    {
        return view('admin.questions.index', [
            'questions' => Question::with('exam', 'category')->orderByDesc('id')->paginate(15),
            'exams' => Exam::with('categories')->orderBy('title')->get(),
            'categories' => ExamCategory::with('exam')->get(),
            'regulations' => Regulation::where('status', 'active')->orderBy('title')->get(),
        ]);
    }

    public function storeQuestion(Request $request)
    {
        Question::create($this->questionData($request));
        return back()->with('success', 'Soal disimpan.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $question->update($this->questionData($request));
        return back()->with('success', 'Soal diperbarui.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Soal dihapus.');
    }

    public function importQuestions(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($data['file']->getRealPath(), 'r');
        $count = 0;
        $header = null;
        while (($row = fgetcsv($handle)) !== false) {
            if (!$header) {
                $header = array_map(fn ($value) => strtolower(trim($value)), $row);
                continue;
            }
            $record = array_combine($header, array_pad($row, count($header), null));
            if (!$record || empty($record['exam_title']) || empty($record['category_code'])) {
                continue;
            }
            $exam = Exam::where('title', $record['exam_title'])->first();
            $category = $exam ? ExamCategory::where('exam_id', $exam->id)->where('code', $record['category_code'])->first() : null;
            if (!$exam || !$category) {
                continue;
            }
            Question::create([
                'exam_id' => $exam->id,
                'exam_category_id' => $category->id,
                'question_text' => $record['question_text'],
                'option_a' => $record['option_a'],
                'option_b' => $record['option_b'],
                'option_c' => $record['option_c'],
                'option_d' => $record['option_d'],
                'option_e' => $record['option_e'],
                'correct_answer' => strtoupper($record['correct_answer']),
                'explanation' => $record['explanation'] ?? null,
                'source_reference' => $record['source_reference'] ?? null,
                'score' => (int) ($record['score'] ?? 1),
                'difficulty' => $record['difficulty'] ?? 'medium',
                'order_number' => (Question::where('exam_id', $exam->id)->max('order_number') ?? 0) + 1,
                'is_active' => true,
            ]);
            $count++;
        }
        fclose($handle);

        return back()->with('success', "$count soal berhasil diimport.");
    }

    public function regulations()
    {
        return view('admin.regulations.index', [
            'regulations' => Regulation::with('uploader')->latest()->paginate(15),
        ]);
    }

    public function storeRegulation(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'regulation_number' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('regulations', 'public');
        }
        $data['uploaded_by'] = auth()->id();
        $regulation = Regulation::create($data);
        $this->extractToRegulation($regulation);

        return back()->with('success', 'Regulasi berhasil diupload.');
    }

    public function updateRegulation(Request $request, Regulation $regulation)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'regulation_number' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('regulations', 'public');
        }
        $regulation->update($data);

        return back()->with('success', 'Regulasi diperbarui.');
    }

    public function destroyRegulation(Regulation $regulation)
    {
        $regulation->delete();
        return back()->with('success', 'Regulasi dihapus.');
    }

    public function extractRegulation(Regulation $regulation)
    {
        return view('admin.regulations.extract', compact('regulation'));
    }

    public function runExtractRegulation(Regulation $regulation)
    {
        $this->extractToRegulation($regulation);
        return back()->with('success', 'Teks regulasi berhasil diekstrak.');
    }

    public function questionGenerator()
    {
        return view('admin.regulations.generator', [
            'regulations' => Regulation::where('status', 'active')->latest()->get(),
            'exams' => Exam::with('categories')->orderBy('title')->get(),
            'categories' => ExamCategory::with('exam')->get(),
        ]);
    }

    public function generateQuestions(Request $request, QuestionGeneratorService $service)
    {
        $data = $request->validate([
            'regulation_id' => ['required', 'exists:regulations,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'exam_category_id' => ['required', 'exists:exam_categories,id'],
            'count' => ['required', 'integer', 'min:1', 'max:20'],
            'difficulty' => ['required', 'in:easy,medium,hard,case,calculation'],
            'question_type' => ['required', 'string', 'max:100'],
        ]);

        $saved = $service->generate(
            Regulation::findOrFail($data['regulation_id']),
            Exam::findOrFail($data['exam_id']),
            ExamCategory::findOrFail($data['exam_category_id']),
            (int) $data['count'],
            $data['difficulty'],
            $data['question_type'],
            auth()->id()
        );

        return redirect()->route('admin.generated-questions.index')->with('success', count($saved).' draft soal berhasil dibuat.');
    }

    public function generatedQuestions(Request $request)
    {
        $query = GeneratedQuestion::with('regulation', 'exam', 'category')->latest();
        foreach (['regulation_id', 'exam_id', 'exam_category_id', 'difficulty', 'question_type', 'status', 'validation_status'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }
        if ($request->filled('keyword')) {
            $query->where('question_text', 'like', '%'.$request->keyword.'%');
        }

        return view('admin.generated_questions.index', [
            'drafts' => $query->paginate(15)->withQueryString(),
            'regulations' => Regulation::orderBy('title')->get(),
            'exams' => Exam::orderBy('title')->get(),
            'categories' => ExamCategory::with('exam')->get(),
        ]);
    }

    public function editGeneratedQuestion(GeneratedQuestion $generatedQuestion)
    {
        return view('admin.generated_questions.edit', compact('generatedQuestion'));
    }

    public function updateGeneratedQuestion(Request $request, GeneratedQuestion $generatedQuestion)
    {
        $generatedQuestion->update($request->validate([
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'option_e' => ['required', 'string'],
            'correct_answer' => ['required', 'in:A,B,C,D,E'],
            'explanation' => ['nullable', 'string'],
            'source_reference' => ['nullable', 'string', 'max:255'],
            'source_page' => ['nullable', 'integer', 'min:1'],
            'difficulty' => ['required', 'string', 'max:50'],
            'question_type' => ['required', 'string', 'max:100'],
        ]));

        return redirect()->route('admin.generated-questions.index')->with('success', 'Draft soal diperbarui.');
    }

    public function approveGeneratedQuestion(GeneratedQuestion $generatedQuestion)
    {
        Question::create([
            'exam_id' => $generatedQuestion->exam_id,
            'exam_category_id' => $generatedQuestion->exam_category_id,
            'regulation_id' => $generatedQuestion->regulation_id,
            'question_text' => $generatedQuestion->question_text,
            'option_a' => $generatedQuestion->option_a,
            'option_b' => $generatedQuestion->option_b,
            'option_c' => $generatedQuestion->option_c,
            'option_d' => $generatedQuestion->option_d,
            'option_e' => $generatedQuestion->option_e,
            'correct_answer' => $generatedQuestion->correct_answer,
            'explanation' => $generatedQuestion->explanation,
            'source_reference' => $generatedQuestion->source_reference,
            'source_page' => $generatedQuestion->source_page,
            'question_type' => $generatedQuestion->question_type,
            'difficulty' => $generatedQuestion->difficulty,
            'score' => 1,
            'order_number' => (Question::where('exam_id', $generatedQuestion->exam_id)->max('order_number') ?? 0) + 1,
            'is_active' => true,
        ]);
        $generatedQuestion->update(['status' => 'approved']);

        return back()->with('success', 'Draft disetujui dan masuk bank soal.');
    }

    public function rejectGeneratedQuestion(GeneratedQuestion $generatedQuestion)
    {
        $generatedQuestion->update(['status' => 'rejected']);
        return back()->with('success', 'Draft soal ditolak.');
    }

    public function bulkGeneratedQuestions(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:generated_questions,id'],
            'action' => ['required', 'in:approve,reject,delete,assign'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'exam_category_id' => ['nullable', 'exists:exam_categories,id'],
        ]);

        $drafts = GeneratedQuestion::whereIn('id', $data['ids'])->get();
        foreach ($drafts as $draft) {
            if ($data['action'] === 'approve' && $draft->status === 'draft') {
                $this->approveGeneratedQuestion($draft);
            } elseif ($data['action'] === 'reject') {
                $draft->update(['status' => 'rejected']);
            } elseif ($data['action'] === 'delete') {
                $draft->delete();
            } elseif ($data['action'] === 'assign') {
                $draft->update(array_filter([
                    'exam_id' => $data['exam_id'] ?? null,
                    'exam_category_id' => $data['exam_category_id'] ?? null,
                ]));
            }
        }

        return back()->with('success', 'Aksi bulk draft soal selesai diproses.');
    }

    public function bulkApproveGeneratedQuestions(Request $request)
    {
        $request->merge(['action' => 'approve']);
        return $this->bulkGeneratedQuestions($request);
    }

    public function bulkRejectGeneratedQuestions(Request $request)
    {
        $request->merge(['action' => 'reject']);
        return $this->bulkGeneratedQuestions($request);
    }

    public function bulkDeleteGeneratedQuestions(Request $request)
    {
        $request->merge(['action' => 'delete']);
        return $this->bulkGeneratedQuestions($request);
    }

    public function attempts()
    {
        return view('admin.attempts.index', [
            'attempts' => ExamAttempt::with('user', 'exam')->latest()->paginate(20),
        ]);
    }

    public function attemptDetail(ExamAttempt $attempt)
    {
        $attempt->load('user', 'exam', 'answers.question.category', 'categoryScores.category');
        return view('admin.results.show', compact('attempt'));
    }

    public function reports()
    {
        return view('admin.reports.index', [
            'attempts' => ExamAttempt::with('user', 'exam')->whereIn('status', ['finished', 'expired'])->latest()->get(),
        ]);
    }

    public function resetAttempt(ExamAttempt $attempt)
    {
        $attempt->answers()->delete();
        $attempt->delete();
        return back()->with('success', 'Hasil peserta direset.');
    }

    private function examData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'exam_type' => ['nullable', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'passing_grade' => ['required', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
            'regulation_basis' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function categoryData(Request $request): array
    {
        return $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'question_count' => ['required', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function questionData(Request $request): array
    {
        $data = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'exam_category_id' => ['required', 'exists:exam_categories,id'],
            'regulation_id' => ['nullable', 'exists:regulations,id'],
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'option_e' => ['required', 'string'],
            'correct_answer' => ['required', 'in:A,B,C,D,E'],
            'explanation' => ['nullable', 'string'],
            'source_reference' => ['nullable', 'string', 'max:255'],
            'question_type' => ['nullable', 'string', 'max:100'],
            'source_page' => ['nullable', 'integer', 'min:1'],
            'score' => ['required', 'integer', 'min:0'],
            'difficulty' => ['required', 'in:easy,medium,hard,case,calculation'],
            'order_number' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function extractToRegulation(Regulation $regulation): void
    {
        if (!$regulation->file_path || !Storage::disk('public')->exists($regulation->file_path)) {
            $regulation->update(['extracted_text' => $this->cleanExtractedText($regulation->description)]);
            return;
        }

        $absolute = Storage::disk('public')->path($regulation->file_path);
        $extension = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));
        $content = '';

        if ($extension === 'txt') {
            $content = file_get_contents($absolute);
        } elseif ($extension === 'docx') {
            $zip = new \ZipArchive();
            if ($zip->open($absolute) === true) {
                $xml = $zip->getFromName('word/document.xml') ?: '';
                $zip->close();
                $content = strip_tags(str_replace(['</w:p>', '</w:tr>'], "\n", $xml));
            }
        } elseif ($extension === 'pdf') {
            $content = preg_replace('/[^\P{C}\n\r\t]+/u', ' ', file_get_contents($absolute));
        }

        $regulation->update(['extracted_text' => $this->cleanExtractedText($content ?: $regulation->description)]);
    }

    public function syncExamCategories()
    {
        \Illuminate\Support\Facades\Artisan::call('exam:sync-categories');
        return back()->with('success', trim(\Illuminate\Support\Facades\Artisan::output()) ?: 'Kategori course berhasil disinkronkan.');
    }

    private function cleanExtractedText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($text))), 60000, '');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'users' => User::where('role', 'peserta')->count(),
            'exams' => Exam::count(),
            'questions' => Question::count(),
            'attempts' => ExamAttempt::whereIn('status', ['finished', 'expired'])->count(),
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
            'exam_id' => ['required', 'exists:exams,id'],
            'exam_category_id' => ['required', 'exists:exam_categories,id'],
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($data['file']->getRealPath(), 'r');
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 8 || strtolower($row[0]) === 'pertanyaan') {
                continue;
            }
            Question::create([
                'exam_id' => $data['exam_id'],
                'exam_category_id' => $data['exam_category_id'],
                'question_text' => $row[0],
                'option_a' => $row[1],
                'option_b' => $row[2],
                'option_c' => $row[3],
                'option_d' => $row[4],
                'option_e' => $row[5],
                'correct_answer' => strtoupper($row[6]),
                'score' => (int) $row[7],
                'order_number' => (int) ($row[8] ?? Question::where('exam_id', $data['exam_id'])->max('order_number') + 1),
                'is_active' => true,
            ]);
            $count++;
        }
        fclose($handle);

        return back()->with('success', "$count soal berhasil diimport.");
    }

    public function attempts()
    {
        return view('admin.attempts.index', [
            'attempts' => ExamAttempt::with('user', 'exam')->latest()->paginate(20),
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
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'passing_grade' => ['required', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
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
            'code' => ['required', 'string', 'max:10'],
            'question_count' => ['required', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function questionData(Request $request): array
    {
        $data = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'exam_category_id' => ['required', 'exists:exam_categories,id'],
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'option_e' => ['required', 'string'],
            'correct_answer' => ['required', 'in:A,B,C,D,E'],
            'score' => ['required', 'integer', 'min:0'],
            'order_number' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}

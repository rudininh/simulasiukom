<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)->latest()->get();
        return view('exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $exam->load('categories');
        $finished = $exam->attempts()->where('user_id', auth()->id())->whereIn('status', ['finished', 'expired'])->latest()->first();
        return view('exams.show', compact('exam', 'finished'));
    }

    public function start(Exam $exam)
    {
        abort_unless($exam->is_active, 404);
        $finished = ExamAttempt::where('user_id', auth()->id())->where('exam_id', $exam->id)->whereIn('status', ['finished', 'expired'])->exists();
        if ($finished) {
            return back()->withErrors(['exam' => 'Ujian ini sudah selesai dan tidak dapat dimulai ulang.']);
        }

        $attempt = ExamAttempt::firstOrCreate(
            ['user_id' => auth()->id(), 'exam_id' => $exam->id, 'status' => 'ongoing'],
            ['started_at' => now()]
        );

        return redirect()->route('attempts.show', $attempt);
    }
}

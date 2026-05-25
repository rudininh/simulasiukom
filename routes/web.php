<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegulationPublicController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:peserta'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/simulasi', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/simulasi/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('/simulasi/{exam}/mulai', [ExamController::class, 'start'])->name('exams.start');
    Route::get('/ujian/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
    Route::post('/ujian/{attempt}/jawab', [AttemptController::class, 'answer'])->name('attempts.answer');
    Route::post('/ujian/{attempt}/selesai', [AttemptController::class, 'finish'])->name('attempts.finish');
    Route::get('/hasil/{attempt}', [AttemptController::class, 'result'])->name('results.show');
    Route::get('/riwayat', [AttemptController::class, 'history'])->name('history');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/regulasi', [RegulationPublicController::class, 'index'])->name('regulations.public');
    Route::get('/hasil/{attempt}/cetak', [AttemptController::class, 'print'])->name('results.print');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/peserta', [AdminController::class, 'users'])->name('users');
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams.index');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::put('/exams/{exam}', [AdminController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{exam}', [AdminController::class, 'destroyExam'])->name('exams.destroy');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/questions', [AdminController::class, 'questions'])->name('questions.index');
    Route::post('/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');
    Route::post('/questions/import', [AdminController::class, 'importQuestions'])->name('questions.import');
    Route::put('/questions/{question}', [AdminController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{question}', [AdminController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::get('/regulations', [AdminController::class, 'regulations'])->name('regulations.index');
    Route::post('/regulations', [AdminController::class, 'storeRegulation'])->name('regulations.store');
    Route::put('/regulations/{regulation}', [AdminController::class, 'updateRegulation'])->name('regulations.update');
    Route::delete('/regulations/{regulation}', [AdminController::class, 'destroyRegulation'])->name('regulations.destroy');
    Route::get('/regulations/{regulation}/extract', [AdminController::class, 'extractRegulation'])->name('regulations.extract');
    Route::post('/regulations/{regulation}/extract', [AdminController::class, 'runExtractRegulation'])->name('regulations.extract.run');
    Route::get('/question-generator', [AdminController::class, 'questionGenerator'])->name('question-generator.index');
    Route::post('/question-generator/generate', [AdminController::class, 'generateQuestions'])->name('question-generator.generate');
    Route::get('/generated-questions', [AdminController::class, 'generatedQuestions'])->name('generated-questions.index');
    Route::get('/generated-questions/{generatedQuestion}/edit', [AdminController::class, 'editGeneratedQuestion'])->name('generated-questions.edit');
    Route::put('/generated-questions/{generatedQuestion}', [AdminController::class, 'updateGeneratedQuestion'])->name('generated-questions.update');
    Route::post('/generated-questions/{generatedQuestion}/approve', [AdminController::class, 'approveGeneratedQuestion'])->name('generated-questions.approve');
    Route::post('/generated-questions/{generatedQuestion}/reject', [AdminController::class, 'rejectGeneratedQuestion'])->name('generated-questions.reject');
    Route::get('/results', [AdminController::class, 'attempts'])->name('results.index');
    Route::get('/results/{attempt}', [AdminController::class, 'attemptDetail'])->name('results.show');
    Route::post('/results/{attempt}/reset', [AdminController::class, 'resetAttempt'])->name('results.reset');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::get('/settings', fn () => view('admin.settings.index'))->name('settings.index');

    Route::redirect('/ujian', '/admin/exams')->name('exams.legacy');
    Route::redirect('/kategori', '/admin/categories')->name('categories.legacy');
    Route::redirect('/soal', '/admin/questions')->name('questions.legacy');
    Route::redirect('/hasil', '/admin/results')->name('attempts.legacy');
});

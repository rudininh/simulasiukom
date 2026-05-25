<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
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
    Route::post('/hasil/{attempt}/reset', [AttemptController::class, 'reset'])->name('results.reset');
    Route::get('/riwayat', [AttemptController::class, 'history'])->name('history');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/peserta', [AdminController::class, 'users'])->name('users');
    Route::get('/ujian', [AdminController::class, 'exams'])->name('exams');
    Route::post('/ujian', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::put('/ujian/{exam}', [AdminController::class, 'updateExam'])->name('exams.update');
    Route::delete('/ujian/{exam}', [AdminController::class, 'destroyExam'])->name('exams.destroy');
    Route::get('/kategori', [AdminController::class, 'categories'])->name('categories');
    Route::post('/kategori', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/kategori/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/kategori/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/soal', [AdminController::class, 'questions'])->name('questions');
    Route::post('/soal', [AdminController::class, 'storeQuestion'])->name('questions.store');
    Route::post('/soal/import', [AdminController::class, 'importQuestions'])->name('questions.import');
    Route::put('/soal/{question}', [AdminController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/soal/{question}', [AdminController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::get('/hasil', [AdminController::class, 'attempts'])->name('attempts');
    Route::delete('/hasil/{attempt}', [AdminController::class, 'resetAttempt'])->name('attempts.reset');
});

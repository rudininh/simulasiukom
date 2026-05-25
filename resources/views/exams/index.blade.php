@extends('layouts.app')
@section('content')
<div class="mb-5">
    <h1 class="page-title"><i class="fa-solid fa-laptop me-3"></i>Daftar Simulasi</h1>
    <p class="page-subtitle">Silakan pilih menu Simulasi Ujian yang tersedia.</p>
</div>
<div class="row g-4">
    @foreach ($exams as $exam)
        <div class="col-lg-6">
            <div class="cat-card p-4 p-lg-5 h-100">
                <div class="icon-box"><i class="fa-solid fa-file-lines"></i></div>
                <h3 class="h4 fw-bold text-primary-emphasis">{{ $exam->title }}</h3>
                <p class="mb-4">Detail Simulasi :</p>
                <div class="d-grid gap-3 small mb-4">
                    <div><i class="fa-solid fa-clock text-warning me-3"></i> Durasi: {{ $exam->duration_minutes }} menit</div>
                    <div><i class="fa-solid fa-calendar text-info me-3"></i> Tanggal Tersedia: {{ optional($exam->available_from)->format('d/m/Y') }} - {{ optional($exam->available_until)->format('d/m/Y') }}</div>
                    <div><i class="fa-solid fa-circle-question text-success me-3"></i> Total Soal: {{ $exam->total_questions }}</div>
                    <div><i class="fa-solid fa-trophy text-warning me-3"></i> Passing Grade: {{ number_format($exam->passing_grade, 2) }}</div>
                </div>
                <a class="btn btn-navy px-4 py-3" href="{{ route('exams.show', $exam) }}"><i class="fa-solid fa-circle-info me-2"></i>Lihat Detail & Mulai Ujian</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

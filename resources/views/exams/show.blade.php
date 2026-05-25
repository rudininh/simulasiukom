@extends('layouts.app')
@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="cat-card overflow-hidden">
            <div class="bg-primary text-white p-3 h4 fw-bold mb-0"><i class="fa-solid fa-circle-info me-2"></i>{{ $exam->title }}</div>
            <div class="p-4">
                <p><strong>Deskripsi Ujian:</strong> {{ $exam->description }}</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2">Informasi Ujian</h6>
                        <div class="d-flex justify-content-between border-bottom py-3"><span><i class="fa-solid fa-clock text-warning me-2"></i>Durasi</span><strong>{{ $exam->duration_minutes }} menit</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-3"><span><i class="fa-solid fa-list-ol text-success me-2"></i>Total Soal</span><strong>{{ $exam->total_questions }} soal</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-3"><span><i class="fa-solid fa-trophy text-info me-2"></i>Passing Grade</span><strong>{{ number_format($exam->passing_grade, 2) }}</strong></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2">Materi Ujian</h6>
                        @foreach ($exam->categories as $category)
                            <div class="d-flex justify-content-between py-2"><span><i class="fa-solid fa-book text-primary me-2"></i>{{ $category->name }} ({{ $category->code }})</span><span class="badge bg-secondary rounded-pill">{{ $category->question_count }} soal</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="cat-card overflow-hidden mb-3">
            <div class="bg-warning p-3 h5 fw-bold mb-0"><i class="fa-solid fa-triangle-exclamation me-2"></i>Perhatian Penting</div>
            <div class="p-3 d-grid gap-3">
                <div><i class="fa-solid fa-check text-success me-2"></i>Pastikan koneksi internet stabil</div>
                <div><i class="fa-solid fa-check text-success me-2"></i>Siapkan alat tulis untuk coret-coretan</div>
                <div><i class="fa-solid fa-check text-success me-2"></i>Jawaban akan tersimpan otomatis</div>
                <div><i class="fa-solid fa-xmark text-danger me-2"></i>Jangan refresh atau tutup browser</div>
                <div><i class="fa-solid fa-xmark text-danger me-2"></i>Ujian hanya dapat dikerjakan 1 kali</div>
                <div><i class="fa-solid fa-clock text-warning me-2"></i>Waktu akan berjalan otomatis setelah mulai</div>
            </div>
        </div>
        <div class="cat-card p-4 text-center">
            @if ($finished)
                <a class="btn btn-primary btn-lg" href="{{ route('results.show', $finished) }}">Lihat Hasil Ujian</a>
            @else
                <form method="post" action="{{ route('exams.start', $exam) }}">@csrf
                    <button class="btn btn-success btn-lg"><i class="fa-solid fa-play me-2"></i>Mulai Ujian Sekarang</button>
                </form>
            @endif
            <a class="btn btn-secondary mt-2" href="{{ route('exams.index') }}"><i class="fa-solid fa-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
</div>
@endsection

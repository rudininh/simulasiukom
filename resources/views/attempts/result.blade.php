@extends('layouts.app')
@section('content')
<div class="mb-4">
    <h1 class="page-title"><i class="fa-solid fa-chart-line text-success me-3"></i>Hasil Simulasi Uji Kompetensi</h1>
    <p class="page-subtitle">{{ $attempt->exam->title }}</p>
</div>
<div class="row justify-content-center align-items-start g-4">
    <div class="col-lg-6">
        <div class="score-card text-center">
            <span class="badge {{ $attempt->competency_status === 'kompeten' ? 'bg-success' : 'bg-danger' }} mb-3">{{ $attempt->competency_status === 'kompeten' ? 'KOMPETEN' : 'BELUM KOMPETEN' }}</span>
            <div class="score-total">{{ $attempt->score_total }}</div>
            <div class="fw-bold mb-4">Skor Anda</div>
            <div class="row g-3">
                @foreach($attempt->categoryScores as $score)
                    <div class="col"><div class="score-part">{{ $score->score }}</div><small>{{ $score->category->name }}</small></div>
                @endforeach
            </div>
        </div>
        <div class="text-center mt-3">
            <a class="btn btn-primary" href="{{ route('exams.index') }}"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Simulasi</a>
            <a class="btn btn-navy" href="{{ route('history') }}">Lihat Riwayat</a>
            <a class="btn btn-secondary" href="{{ route('results.print', $attempt) }}" target="_blank">Cetak Hasil</a>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="cat-card">
            <div class="border-bottom p-3 fw-bold"><i class="fa-solid fa-circle-info me-1"></i>Detail Ujian</div>
            <div class="p-3 small">
                <div class="d-flex justify-content-between py-2"><span>Nama Ujian:</span><strong>{{ $attempt->exam->title }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Nama Peserta:</span><strong>{{ $attempt->user->name }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Waktu Mulai:</span><strong>{{ $attempt->started_at->format('d/m/Y H:i') }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Waktu Selesai:</span><strong>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Durasi Pengerjaan:</span><strong>{{ gmdate('H:i:s', $attempt->duration_seconds ?? 0) }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Total Soal:</span><strong>{{ $attempt->exam->total_questions }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Jumlah Dijawab:</span><strong>{{ $attempt->total_answered }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Jumlah Benar:</span><strong>{{ $attempt->total_correct }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Jumlah Salah:</span><strong>{{ $attempt->total_wrong }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Nilai Akhir:</span><strong>{{ $attempt->score_total }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Passing Grade:</span><strong>{{ number_format($attempt->exam->passing_grade, 2) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection

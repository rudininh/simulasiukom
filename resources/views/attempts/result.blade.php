@extends('layouts.app')
@section('content')
<div class="mb-4">
    <h1 class="page-title"><i class="fa-solid fa-chart-line text-success me-3"></i>Hasil Simulasi</h1>
    <p class="page-subtitle">{{ $attempt->exam->title }}</p>
</div>
<div class="row justify-content-center align-items-start g-4">
    <div class="col-lg-6">
        <div class="score-card text-center">
            <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }} mb-3">{{ $attempt->is_passed ? 'LULUS' : 'TIDAK LULUS' }}</span>
            <div class="score-total">{{ $attempt->score_total }}</div>
            <div class="fw-bold mb-4">Skor Anda</div>
            <div class="row">
                <div class="col"><div class="score-part">{{ $attempt->score_twk }}</div><small>Tes Wawasan Kebangsaan (TWK)</small></div>
                <div class="col"><div class="score-part">{{ $attempt->score_tiu }}</div><small>Tes Intelegensi Umum (TIU)</small></div>
                <div class="col"><div class="score-part">{{ $attempt->score_tkp }}</div><small>Tes Karakteristik Pribadi (TKP)</small></div>
            </div>
        </div>
        <div class="text-center mt-3">
            <a class="btn btn-primary" href="{{ route('exams.index') }}"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Ujian</a>
            <form class="d-inline" method="post" action="{{ route('results.reset', $attempt) }}">@csrf
                <button class="btn btn-warning"><i class="fa-solid fa-arrows-rotate me-1"></i>Reset Hasil Ujian</button>
            </form>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="cat-card">
            <div class="border-bottom p-3 fw-bold"><i class="fa-solid fa-circle-info me-1"></i>Detail Ujian</div>
            <div class="p-3 small">
                <div class="d-flex justify-content-between py-2"><span>Nama Ujian:</span><strong>{{ $attempt->exam->title }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Waktu Mulai:</span><strong>{{ $attempt->started_at->format('d/m/Y H:i') }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Waktu Selesai:</span><strong>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Durasi:</span><strong>{{ gmdate('H:i:s', $attempt->duration_seconds ?? 0) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection

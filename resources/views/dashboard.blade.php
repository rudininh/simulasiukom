@extends('layouts.app')
@section('content')
<div class="mb-5">
    <h1 class="page-title"><i class="fa-solid fa-gauge-high me-3"></i>Dashboard</h1>
    <p class="page-subtitle">Selamat datang di Aplikasi Simulasi CAT BKN. Silakan pilih menu yang tersedia.</p>
</div>
<div class="row g-4">
    @php
        $cards = [
            ['Daftar Simulasi','Lihat daftar simulasi yang dapat Anda ikuti','Lihat Ujian','fa-clipboard-list',route('exams.index')],
            ['Riwayat Ujian','Lihat hasil ujian yang telah Anda selesaikan','Lihat Riwayat','fa-clock-rotate-left',route('history')],
            ['Profil','Kelola informasi profil Anda','Edit Profil','fa-user-pen',route('profile.edit')],
            ['Bantuan','Panduan dan FAQ sistem ujian','Lihat Bantuan','fa-circle-question','#'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="col-md-6 col-lg-4">
            <div class="cat-card feature-card">
                <div class="icon-box"><i class="fa-solid {{ $card[3] }}"></i></div>
                <h4 class="fw-bold text-primary-emphasis">{{ $card[0] }}</h4>
                <p class="text-muted small mb-4">{{ $card[1] }}</p>
                <a class="btn btn-navy px-4 py-3" href="{{ $card[4] }}">{{ $card[2] }}</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

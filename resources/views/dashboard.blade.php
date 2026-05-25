@extends('layouts.app')
@section('content')
<div class="mb-5">
    <h1 class="page-title"><i class="fa-solid fa-gauge-high me-3"></i>Dashboard</h1>
    <p class="page-subtitle">Selamat datang di Aplikasi Simulasi CAT Uji Kompetensi Manajemen ASN. Silakan pilih menu yang tersedia.</p>
</div>
<div class="row g-4">
    @php
        $cards = [
            ['Daftar Simulasi','Lihat daftar simulasi uji kompetensi yang dapat Anda ikuti','Lihat Simulasi','fa-clipboard-list',route('exams.index')],
            ['Riwayat Ujian','Lihat hasil simulasi uji kompetensi yang telah Anda selesaikan','Lihat Riwayat','fa-clock-rotate-left',route('history')],
            ['Profil Peserta','Kelola informasi profil peserta uji kompetensi','Edit Profil','fa-user-pen',route('profile.edit')],
            ['Bank Regulasi','Lihat dasar hukum dan regulasi yang menjadi sumber materi soal','Lihat Regulasi','fa-scale-balanced',route('regulations.public')],
            ['Bantuan','Panduan penggunaan sistem simulasi uji kompetensi','Lihat Bantuan','fa-circle-question','#'],
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

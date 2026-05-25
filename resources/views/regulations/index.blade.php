@extends('layouts.app')
@section('content')
<h1 class="page-title mb-2"><i class="fa-solid fa-scale-balanced me-3"></i>Bank Regulasi</h1>
<p class="page-subtitle mb-4">Dasar hukum dan regulasi yang menjadi sumber materi soal.</p>
<div class="row g-4">
    @foreach($regulations as $regulation)
        <div class="col-md-6">
            <div class="cat-card p-4 h-100">
                <h4 class="fw-bold text-primary-emphasis">{{ $regulation->title }}</h4>
                <p class="mb-1"><strong>Nomor:</strong> {{ $regulation->regulation_number }}</p>
                <p class="mb-1"><strong>Tahun:</strong> {{ $regulation->year }}</p>
                <p class="mb-1"><strong>Kategori:</strong> {{ $regulation->category }}</p>
                <p class="text-muted">{{ $regulation->description }}</p>
                <a class="btn btn-navy" href="{{ route('regulations.public.show',$regulation) }}">Lihat Detail</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

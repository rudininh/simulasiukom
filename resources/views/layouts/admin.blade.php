@extends('layouts.app')
@section('content')
<div class="row g-4">
    <aside class="col-lg-3">
        <div class="admin-sidebar cat-card p-3">
            <h5 class="text-white fw-bold mb-3">Admin Panel</h5>
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard Admin</a>
            <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users me-2"></i>Manajemen Peserta</a>
            <a href="{{ route('admin.exams.index') }}"><i class="fa-solid fa-laptop me-2"></i>Manajemen Ujian</a>
            <a href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-layer-group me-2"></i>Kategori Ujian</a>
            <a href="{{ route('admin.questions.index') }}"><i class="fa-solid fa-file-lines me-2"></i>Bank Soal</a>
            <a href="{{ route('admin.regulations.index') }}"><i class="fa-solid fa-scale-balanced me-2"></i>Bank Regulasi</a>
            <a href="{{ route('admin.regulations.index') }}"><i class="fa-solid fa-upload me-2"></i>Upload Regulasi</a>
            <a href="{{ route('admin.regulations.index') }}"><i class="fa-solid fa-file-image me-2"></i>OCR Regulasi</a>
            <a href="{{ route('admin.regulations.index') }}"><i class="fa-solid fa-file-pdf me-2"></i>Generate Soal dari PDF</a>
            <a href="{{ route('admin.question-generator.index') }}"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Generate Soal</a>
            <a href="{{ route('admin.generated-questions.index') }}"><i class="fa-solid fa-list-check me-2"></i>Draft Soal AI</a>
            <a href="{{ route('admin.results.index') }}"><i class="fa-solid fa-chart-simple me-2"></i>Hasil Ujian</a>
            <a href="{{ route('admin.reports.index') }}"><i class="fa-solid fa-table me-2"></i>Rekap Nilai</a>
            <a href="{{ route('admin.settings.index') }}"><i class="fa-solid fa-gear me-2"></i>Pengaturan Aplikasi</a>
        </div>
    </aside>
    <section class="col-lg-9">@yield('admin-content')</section>
</div>
@endsection

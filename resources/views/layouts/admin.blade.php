@extends('layouts.app')
@section('content')
<div class="row g-4">
    <aside class="col-lg-3">
        <div class="admin-sidebar cat-card p-3">
            <h5 class="text-white fw-bold mb-3">Admin Panel</h5>
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
            <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users me-2"></i>Peserta</a>
            <a href="{{ route('admin.exams') }}"><i class="fa-solid fa-laptop me-2"></i>Ujian</a>
            <a href="{{ route('admin.categories') }}"><i class="fa-solid fa-layer-group me-2"></i>Kategori Soal</a>
            <a href="{{ route('admin.questions') }}"><i class="fa-solid fa-file-lines me-2"></i>Soal</a>
            <a href="{{ route('admin.attempts') }}"><i class="fa-solid fa-chart-simple me-2"></i>Hasil Ujian</a>
        </div>
    </aside>
    <section class="col-lg-9">@yield('admin-content')</section>
</div>
@endsection

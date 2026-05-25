@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Pengaturan Aplikasi</h1>
<div class="cat-card p-4">
    <p class="mb-1"><strong>Nama Aplikasi:</strong> SIMULASI CAT UJI KOMPETENSI MANAJEMEN ASN</p>
    <p class="mb-1"><strong>AI Provider:</strong> {{ config('ai.provider') }}</p>
    <p class="mb-0"><strong>Model:</strong> {{ config('ai.model') }}</p>
</div>
@endsection

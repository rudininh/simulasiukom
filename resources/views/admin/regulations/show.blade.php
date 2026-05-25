@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title mb-2">{{ $regulation->title }}</h1>
        <p class="page-subtitle">{{ $regulation->regulation_number }} | {{ $regulation->year }} | {{ $regulation->category }}</p>
    </div>
    <a class="btn btn-secondary" href="{{ route('admin.regulations.index') }}">Kembali</a>
</div>
@if($regulation->extraction_status === 'need_ocr')
    <div class="alert alert-warning">PDF ini kemungkinan berupa scan/gambar. Silakan jalankan OCR agar teks bisa dibaca.</div>
@endif
@if($regulation->extraction_status === 'ocr_completed')
    <div class="alert alert-success">OCR selesai. Teks siap digunakan untuk generate soal.</div>
@endif
<div class="row g-4">
    <div class="col-lg-5">
        <div class="cat-card p-4">
            <h5 class="fw-bold">Informasi Regulasi</h5>
            <div class="small d-grid gap-2">
                <div><strong>Judul:</strong> {{ $regulation->title }}</div>
                <div><strong>Nomor:</strong> {{ $regulation->regulation_number }}</div>
                <div><strong>Tahun:</strong> {{ $regulation->year }}</div>
                <div><strong>Kategori:</strong> {{ $regulation->category }}</div>
                <div><strong>Prioritas:</strong> {{ $regulation->priority }}</div>
                <div><strong>Status:</strong> {{ $regulation->status }}</div>
                <div><strong>Status Ekstraksi:</strong> {{ $regulation->extraction_status }}</div>
                <div><strong>Metode:</strong> {{ $regulation->extraction_method }}</div>
                <div><strong>Deskripsi:</strong> {{ $regulation->description }}</div>
                <div><strong>Catatan:</strong> {{ $regulation->usage_notes }}</div>
            </div>
        </div>
        <div class="cat-card p-3 mt-3 d-grid gap-2">
            <a class="btn btn-primary" href="{{ route('admin.regulations.preview',$regulation) }}">Lihat PDF/File</a>
            @if($regulation->file_path)<a class="btn btn-secondary" href="{{ route('admin.regulations.download',$regulation) }}">Download PDF/File</a>@endif
            <form method="post" action="{{ route('admin.regulations.extract-text',$regulation) }}">@csrf<button class="btn btn-navy w-100">Ekstrak Teks</button></form>
            <form method="post" action="{{ route('admin.regulations.ocr',$regulation) }}">@csrf<button class="btn btn-warning w-100">OCR PDF</button></form>
            <a class="btn btn-success" href="{{ route('admin.regulations.generate-questions',$regulation) }}">Generate Soal dari PDF</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.regulations.text',$regulation) }}">Lihat Hasil Ekstraksi</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.generated-questions.index',['regulation_id'=>$regulation->id]) }}">Lihat Draft Soal</a>
            <form method="post" action="{{ route('admin.regulations.summarize',$regulation) }}">@csrf<button class="btn btn-outline-success w-100">Generate Ringkasan Regulasi</button></form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="cat-card p-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Viewer</strong>
                @if($regulation->file_path)<a target="_blank" href="{{ asset('storage/'.$regulation->file_path) }}">Fullscreen / Open New Tab</a>@endif
            </div>
            @if($regulation->isPdf() && $regulation->file_path)
                <iframe src="{{ asset('storage/'.$regulation->file_path) }}" style="width:100%;height:640px;border:1px solid #ddd;border-radius:8px"></iframe>
            @elseif($regulation->file_path)
                <pre class="bg-light p-3" style="max-height:640px;overflow:auto">{{ $regulation->extracted_text ?: 'Preview teks belum tersedia.' }}</pre>
            @else
                <p class="text-muted">Belum ada file.</p>
            @endif
        </div>
        @if($regulation->summary)
            <div class="cat-card p-4 mt-3"><h5>Ringkasan</h5><pre class="mb-0">{{ $regulation->summary }}</pre></div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between mb-4">
    <div>
        <h1 class="page-title">Hasil Ekstraksi/OCR</h1>
        <p class="page-subtitle">{{ $regulation->title }}</p>
    </div>
    <a class="btn btn-secondary" href="{{ route('admin.regulations.show',$regulation) }}">Kembali</a>
</div>
<div class="cat-card p-4 mb-4">
    <div class="row g-3 small">
        <div class="col-md-3"><strong>Status:</strong> {{ $regulation->extraction_status }}</div>
        <div class="col-md-3"><strong>Metode:</strong> {{ $regulation->extraction_method }}</div>
        <div class="col-md-3"><strong>Halaman:</strong> {{ $regulation->page_count ?: $regulation->pages->count() }}</div>
        <div class="col-md-3"><strong>Karakter:</strong> {{ strlen($regulation->extracted_text ?? '') }}</div>
    </div>
    <div class="mt-3 d-flex gap-2 flex-wrap">
        <button class="btn btn-navy" onclick="navigator.clipboard.writeText(document.getElementById('extractText').value)">Salin Teks</button>
        <a class="btn btn-secondary" href="{{ route('admin.regulations.text.download',$regulation) }}">Download TXT</a>
        <form method="post" action="{{ route('admin.regulations.summarize',$regulation) }}">@csrf<button class="btn btn-success">Generate Ringkasan</button></form>
        <a class="btn btn-primary" href="{{ route('admin.regulations.generate-questions',$regulation) }}">Generate Soal</a>
    </div>
</div>
<div class="cat-card p-3 mb-4">
    <textarea id="extractText" class="form-control" rows="16">{{ $regulation->extracted_text }}</textarea>
</div>
@if($regulation->pages->count())
<div class="accordion" id="pageAccordion">
    @foreach($regulation->pages as $page)
        <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#page{{ $page->id }}">Halaman {{ $page->page_number }} - {{ $page->status }}</button></h2>
            <div id="page{{ $page->id }}" class="accordion-collapse collapse" data-bs-parent="#pageAccordion">
                <div class="accordion-body"><pre style="white-space:pre-wrap">{{ $page->ocr_text ?: $page->text }}</pre></div>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection

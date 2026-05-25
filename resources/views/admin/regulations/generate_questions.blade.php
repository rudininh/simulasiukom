@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Generate Soal dari PDF Regulasi</h1>
<div class="cat-card p-4">
    <h5>{{ $regulation->title }}</h5>
    @if(!$regulation->extracted_text)
        <div class="alert alert-warning">Teks regulasi belum tersedia. Jalankan ekstraksi teks atau OCR terlebih dahulu.</div>
    @endif
    <form method="post" action="{{ route('admin.regulations.generate-questions.store',$regulation) }}" class="row g-3">@csrf
        <div class="col-md-6"><label class="form-label">Ujian Tujuan</label><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Kategori Soal</label><select class="form-select" name="exam_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->exam->title }} - {{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Jumlah</label><input class="form-control" name="count" type="number" value="5" min="1" max="20"></div>
        <div class="col-md-3"><label class="form-label">Kesulitan</label><select class="form-select" name="difficulty"><option value="easy">Mudah</option><option value="medium">Sedang</option><option value="hard">Sulit</option><option value="case">Studi Kasus</option></select></div>
        <div class="col-md-4"><label class="form-label">Tipe Soal</label><select class="form-select" name="question_type"><option>Pemahaman pasal</option><option>Definisi konsep</option><option>Prosedur administratif</option><option>Penerapan aturan</option><option>Analisis kasus</option><option>Etika dan pengambilan keputusan</option></select></div>
        <div class="col-md-3"><label class="form-label">Gunakan OCR text</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="use_ocr_text" value="1"> <label class="form-check-label">Ya</label></div></div>
        <div class="col-md-2"><label class="form-label">Dari halaman</label><input class="form-control" name="page_from" type="number" min="1"></div>
        <div class="col-md-2"><label class="form-label">Sampai halaman</label><input class="form-control" name="page_until" type="number" min="1"></div>
        <div class="col-md-8"><label class="form-label">Kata kunci fokus</label><input class="form-control" name="keywords" placeholder="izin cerai, pejabat, surat keterangan, hukuman disiplin"></div>
        <div class="col-12"><button class="btn btn-success">Generate Draft Soal</button></div>
    </form>
</div>
@endsection

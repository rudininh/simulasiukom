@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Generate Soal dari Regulasi</h1>
<div class="cat-card p-4">
    <form method="post" action="{{ route('admin.question-generator.generate') }}" class="row g-3">@csrf
        <div class="col-md-6"><label class="form-label">Regulasi</label><select class="form-select" name="regulation_id" required>@foreach($regulations as $regulation)<option value="{{ $regulation->id }}">{{ $regulation->title }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Ujian Tujuan</label><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Kategori Soal</label><select class="form-select" name="exam_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->exam->title }} - {{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Jumlah</label><input class="form-control" type="number" name="count" min="1" max="20" value="5"></div>
        <div class="col-md-2"><label class="form-label">Kesulitan</label><select class="form-select" name="difficulty"><option value="easy">Mudah</option><option value="medium">Sedang</option><option value="hard">Sulit</option><option value="case">Studi kasus</option></select></div>
        <div class="col-md-2"><label class="form-label">Tipe</label><select class="form-select" name="question_type"><option>Pemahaman pasal</option><option>Definisi konsep</option><option>Penerapan aturan</option><option>Analisis kasus</option><option>Etika dan pengambilan keputusan</option></select></div>
        <div class="col-12"><button class="btn btn-success">Generate Draft Soal</button></div>
    </form>
</div>
@endsection

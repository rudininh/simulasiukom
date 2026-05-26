@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between mb-4">
    <h1 class="page-title">Preview Regulasi</h1>
    <a class="btn btn-secondary" href="{{ route('admin.regulations.show',$regulation) }}">Kembali</a>
</div>
<div class="cat-card p-3">
    <h5 class="fw-bold">{{ $regulation->title }}</h5>
    @if($regulation->isPdf())
        <iframe src="{{ Storage::url($regulation->file_path) }}" style="width:100%;height:800px;border:1px solid #ddd;border-radius:8px"></iframe>
    @else
        <pre class="bg-light p-3" style="white-space:pre-wrap">{{ $regulation->extracted_text ?: 'Preview teks belum tersedia.' }}</pre>
    @endif
</div>
@endsection

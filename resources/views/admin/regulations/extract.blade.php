@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Ekstraksi Teks Regulasi</h1>
<div class="cat-card p-4">
    <h4>{{ $regulation->title }}</h4>
    <form method="post" action="{{ route('admin.regulations.extract.run',$regulation) }}" class="mb-3">@csrf
        <button class="btn btn-navy">Ekstrak Ulang</button>
        <a class="btn btn-secondary" href="{{ route('admin.regulations.index') }}">Kembali</a>
    </form>
    <textarea class="form-control" rows="18" readonly>{{ $regulation->extracted_text }}</textarea>
</div>
@endsection

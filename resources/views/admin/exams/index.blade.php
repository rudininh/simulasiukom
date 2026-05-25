@extends('layouts.admin')
@section('admin-content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="page-title mb-0">CRUD Ujian</h1>
    <form method="post" action="{{ route('admin.exams.sync-categories') }}">@csrf
        <button class="btn btn-warning"><i class="fa-solid fa-rotate me-1"></i> Sinkron Kategori 100 Soal</button>
    </form>
</div>
<div class="cat-card p-3 mb-4">
<form method="post" action="{{ route('admin.exams.store') }}" class="row g-2">@csrf
    <div class="col-md-6"><input class="form-control" name="title" placeholder="Nama ujian" required></div>
    <div class="col-md-3"><input class="form-control" name="exam_type" placeholder="Jenis ujian"></div>
    <div class="col-md-3"><input class="form-control" name="duration_minutes" type="number" placeholder="Durasi menit" required></div>
    <div class="col-md-3"><input class="form-control" name="total_questions" type="number" placeholder="Total soal" required></div>
    <div class="col-md-3"><input class="form-control" name="passing_grade" type="number" step="0.01" placeholder="Passing grade" required></div>
    <div class="col-md-3"><input class="form-control" name="available_from" type="date"></div>
    <div class="col-md-3"><input class="form-control" name="available_until" type="date"></div>
    <div class="col-md-3"><input class="form-control" name="regulation_basis" placeholder="Dasar regulasi"></div>
    <div class="col-md-3 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Aktif</div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi"></textarea></div>
    <div class="col-12"><button class="btn btn-navy">Tambah Ujian</button></div>
</form></div>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Nama</th><th>Durasi</th><th>Soal</th><th>PG</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach ($exams as $exam)<tr><form method="post" action="{{ route('admin.exams.update',$exam) }}">@csrf @method('PUT')
<td><input class="form-control" name="title" value="{{ $exam->title }}"><input class="form-control mt-1" name="exam_type" value="{{ $exam->exam_type }}"><textarea class="form-control mt-1" name="description">{{ $exam->description }}</textarea><input class="form-control mt-1" name="regulation_basis" value="{{ $exam->regulation_basis }}"></td>
<td><input class="form-control" name="duration_minutes" type="number" value="{{ $exam->duration_minutes }}"></td><td><input class="form-control" name="total_questions" type="number" value="{{ $exam->total_questions }}"></td><td><input class="form-control" name="passing_grade" type="number" step="0.01" value="{{ $exam->passing_grade }}"></td>
<td><input class="form-control mb-1" name="available_from" type="date" value="{{ optional($exam->available_from)->format('Y-m-d') }}"><input class="form-control mb-1" name="available_until" type="date" value="{{ optional($exam->available_until)->format('Y-m-d') }}"><label><input type="checkbox" name="is_active" value="1" @checked($exam->is_active)> Aktif</label></td>
<td><button class="btn btn-sm btn-primary mb-1">Simpan</button></form><form method="post" action="{{ route('admin.exams.destroy',$exam) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>@endforeach
</tbody></table>{{ $exams->links() }}</div>
@endsection

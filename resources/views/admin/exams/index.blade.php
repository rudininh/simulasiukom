@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">CRUD Ujian</h1>
<div class="cat-card p-3 mb-4">
<form method="post" action="{{ route('admin.exams.store') }}" class="row g-2">@csrf
    <div class="col-md-6"><input class="form-control" name="title" placeholder="Nama ujian" required></div>
    <div class="col-md-3"><input class="form-control" name="duration_minutes" type="number" placeholder="Durasi menit" required></div>
    <div class="col-md-3"><input class="form-control" name="total_questions" type="number" placeholder="Total soal" required></div>
    <div class="col-md-3"><input class="form-control" name="passing_grade" type="number" step="0.01" placeholder="Passing grade" required></div>
    <div class="col-md-3"><input class="form-control" name="available_from" type="date"></div>
    <div class="col-md-3"><input class="form-control" name="available_until" type="date"></div>
    <div class="col-md-3 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Aktif</div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi"></textarea></div>
    <div class="col-12"><button class="btn btn-navy">Tambah Ujian</button></div>
</form></div>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Nama</th><th>Durasi</th><th>Soal</th><th>PG</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach ($exams as $exam)<tr><form method="post" action="{{ route('admin.exams.update',$exam) }}">@csrf @method('PUT')
<td><input class="form-control" name="title" value="{{ $exam->title }}"><textarea class="form-control mt-1" name="description">{{ $exam->description }}</textarea></td>
<td><input class="form-control" name="duration_minutes" type="number" value="{{ $exam->duration_minutes }}"></td><td><input class="form-control" name="total_questions" type="number" value="{{ $exam->total_questions }}"></td><td><input class="form-control" name="passing_grade" type="number" step="0.01" value="{{ $exam->passing_grade }}"></td>
<td><input class="form-control mb-1" name="available_from" type="date" value="{{ optional($exam->available_from)->format('Y-m-d') }}"><input class="form-control mb-1" name="available_until" type="date" value="{{ optional($exam->available_until)->format('Y-m-d') }}"><label><input type="checkbox" name="is_active" value="1" @checked($exam->is_active)> Aktif</label></td>
<td><button class="btn btn-sm btn-primary mb-1">Simpan</button></form><form method="post" action="{{ route('admin.exams.destroy',$exam) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>@endforeach
</tbody></table>{{ $exams->links() }}</div>
@endsection

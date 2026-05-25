@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">CRUD Kategori Soal</h1>
<div class="cat-card p-3 mb-4"><form method="post" action="{{ route('admin.categories.store') }}" class="row g-2">@csrf
    <div class="col-md-4"><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Nama kategori" required></div>
    <div class="col-md-2"><input class="form-control" name="code" placeholder="TWK" required></div>
    <div class="col-md-2"><input class="form-control" name="question_count" type="number" placeholder="Jumlah" required></div>
    <div class="col-md-1"><button class="btn btn-navy">Tambah</button></div>
</form></div>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Ujian</th><th>Nama</th><th>Kode</th><th>Jumlah</th><th>Passing</th><th>Aksi</th></tr></thead><tbody>
@foreach($categories as $category)<tr><form method="post" action="{{ route('admin.categories.update',$category) }}">@csrf @method('PUT')
<td><select class="form-select" name="exam_id">@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected($category->exam_id===$exam->id)>{{ $exam->title }}</option>@endforeach</select></td>
<td><input class="form-control" name="name" value="{{ $category->name }}"></td><td><input class="form-control" name="code" value="{{ $category->code }}"></td><td><input class="form-control" name="question_count" type="number" value="{{ $category->question_count }}"></td><td><input class="form-control" name="passing_score" type="number" step="0.01" value="{{ $category->passing_score }}"></td>
<td><button class="btn btn-sm btn-primary mb-1">Simpan</button></form><form method="post" action="{{ route('admin.categories.destroy',$category) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>@endforeach
</tbody></table>{{ $categories->links() }}</div>
@endsection

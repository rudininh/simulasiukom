@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">CRUD Soal</h1>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.questions.store') }}" class="row g-2">@csrf
        <div class="col-md-4"><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-4"><select class="form-select" name="exam_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->exam->title }} - {{ $category->code }}</option>@endforeach</select></div>
        <div class="col-md-2"><input class="form-control" name="order_number" type="number" placeholder="Urutan" required></div>
        <div class="col-md-2"><input class="form-control" name="score" type="number" value="5" required></div>
        <div class="col-12"><textarea class="form-control" name="question_text" placeholder="Pertanyaan" required></textarea></div>
        @foreach(['a','b','c','d','e'] as $letter)<div class="col-md"><input class="form-control" name="option_{{ $letter }}" placeholder="Opsi {{ strtoupper($letter) }}" required></div>@endforeach
        <div class="col-md-2"><select class="form-select" name="correct_answer">@foreach(['A','B','C','D','E'] as $letter)<option>{{ $letter }}</option>@endforeach</select></div>
        <div class="col-md-2 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Aktif</div>
        <div class="col-md-2"><button class="btn btn-navy">Tambah Soal</button></div>
    </form>
    <hr>
    <form method="post" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-4"><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-4"><select class="form-select" name="exam_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->exam->title }} - {{ $category->code }}</option>@endforeach</select></div>
        <div class="col-md-3"><input class="form-control" type="file" name="file" accept=".csv,.txt" required></div>
        <div class="col-md-1"><button class="btn btn-success">Import</button></div>
    </form>
</div>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Ujian</th><th>Kategori</th><th>Soal</th><th>Jawaban</th><th>Aksi</th></tr></thead><tbody>
@foreach($questions as $question)<tr><form method="post" action="{{ route('admin.questions.update',$question) }}">@csrf @method('PUT')
<td><select class="form-select" name="exam_id">@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected($question->exam_id===$exam->id)>{{ $exam->title }}</option>@endforeach</select><input class="form-control mt-1" name="order_number" type="number" value="{{ $question->order_number }}"></td>
<td><select class="form-select" name="exam_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected($question->exam_category_id===$category->id)>{{ $category->code }}</option>@endforeach</select><input class="form-control mt-1" name="score" type="number" value="{{ $question->score }}"></td>
<td><textarea class="form-control mb-1" name="question_text">{{ $question->question_text }}</textarea>@foreach(['a','b','c','d','e'] as $letter)<input class="form-control mb-1" name="option_{{ $letter }}" value="{{ $question->{'option_'.$letter} }}">@endforeach</td>
<td><select class="form-select mb-1" name="correct_answer">@foreach(['A','B','C','D','E'] as $letter)<option @selected($question->correct_answer===$letter)>{{ $letter }}</option>@endforeach</select><label><input type="checkbox" name="is_active" value="1" @checked($question->is_active)> Aktif</label></td>
<td><button class="btn btn-sm btn-primary mb-1">Simpan</button></form><form method="post" action="{{ route('admin.questions.destroy',$question) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>@endforeach
</tbody></table>{{ $questions->links() }}</div>
@endsection

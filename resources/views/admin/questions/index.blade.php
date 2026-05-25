@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Bank Soal</h1>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.questions.store') }}" class="row g-2">@csrf
        <div class="col-md-4"><select class="form-select" name="exam_id" required>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-4"><select class="form-select" name="exam_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->exam->title }} - {{ $category->code }}</option>@endforeach</select></div>
        <div class="col-md-2"><input class="form-control" name="order_number" type="number" placeholder="Urutan" required></div>
        <div class="col-md-2"><input class="form-control" name="score" type="number" value="1" required></div>
        <div class="col-md-4"><select class="form-select" name="regulation_id"><option value="">Tanpa regulasi</option>@foreach($regulations as $regulation)<option value="{{ $regulation->id }}">{{ $regulation->title }}</option>@endforeach</select></div>
        <div class="col-12"><textarea class="form-control" name="question_text" placeholder="Pertanyaan" required></textarea></div>
        @foreach(['a','b','c','d','e'] as $letter)<div class="col-md"><input class="form-control" name="option_{{ $letter }}" placeholder="Opsi {{ strtoupper($letter) }}" required></div>@endforeach
        <div class="col-md-2"><select class="form-select" name="correct_answer">@foreach(['A','B','C','D','E'] as $letter)<option>{{ $letter }}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-select" name="difficulty">@foreach(['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit','case'=>'Studi kasus','calculation'=>'Hitungan'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-12"><textarea class="form-control" name="explanation" placeholder="Pembahasan"></textarea></div>
        <div class="col-md-4"><input class="form-control" name="source_reference" placeholder="Sumber/pasal"></div>
        <div class="col-md-3"><input class="form-control" name="question_type" placeholder="Tipe soal"></div>
        <div class="col-md-2"><input class="form-control" name="source_page" type="number" placeholder="Halaman"></div>
        <div class="col-md-2 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Aktif</div>
        <div class="col-md-2"><button class="btn btn-navy">Tambah Soal</button></div>
    </form>
    <hr>
    <form method="post" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-10"><input class="form-control" type="file" name="file" accept=".csv,.txt" required></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Import CSV</button></div>
    </form>
</div>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Ujian</th><th>Kategori</th><th>Soal</th><th>Jawaban</th><th>Aksi</th></tr></thead><tbody>
@foreach($questions as $question)<tr><form method="post" action="{{ route('admin.questions.update',$question) }}">@csrf @method('PUT')
<td><select class="form-select" name="exam_id">@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected($question->exam_id===$exam->id)>{{ $exam->title }}</option>@endforeach</select><input class="form-control mt-1" name="order_number" type="number" value="{{ $question->order_number }}"></td>
<td><select class="form-select" name="exam_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected($question->exam_category_id===$category->id)>{{ $category->code }}</option>@endforeach</select><input class="form-control mt-1" name="score" type="number" value="{{ $question->score }}"><select class="form-select mt-1" name="difficulty">@foreach(['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit','case'=>'Studi kasus','calculation'=>'Hitungan'] as $value=>$label)<option value="{{ $value }}" @selected($question->difficulty===$value)>{{ $label }}</option>@endforeach</select></td>
<td><select class="form-select mb-1" name="regulation_id"><option value="">Tanpa regulasi</option>@foreach($regulations as $regulation)<option value="{{ $regulation->id }}" @selected($question->regulation_id===$regulation->id)>{{ $regulation->title }}</option>@endforeach</select>@if($question->category?->code === 'PERKAWINAN_PERCERAIAN_ASN')<span class="badge bg-warning text-dark mb-1">Izin Perkawinan/Perceraian ASN</span>@endif<textarea class="form-control mb-1" name="question_text">{{ $question->question_text }}</textarea>@foreach(['a','b','c','d','e'] as $letter)<input class="form-control mb-1" name="option_{{ $letter }}" value="{{ $question->{'option_'.$letter} }}">@endforeach<textarea class="form-control mb-1" name="explanation">{{ $question->explanation }}</textarea><input class="form-control mb-1" name="source_reference" value="{{ $question->source_reference }}"><input class="form-control mb-1" name="question_type" value="{{ $question->question_type }}"><input class="form-control" name="source_page" type="number" value="{{ $question->source_page }}" placeholder="Halaman"></td>
<td><select class="form-select mb-1" name="correct_answer">@foreach(['A','B','C','D','E'] as $letter)<option @selected($question->correct_answer===$letter)>{{ $letter }}</option>@endforeach</select><label><input type="checkbox" name="is_active" value="1" @checked($question->is_active)> Aktif</label></td>
<td><button class="btn btn-sm btn-primary mb-1">Simpan</button></form><form method="post" action="{{ route('admin.questions.destroy',$question) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>@endforeach
</tbody></table>{{ $questions->links() }}</div>
@endsection

@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Edit Draft Soal</h1>
<div class="cat-card p-4">
    <form method="post" action="{{ route('admin.generated-questions.update',$generatedQuestion) }}" class="row g-3">@csrf @method('PUT')
        <div class="col-12"><textarea class="form-control" name="question_text" rows="3" required>{{ $generatedQuestion->question_text }}</textarea></div>
        @foreach(['a','b','c','d','e'] as $letter)<div class="col-md"><input class="form-control" name="option_{{ $letter }}" value="{{ $generatedQuestion->{'option_'.$letter} }}" required></div>@endforeach
        <div class="col-md-2"><select class="form-select" name="correct_answer">@foreach(['A','B','C','D','E'] as $letter)<option @selected($generatedQuestion->correct_answer===$letter)>{{ $letter }}</option>@endforeach</select></div>
        <div class="col-md-3"><input class="form-control" name="difficulty" value="{{ $generatedQuestion->difficulty }}"></div>
        <div class="col-md-4"><input class="form-control" name="question_type" value="{{ $generatedQuestion->question_type }}"></div>
        <div class="col-md-5"><input class="form-control" name="source_reference" value="{{ $generatedQuestion->source_reference }}"></div>
        <div class="col-12"><textarea class="form-control" name="explanation" rows="4">{{ $generatedQuestion->explanation }}</textarea></div>
        <div class="col-12"><button class="btn btn-navy">Simpan Draft</button></div>
    </form>
</div>
@endsection

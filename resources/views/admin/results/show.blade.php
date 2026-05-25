@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Detail Hasil Ujian</h1>
<div class="cat-card p-4">
    <h4>{{ $attempt->exam->title }}</h4>
    <p>{{ $attempt->user->name }} - {{ strtoupper(str_replace('_',' ', $attempt->competency_status)) }}</p>
    <div class="row g-3">
        @foreach($attempt->categoryScores as $score)
            <div class="col-md-2"><div class="cat-card p-3 text-center"><strong>{{ $score->score }}</strong><br><small>{{ $score->category->name }}</small></div></div>
        @endforeach
        <div class="col-md-2"><div class="cat-card p-3 text-center"><strong>{{ $attempt->score_total }}</strong><br><small>Total</small></div></div>
    </div>
</div>
@endsection

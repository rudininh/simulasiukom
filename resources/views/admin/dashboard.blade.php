@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Dashboard Admin</h1>
<div class="row g-3">
    @foreach ([['Peserta',$users,'fa-users'],['Ujian',$exams,'fa-laptop'],['Soal',$questions,'fa-file-lines'],['Hasil',$attempts,'fa-chart-line']] as $card)
        <div class="col-md-3"><div class="cat-card p-4"><div class="icon-box"><i class="fa-solid {{ $card[2] }}"></i></div><h3>{{ $card[1] }}</h3><p class="fw-bold text-muted mb-0">{{ $card[0] }}</p></div></div>
    @endforeach
</div>
@endsection

@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Detail Hasil Ujian</h1>
<div class="cat-card p-4">
    <h4>{{ $attempt->exam->title }}</h4>
    <p>{{ $attempt->user->name }} - {{ strtoupper(str_replace('_',' ', $attempt->competency_status)) }}</p>
    <div class="row g-3">
        @foreach([['Regulasi ASN',$attempt->score_regulasi_asn],['Manajemen ASN',$attempt->score_manajemen_asn],['Kepemimpinan',$attempt->score_kepemimpinan],['Pelayanan Publik',$attempt->score_pelayanan_publik],['Studi Kasus',$attempt->score_studi_kasus],['Total',$attempt->score_total]] as $score)
            <div class="col-md-2"><div class="cat-card p-3 text-center"><strong>{{ $score[1] }}</strong><br><small>{{ $score[0] }}</small></div></div>
        @endforeach
    </div>
</div>
@endsection

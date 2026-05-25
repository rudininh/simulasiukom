@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Dashboard Admin</h1>
<div class="row g-3">
    @foreach ([['Peserta',$users,'fa-users'],['Ujian Aktif',$exams,'fa-laptop'],['Soal',$questions,'fa-file-lines'],['Regulasi',$regulations,'fa-scale-balanced'],['Draft Soal',$drafts,'fa-list-check'],['Ujian Selesai',$attempts,'fa-chart-line']] as $card)
        <div class="col-md-3"><div class="cat-card p-4"><div class="icon-box"><i class="fa-solid {{ $card[2] }}"></i></div><h3>{{ $card[1] }}</h3><p class="fw-bold text-muted mb-0">{{ $card[0] }}</p></div></div>
    @endforeach
</div>
<div class="cat-card p-4 mt-4">
    <h5 class="fw-bold">Grafik Kompetensi</h5>
    <div class="progress" style="height:30px">
        @php $total=max(1,$kompeten+$belumKompeten); @endphp
        <div class="progress-bar bg-success" style="width:{{ $kompeten/$total*100 }}%">Kompeten: {{ $kompeten }}</div>
        <div class="progress-bar bg-danger" style="width:{{ $belumKompeten/$total*100 }}%">Belum Kompeten: {{ $belumKompeten }}</div>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-lg-4">
        <div class="cat-card p-4 h-100">
            <h5 class="fw-bold">Soal per Kategori</h5>
            @foreach($questionsByCategory as $item)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->name }}</span><strong>{{ $item->total }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="cat-card p-4 h-100">
            <h5 class="fw-bold">Regulasi per Kategori</h5>
            @foreach($regulationsByCategory as $item)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->category }}</span><strong>{{ $item->total }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="cat-card p-4 h-100">
            <h5 class="fw-bold">Draft AI per Kategori</h5>
            <div class="alert alert-info py-2">Regulasi siap generate soal: <strong>{{ $readyRegulations }}</strong></div>
            @forelse($draftsByCategory as $item)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->name }}</span><strong>{{ $item->total }}</strong></div>
            @empty
                <p class="text-muted mb-0">Belum ada draft soal AI.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

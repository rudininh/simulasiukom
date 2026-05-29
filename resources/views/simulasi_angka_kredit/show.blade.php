@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h1 class="page-title mb-1"><i class="fa-solid fa-file-lines me-3"></i>Detail Simulasi Angka Kredit</h1>
        <p class="page-subtitle mb-0">Disimpan pada {{ optional($simulation->created_at)->format('d/m/Y H:i') }}.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-secondary" href="{{ route('angka-kredit.history') }}"><i class="fa-solid fa-arrow-left me-2"></i>Riwayat</a>
        <button class="btn btn-outline-dark" type="button" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Cetak</button>
    </div>
</div>

@include('simulasi_angka_kredit.partials.result', ['result' => $result])

@if(!empty($simulation->data_input))
    <div class="cat-card p-4 mt-4">
        <h2 class="h5 fw-bold text-primary-emphasis mb-3">Data Input</h2>
        <div class="row g-2 small">
            @foreach($simulation->data_input as $key => $value)
                @continue(is_array($value))
                <div class="col-md-4">
                    <div class="border rounded p-2 h-100">
                        <div class="text-muted">{{ str_replace('_', ' ', $key) }}</div>
                        <div class="fw-semibold">{{ $value === null || $value === '' ? '-' : $value }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection

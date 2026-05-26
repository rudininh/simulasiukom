@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title mb-2">{{ $regulation->title }}</h1>
        <p class="page-subtitle">{{ $regulation->regulation_number }} | {{ $regulation->year }} | {{ $regulation->category }}</p>
    </div>
    <a class="btn btn-secondary" href="{{ route('regulations.public') }}">Kembali</a>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="cat-card p-4">
            <h5 class="fw-bold">Informasi Regulasi</h5>
            <p><strong>Prioritas:</strong> {{ $regulation->priority }}</p>
            <p><strong>Status:</strong> {{ $regulation->status }}</p>
            <p>{{ $regulation->description }}</p>
            <p class="text-muted">{{ $regulation->usage_notes }}</p>
            @if($regulation->keywords)
                <div class="mb-3">@foreach($regulation->keywords as $keyword)<span class="badge bg-primary me-1">{{ $keyword }}</span>@endforeach</div>
            @endif
            @if($regulation->file_path)
                <a class="btn btn-navy w-100" href="{{ route('regulations.public.preview',$regulation) }}">Baca PDF/File</a>
                @if($regulation->can_download_by_participant)
                    <a class="btn btn-secondary w-100 mt-2" href="{{ route('regulations.public.download',$regulation) }}">Download PDF/File</a>
                @endif
            @endif
        </div>
        @if($regulation->summary)
            <div class="cat-card p-4 mt-3"><h5>Ringkasan</h5><pre class="mb-0" style="white-space:pre-wrap">{{ $regulation->summary }}</pre></div>
        @endif
    </div>
    <div class="col-lg-8">
        <div class="cat-card p-4">
            <h5 class="fw-bold">Cuplikan Teks Regulasi</h5>
            <pre style="white-space:pre-wrap;max-height:600px;overflow:auto">{{ \Illuminate\Support\Str::limit($regulation->extracted_text, 5000) }}</pre>
        </div>
    </div>
</div>
@endsection

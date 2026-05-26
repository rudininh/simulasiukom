@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title mb-2">{{ $regulation->title }}</h1>
        <p class="page-subtitle">{{ $regulation->regulation_number }} | {{ $regulation->year }} | {{ $regulation->category }}</p>
    </div>
    <a class="btn btn-secondary" href="{{ route('admin.regulations.index') }}">Kembali</a>
</div>
@if($regulation->extraction_status === 'need_ocr')
    <div class="alert alert-warning">PDF ini kemungkinan berupa scan/gambar. Silakan jalankan OCR agar teks bisa dibaca.</div>
@endif
@if($regulation->extraction_status === 'ocr_completed')
    <div class="alert alert-success">OCR selesai. Teks siap digunakan untuk generate soal.</div>
@endif
<div class="row g-4">
    <div class="col-lg-5">
        <div class="cat-card p-4">
            <h5 class="fw-bold">Informasi Regulasi</h5>
            <div class="small d-grid gap-2">
                <div><strong>Judul:</strong> {{ $regulation->title }}</div>
                <div><strong>Nomor:</strong> {{ $regulation->regulation_number }}</div>
                <div><strong>Tahun:</strong> {{ $regulation->year }}</div>
                <div><strong>Kategori:</strong> {{ $regulation->category }}</div>
                <div><strong>Prioritas:</strong> {{ $regulation->priority }}</div>
                <div><strong>Status:</strong> {{ $regulation->status }}</div>
                <div><strong>Status PDF:</strong> {{ $regulation->download_status }}</div>
                <div><strong>Boleh Download Peserta:</strong> {{ $regulation->can_download_by_participant ? 'Ya' : 'Tidak' }}</div>
                @if($regulation->official_url)<div><strong>URL Resmi:</strong> <a href="{{ $regulation->official_url }}" target="_blank">{{ $regulation->official_url }}</a></div>@endif
                @if($regulation->pdf_url)<div><strong>URL PDF:</strong> <a href="{{ $regulation->pdf_url }}" target="_blank">{{ $regulation->pdf_url }}</a></div>@endif
                @if($regulation->download_error)<div><strong>Error Download:</strong> {{ $regulation->download_error }}</div>@endif
                <div><strong>Status Ekstraksi:</strong> {{ $regulation->extraction_status }}</div>
                <div><strong>Metode:</strong> {{ $regulation->extraction_method }}</div>
                <div><strong>Deskripsi:</strong> {{ $regulation->description }}</div>
                <div><strong>Catatan:</strong> {{ $regulation->usage_notes }}</div>
            </div>
        </div>
        <div class="cat-card p-3 mt-3 d-grid gap-2">
            @if($regulation->file_path)<a class="btn btn-primary" href="{{ route('admin.regulations.preview',$regulation) }}">Lihat PDF/File</a>@endif
            @if($regulation->file_path)<a class="btn btn-secondary" href="{{ route('admin.regulations.download',$regulation) }}">Download PDF/File</a>@endif
            @if($regulation->pdf_url)<form method="post" action="{{ route('admin.regulations.download-pdf',$regulation) }}">@csrf<button class="btn btn-info w-100">Download PDF dari URL</button></form>@endif
            <form method="post" action="{{ route('admin.regulations.extract-text',$regulation) }}">@csrf<button class="btn btn-navy w-100">Ekstrak Teks</button></form>
            <form method="post" action="{{ route('admin.regulations.ocr',$regulation) }}">@csrf<button class="btn btn-warning w-100">OCR PDF</button></form>
            <a class="btn btn-success" href="{{ route('admin.regulations.generate-questions',$regulation) }}">Generate Soal dari PDF</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.regulations.text',$regulation) }}">Lihat Hasil Ekstraksi</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.generated-questions.index',['regulation_id'=>$regulation->id]) }}">Lihat Draft Soal</a>
            <form method="post" action="{{ route('admin.regulations.summarize',$regulation) }}">@csrf<button class="btn btn-outline-success w-100">Generate Ringkasan Regulasi</button></form>
        </div>
        <div class="cat-card p-3 mt-3">
            <h5 class="fw-bold mb-3">Kelola File / Link</h5>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#uploadPdfPanel" aria-expanded="false" aria-controls="uploadPdfPanel">
                    Upload PDF Aturan
                </button>
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#linkAturanPanel" aria-expanded="false" aria-controls="linkAturanPanel">
                    Isi Link Aturan
                </button>
            </div>

            <div class="collapse mt-3" id="uploadPdfPanel">
                <form method="post" action="{{ route('admin.regulations.update', $regulation) }}" enctype="multipart/form-data" class="border rounded-3 p-3 bg-light">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="title" value="{{ $regulation->title }}">
                    <input type="hidden" name="regulation_number" value="{{ $regulation->regulation_number }}">
                    <input type="hidden" name="year" value="{{ $regulation->year }}">
                    <input type="hidden" name="category" value="{{ $regulation->category }}">
                    <input type="hidden" name="priority" value="{{ $regulation->priority }}">
                    <input type="hidden" name="description" value="{{ $regulation->description }}">
                    <input type="hidden" name="usage_notes" value="{{ $regulation->usage_notes }}">
                    <input type="hidden" name="official_url" value="{{ $regulation->official_url }}">
                    <input type="hidden" name="pdf_url" value="{{ $regulation->pdf_url }}">
                    <input type="hidden" name="status" value="{{ $regulation->status }}">
                    <input type="hidden" name="can_download_by_participant" value="{{ $regulation->can_download_by_participant ? 1 : 0 }}">
                    <input type="hidden" name="auto_download_pdf" value="0">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Upload PDF Aturan</label>
                        <input class="form-control" type="file" name="file" accept=".pdf" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Simpan PDF</button>
                </form>
            </div>

            <div class="collapse mt-3" id="linkAturanPanel">
                <form method="post" action="{{ route('admin.regulations.update', $regulation) }}" class="border rounded-3 p-3 bg-light">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="title" value="{{ $regulation->title }}">
                    <input type="hidden" name="regulation_number" value="{{ $regulation->regulation_number }}">
                    <input type="hidden" name="year" value="{{ $regulation->year }}">
                    <input type="hidden" name="category" value="{{ $regulation->category }}">
                    <input type="hidden" name="priority" value="{{ $regulation->priority }}">
                    <input type="hidden" name="description" value="{{ $regulation->description }}">
                    <input type="hidden" name="usage_notes" value="{{ $regulation->usage_notes }}">
                    <input type="hidden" name="status" value="{{ $regulation->status }}">
                    <input type="hidden" name="can_download_by_participant" value="{{ $regulation->can_download_by_participant ? 1 : 0 }}">
                    <input type="hidden" name="auto_download_pdf" value="1">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Link Resmi Aturan</label>
                        <input class="form-control" type="url" name="official_url" value="{{ $regulation->official_url }}" placeholder="https://...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Link PDF Aturan</label>
                        <input class="form-control" type="url" name="pdf_url" value="{{ $regulation->pdf_url }}" placeholder="https://...pdf">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="can_download_by_participant" value="1" id="quick-download-{{ $regulation->id }}" @checked($regulation->can_download_by_participant)>
                        <label class="form-check-label" for="quick-download-{{ $regulation->id }}">Peserta boleh download file</label>
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit">Simpan Link</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="cat-card p-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Viewer</strong>
                @if($regulation->file_path)<a target="_blank" href="{{ asset('storage/'.$regulation->file_path) }}">Fullscreen / Open New Tab</a>@endif
            </div>
            @if($regulation->isPdf() && $regulation->file_path)
                <iframe src="{{ asset('storage/'.$regulation->file_path) }}" style="width:100%;height:640px;border:1px solid #ddd;border-radius:8px"></iframe>
            @elseif($regulation->file_path)
                <pre class="bg-light p-3" style="max-height:640px;overflow:auto">{{ $regulation->extracted_text ?: 'Preview teks belum tersedia.' }}</pre>
            @else
                <p class="text-muted">Belum ada file.</p>
            @endif
        </div>
        @if($regulation->summary)
            <div class="cat-card p-4 mt-3"><h5>Ringkasan</h5><pre class="mb-0">{{ $regulation->summary }}</pre></div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Bank Regulasi</h1>
    <form method="post" action="{{ route('admin.regulations.download-all-pdfs') }}">@csrf
        <button class="btn btn-primary">Download PDF Massal</button>
    </form>
</div>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.regulations.store') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-5"><input class="form-control" name="title" placeholder="Judul regulasi" required></div>
        <div class="col-md-3"><input class="form-control" name="regulation_number" placeholder="Nomor regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="year" type="number" placeholder="Tahun"></div>
        <div class="col-md-2"><input class="form-control" name="status" placeholder="Status regulasi" value="Berlaku"></div>
        <div class="col-md-4"><input class="form-control" name="category" list="regulation-categories" placeholder="Kategori regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="priority" placeholder="Prioritas"></div>
        <div class="col-md-3"><input class="form-control" name="official_url" type="url" placeholder="URL resmi"></div>
        <div class="col-md-3"><input class="form-control" name="pdf_url" type="url" placeholder="URL PDF resmi"></div>
        <div class="col-md-6"><input class="form-control" type="file" name="file" accept=".pdf,.docx,.txt"></div>
        <div class="col-md-6"><textarea class="form-control" name="description" placeholder="Deskripsi"></textarea></div>
        <div class="col-md-6"><textarea class="form-control" name="usage_notes" placeholder="Catatan penggunaan untuk soal"></textarea></div>
        <div class="col-md-6 form-check d-flex align-items-center gap-2 ps-4">
            <input class="form-check-input" type="checkbox" name="can_download_by_participant" value="1" id="can-download">
            <label class="form-check-label" for="can-download">Peserta boleh download file regulasi</label>
        </div>
        <div class="col-md-6 form-check d-flex align-items-center gap-2 ps-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is-active" checked>
            <label class="form-check-label" for="is-active">Regulasi aktif untuk peserta</label>
        </div>
        <div class="col-12"><button class="btn btn-navy">Upload Regulasi</button></div>
    </form>
    <datalist id="regulation-categories">
        @foreach(\App\Support\AsnCatalog::regulationCategories() as $category)
            <option value="{{ $category }}"></option>
        @endforeach
    </datalist>
</div>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Judul</th><th>Nomor/Tahun</th><th>Kategori</th><th>Prioritas/Status</th><th>Status PDF</th><th>Extract/OCR</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($regulations as $regulation)
            <tr>
                <td>
                    <strong>{{ $regulation->title }}</strong><br>
                    <p class="small text-muted mb-0">{{ $regulation->description }}</p>
                </td>
                <td><small>{{ $regulation->regulation_number }}<br>{{ $regulation->year }}</small></td>
                <td><small>{{ $regulation->category }}</small></td>
                <td><small><strong>{{ $regulation->priority }}</strong><br>{{ $regulation->status }}<br>{{ $regulation->is_active ? 'Aktif' : 'Nonaktif' }}</small></td>
                <td>
                    @if($regulation->download_status === 'downloaded' || $regulation->file_path)
                        <span class="badge bg-success">PDF Tersedia</span>
                    @elseif($regulation->download_status === 'failed')
                        <span class="badge bg-danger">Download Gagal</span>
                    @elseif($regulation->download_status === 'pending')
                        <span class="badge bg-warning text-dark">Belum Download</span>
                    @else
                        <span class="badge bg-secondary">Upload Manual</span>
                    @endif
                    @if($regulation->download_error)<div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($regulation->download_error, 80) }}</div>@endif
                </td>
                <td>
                    @if($regulation->extracted_text)<span class="badge bg-success">Teks tersedia</span>@endif
                    @if($regulation->extraction_status === 'need_ocr')<span class="badge bg-warning text-dark">Perlu OCR</span>@endif
                    @if($regulation->extraction_status === 'ocr_completed')<span class="badge bg-info text-dark">OCR selesai</span>@endif
                    @if($regulation->extracted_text)<span class="badge bg-success">Siap generate soal</span>@endif
                    @if($regulation->generatedQuestions->count())<span class="badge bg-secondary">Draft soal tersedia</span>@endif
                </td>
                <td>
                    <a class="btn btn-sm btn-navy" href="{{ route('admin.regulations.show',$regulation) }}">Detail</a>
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.download-pdf',$regulation) }}">@csrf<button class="btn btn-sm btn-info">Download PDF dari Internet</button></form>
                    @if($regulation->file_path)
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.regulations.preview',$regulation) }}">Preview PDF</a>
                        <a class="btn btn-sm btn-secondary" href="{{ route('admin.regulations.download',$regulation) }}">Download File</a>
                    @else
                        <button class="btn btn-sm btn-outline-secondary" disabled>Preview PDF</button>
                    @endif
                    @if(!$regulation->file_path || $regulation->download_status === 'failed')<a class="btn btn-sm btn-outline-dark" href="{{ route('admin.regulations.show',$regulation) }}">Upload Manual</a>@endif
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.extract-text',$regulation) }}">@csrf<button class="btn btn-sm btn-warning">Extract Text</button></form>
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.ocr',$regulation) }}">@csrf<button class="btn btn-sm btn-outline-warning">OCR</button></form>
                    <a class="btn btn-sm btn-success" href="{{ route('admin.regulations.generate-questions',$regulation) }}">Generate Soal</a>
                    <a class="btn btn-sm btn-secondary" href="{{ route('admin.regulations.text',$regulation) }}">Teks</a>
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.destroy',$regulation) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $regulations->links() }}
</div>
@endsection

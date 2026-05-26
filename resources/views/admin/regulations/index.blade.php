@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Bank Regulasi</h1>
    <form method="post" action="{{ route('admin.regulations.download-all-pdfs') }}">@csrf
        <button class="btn btn-primary">Download PDF Massal</button>
    </form>
</div>
<div class="alert alert-info cat-card border-0 mb-4">
    Bisa simpan regulasi dari <strong>link resmi</strong>, <strong>link PDF langsung</strong>, atau <strong>upload file PDF/DOCX/TXT</strong>.
    Kalau Anda isi <code>URL PDF resmi</code>, aktifkan opsi <strong>unduh otomatis</strong> supaya file langsung masuk ke sistem.
</div>
<div class="cat-card p-3 mb-4">
    <form method="get" action="{{ route('admin.regulations.index') }}" class="row g-2 align-items-center">
        <div class="col-md-10">
            <input
                class="form-control"
                type="search"
                name="q"
                value="{{ $search ?? request('q') }}"
                placeholder="Cari judul, nomor, kategori, URL, prioritas, atau catatan regulasi..."
            >
        </div>
        <div class="col-md-2 d-grid gap-2 d-md-flex">
            <button class="btn btn-navy flex-fill" type="submit">Cari</button>
            @if(!empty($search ?? request('q')))
                <a class="btn btn-secondary flex-fill" href="{{ route('admin.regulations.index') }}">Reset</a>
            @endif
        </div>
    </form>
</div>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.regulations.store') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-5"><input class="form-control" name="title" placeholder="Judul regulasi" required></div>
        <div class="col-md-3"><input class="form-control" name="regulation_number" placeholder="Nomor regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="year" type="number" placeholder="Tahun"></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select></div>
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
            <input class="form-check-input" type="checkbox" name="auto_download_pdf" value="1" id="auto-download-pdf" checked>
            <label class="form-check-label" for="auto-download-pdf">Unduh PDF otomatis dari URL PDF</label>
        </div>
        <div class="col-12 d-flex gap-2 flex-wrap">
            <button class="btn btn-navy" name="action" value="save">Simpan Regulasi</button>
            <button class="btn btn-primary" name="action" value="save_download">Simpan & Unduh PDF URL</button>
        </div>
    </form>
    <datalist id="regulation-categories">
        @foreach(\App\Support\AsnCatalog::regulationCategories() as $category)
            <option value="{{ $category }}"></option>
        @endforeach
    </datalist>
</div>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Regulasi</th><th>Badge</th><th>Teks</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($regulations as $regulation)
            <tr>
                <td>
                    <strong>{{ $regulation->title }}</strong><br>
                    <small>{{ $regulation->regulation_number }} {{ $regulation->year }} | {{ $regulation->category }}</small>
                    <p class="small text-muted mb-0">{{ $regulation->description }}</p>
                </td>
                <td>
                    @if($regulation->file_path)<span class="badge bg-primary">PDF tersedia</span>@else<span class="badge bg-secondary">PDF belum tersedia</span>@endif
                    @if($regulation->download_status === 'failed')<span class="badge bg-danger">Download gagal</span>@endif
                    @if($regulation->download_status === 'manual_required')<span class="badge bg-warning text-dark">Upload manual</span>@endif
                    @if($regulation->extracted_text)<span class="badge bg-success">Teks tersedia</span>@endif
                    @if($regulation->extraction_status === 'need_ocr')<span class="badge bg-warning text-dark">Perlu OCR</span>@endif
                    @if($regulation->extraction_status === 'ocr_completed')<span class="badge bg-info text-dark">OCR selesai</span>@endif
                    @if($regulation->extracted_text)<span class="badge bg-success">Siap generate soal</span>@endif
                    @if($regulation->generatedQuestions->count())<span class="badge bg-secondary">Draft soal tersedia</span>@endif
                </td>
                <td>{{ \Illuminate\Support\Str::limit($regulation->extracted_text, 110) }}</td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-navy" href="{{ route('admin.regulations.show',$regulation) }}">Detail</a>
                    @if($regulation->file_path)<a class="btn btn-sm btn-primary" href="{{ route('admin.regulations.preview',$regulation) }}">Preview</a>@endif
                    @if($regulation->pdf_url)<form class="d-inline" method="post" action="{{ route('admin.regulations.download-pdf',$regulation) }}">@csrf<button class="btn btn-sm btn-info">Download URL</button></form>@endif
                    <a class="btn btn-sm btn-secondary" href="{{ route('admin.regulations.text',$regulation) }}">Teks</a>
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.destroy',$regulation) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    Tidak ada regulasi yang cocok dengan pencarian ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{ $regulations->links() }}
</div>
@endsection

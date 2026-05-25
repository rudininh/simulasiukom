@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Bank Regulasi</h1>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.regulations.store') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-5"><input class="form-control" name="title" placeholder="Judul regulasi" required></div>
        <div class="col-md-3"><input class="form-control" name="regulation_number" placeholder="Nomor regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="year" type="number" placeholder="Tahun"></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select></div>
        <div class="col-md-4"><input class="form-control" name="category" list="regulation-categories" placeholder="Kategori regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="priority" placeholder="Prioritas"></div>
        <div class="col-md-6"><input class="form-control" type="file" name="file" accept=".pdf,.docx,.txt"></div>
        <div class="col-md-6"><textarea class="form-control" name="description" placeholder="Deskripsi"></textarea></div>
        <div class="col-md-6"><textarea class="form-control" name="usage_notes" placeholder="Catatan penggunaan untuk soal"></textarea></div>
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
        <thead><tr><th>Regulasi</th><th>Badge</th><th>Teks</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($regulations as $regulation)
            <tr>
                <td>
                    <strong>{{ $regulation->title }}</strong><br>
                    <small>{{ $regulation->regulation_number }} {{ $regulation->year }} | {{ $regulation->category }}</small>
                    <p class="small text-muted mb-0">{{ $regulation->description }}</p>
                </td>
                <td>
                    @if($regulation->file_path)<span class="badge bg-primary">PDF/File tersedia</span>@endif
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

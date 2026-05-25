@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Bank Regulasi</h1>
<div class="cat-card p-3 mb-4">
    <form method="post" action="{{ route('admin.regulations.store') }}" enctype="multipart/form-data" class="row g-2">@csrf
        <div class="col-md-5"><input class="form-control" name="title" placeholder="Judul regulasi" required></div>
        <div class="col-md-3"><input class="form-control" name="regulation_number" placeholder="Nomor regulasi"></div>
        <div class="col-md-2"><input class="form-control" name="year" type="number" placeholder="Tahun"></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select></div>
        <div class="col-md-8"><textarea class="form-control" name="description" placeholder="Deskripsi"></textarea></div>
        <div class="col-md-4"><input class="form-control" type="file" name="file" accept=".pdf,.docx,.txt"></div>
        <div class="col-12"><button class="btn btn-navy">Upload Regulasi</button></div>
    </form>
</div>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Regulasi</th><th>File</th><th>Status</th><th>Teks</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($regulations as $regulation)
            <tr>
                <td><strong>{{ $regulation->title }}</strong><br><small>{{ $regulation->regulation_number }} {{ $regulation->year }}</small><p class="small text-muted">{{ $regulation->description }}</p></td>
                <td>@if($regulation->file_path)<a href="{{ asset('storage/'.$regulation->file_path) }}" target="_blank">Lihat file</a>@else - @endif</td>
                <td><span class="badge {{ $regulation->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $regulation->status }}</span></td>
                <td>{{ \Illuminate\Support\Str::limit($regulation->extracted_text, 90) }}</td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-primary" href="{{ route('admin.regulations.extract',$regulation) }}">Ekstrak</a>
                    <form class="d-inline" method="post" action="{{ route('admin.regulations.destroy',$regulation) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $regulations->links() }}
</div>
@endsection

@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Draft Soal AI</h1>
<div class="cat-card p-3 mb-3">
    <form class="row g-2">
        <div class="col-md-2"><select class="form-select" name="regulation_id"><option value="">Regulasi</option>@foreach($regulations as $regulation)<option value="{{ $regulation->id }}" @selected(request('regulation_id')==$regulation->id)>{{ $regulation->title }}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-select" name="exam_id"><option value="">Ujian</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected(request('exam_id')==$exam->id)>{{ $exam->title }}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-select" name="exam_category_id"><option value="">Kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('exam_category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-1"><select class="form-select" name="difficulty"><option value="">Sulit</option>@foreach(['easy','medium','hard','case','calculation'] as $difficulty)<option @selected(request('difficulty')===$difficulty)>{{ $difficulty }}</option>@endforeach</select></div>
        <div class="col-md-1"><select class="form-select" name="status"><option value="">Status</option>@foreach(['draft','approved','rejected'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-select" name="validation_status"><option value="">Validasi</option>@foreach(['valid','warning','invalid'] as $status)<option @selected(request('validation_status')===$status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Cari soal"></div>
        <div class="col-12"><button class="btn btn-navy">Filter</button></div>
    </form>
</div>
<form method="post" action="{{ route('admin.generated-questions.bulk') }}">@csrf
<div class="cat-card p-3 mb-3 d-flex gap-2 flex-wrap">
    <select class="form-select" name="action" style="max-width:220px"><option value="approve">Approve selected</option><option value="reject">Reject selected</option><option value="delete">Delete selected</option><option value="assign">Assign to exam/category</option></select>
    <select class="form-select" name="exam_id" style="max-width:260px"><option value="">Pilih ujian</option>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->title }}</option>@endforeach</select>
    <select class="form-select" name="exam_category_id" style="max-width:260px"><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
    <button class="btn btn-success">Proses Bulk</button>
</div>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th><input type="checkbox" onclick="document.querySelectorAll('.draft-check').forEach(el=>el.checked=this.checked)"></th><th>Pertanyaan</th><th>Kategori</th><th>Regulasi Sumber</th><th>Rujukan</th><th>Kesulitan</th><th>Tipe</th><th>Validasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($drafts as $draft)
            <tr>
                <td><input class="draft-check" type="checkbox" name="ids[]" value="{{ $draft->id }}"></td>
                <td><strong>{{ \Illuminate\Support\Str::limit($draft->question_text, 130) }}</strong><br><small>Jawaban: {{ $draft->correct_answer }}</small></td>
                <td>{{ $draft->category->name }} @if($draft->category->code === 'PERKAWINAN_PERCERAIAN_ASN')<span class="badge bg-warning text-dark">Izin Perkawinan/Perceraian ASN</span>@endif</td>
                <td>{{ $draft->regulation->title }}</td>
                <td>{{ $draft->source_reference }} @if($draft->source_page) / Hal. {{ $draft->source_page }} @endif</td>
                <td>{{ $draft->difficulty }}</td>
                <td>{{ $draft->question_type }}</td>
                <td><span class="badge bg-{{ $draft->validation_status === 'valid' ? 'success' : ($draft->validation_status === 'warning' ? 'warning text-dark' : 'danger') }}">{{ $draft->validation_status ?: '-' }}</span><br><small>{{ $draft->validation_notes }}</small></td>
                <td><span class="badge bg-secondary">{{ $draft->status }}</span></td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-primary" href="{{ route('admin.generated-questions.edit',$draft) }}">Edit</a>
                    @if($draft->status === 'draft')
                        <form class="d-inline" method="post" action="{{ route('admin.generated-questions.approve',$draft) }}">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
                        <form class="d-inline" method="post" action="{{ route('admin.generated-questions.reject',$draft) }}">@csrf<button class="btn btn-sm btn-danger">Reject</button></form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $drafts->links() }}
</div>
</form>
@endsection

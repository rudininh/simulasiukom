@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Draft Soal AI</h1>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Soal</th><th>Ujian</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($drafts as $draft)
            <tr>
                <td><strong>{{ \Illuminate\Support\Str::limit($draft->question_text, 120) }}</strong><br><small>Jawaban: {{ $draft->correct_answer }} | {{ $draft->source_reference }}</small></td>
                <td>{{ $draft->exam->title }}</td>
                <td>{{ $draft->category->name }}</td>
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
@endsection

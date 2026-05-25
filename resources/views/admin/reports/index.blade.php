@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Rekap Nilai</h1>
<div class="cat-card p-3 table-responsive">
    <table class="table"><thead><tr><th>Peserta</th><th>Ujian</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>
        @foreach($attempts as $attempt)<tr><td>{{ $attempt->user->name }}</td><td>{{ $attempt->exam->title }}</td><td>{{ $attempt->score_total }}</td><td>{{ $attempt->competency_status }}</td><td>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</td></tr>@endforeach
    </tbody></table>
</div>
@endsection

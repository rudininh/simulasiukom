@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Hasil Ujian</h1>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Peserta</th><th>Ujian</th><th>Mulai</th><th>Selesai</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach($attempts as $attempt)<tr><td>{{ $attempt->user->name }}</td><td>{{ $attempt->exam->title }}</td><td>{{ $attempt->started_at->format('d/m/Y H:i') }}</td><td>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</td><td>{{ $attempt->score_total }}</td><td>{{ $attempt->competency_status }}</td><td><a class="btn btn-sm btn-navy mb-1" href="{{ route('admin.results.show',$attempt) }}">Detail</a><form method="post" action="{{ route('admin.results.reset',$attempt) }}">@csrf<button class="btn btn-sm btn-warning">Reset</button></form></td></tr>@endforeach
</tbody></table>{{ $attempts->links() }}</div>
@endsection

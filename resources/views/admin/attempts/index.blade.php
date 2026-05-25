@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Rekap Hasil Ujian</h1>
<div class="cat-card p-3 table-responsive"><table class="table align-middle"><thead><tr><th>Peserta</th><th>Ujian</th><th>Mulai</th><th>Selesai</th><th>TWK</th><th>TIU</th><th>TKP</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach($attempts as $attempt)<tr><td>{{ $attempt->user->name }}</td><td>{{ $attempt->exam->title }}</td><td>{{ $attempt->started_at->format('d/m/Y H:i') }}</td><td>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</td><td>{{ $attempt->score_twk }}</td><td>{{ $attempt->score_tiu }}</td><td>{{ $attempt->score_tkp }}</td><td>{{ $attempt->score_total }}</td><td>{{ $attempt->status }}</td><td><form method="post" action="{{ route('admin.attempts.reset',$attempt) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-warning">Reset</button></form></td></tr>@endforeach
</tbody></table>{{ $attempts->links() }}</div>
@endsection

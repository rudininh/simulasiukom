@extends('layouts.app')
@section('content')
<h1 class="page-title mb-4"><i class="fa-solid fa-clock-rotate-left me-3"></i>Riwayat Ujian</h1>
<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead><tr><th>No</th><th>Nama Ujian</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Skor TWK</th><th>Skor TIU</th><th>Skor TKP</th><th>Total Skor</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($attempts as $attempt)
            <tr>
                <td>{{ $loop->iteration }}</td><td>{{ $attempt->exam->title }}</td><td>{{ $attempt->started_at->format('d/m/Y H:i') }}</td><td>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $attempt->score_twk }}</td><td>{{ $attempt->score_tiu }}</td><td>{{ $attempt->score_tkp }}</td><td>{{ $attempt->score_total }}</td>
                <td><span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">{{ $attempt->is_passed ? 'LULUS' : 'TIDAK LULUS' }}</span></td>
                <td><a class="btn btn-sm btn-navy" href="{{ route('results.show', $attempt) }}">Detail</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

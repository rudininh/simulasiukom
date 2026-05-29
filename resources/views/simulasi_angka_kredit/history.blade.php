@extends('layouts.app')
@section('content')
@php
    $fmt = fn ($value) => $value === null ? '-' : rtrim(rtrim(number_format((float) $value, 4, ',', '.'), '0'), ',');
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h1 class="page-title mb-1"><i class="fa-solid fa-clock-rotate-left me-3"></i>Riwayat Simulasi Angka Kredit</h1>
        <p class="page-subtitle mb-0">Daftar hasil simulasi yang pernah Anda simpan.</p>
    </div>
    <a class="btn btn-navy" href="{{ route('angka-kredit.index') }}"><i class="fa-solid fa-plus me-2"></i>Simulasi Baru</a>
</div>

<div class="cat-card p-3 table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Tanggal Simulasi</th>
                <th>Jenis Simulasi</th>
                <th>Jabatan Tujuan</th>
                <th>Golongan</th>
                <th>Total AK</th>
                <th>Status KP</th>
                <th>Status KJ</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($simulations as $simulation)
            <tr>
                <td>{{ optional($simulation->created_at)->format('d/m/Y H:i') }}</td>
                <td><small>{{ $simulation->jenis_simulasi }}</small></td>
                <td>{{ $simulation->jabatan_tujuan ?: '-' }}</td>
                <td>{{ $simulation->golongan_ruang ?: '-' }}</td>
                <td class="fw-bold">{{ $fmt($simulation->total_ak) }}</td>
                <td><small>{{ $simulation->status_kp ?: '-' }}</small></td>
                <td><small>{{ $simulation->status_kj ?: '-' }}</small></td>
                <td>
                    <a class="btn btn-sm btn-navy" href="{{ route('angka-kredit.show', $simulation) }}">Detail</a>
                    <form class="d-inline" method="post" action="{{ route('angka-kredit.destroy', $simulation) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus riwayat simulasi ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada hasil simulasi yang disimpan.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $simulations->links() }}
</div>
@endsection

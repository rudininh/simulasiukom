@extends('layouts.app')
@section('content')
@php
    $statusAsn = ['PNS', 'PPPK'];
    $jenisJabatan = ['Pelaksana','Jabatan Administrasi - Pengawas','Jabatan Administrasi - Administrator','JPT','Jabatan Fungsional'];
    $kategoriJf = ['Keahlian','Keterampilan','Tidak Ada'];
    $jenjangJf = ['Pemula','Terampil','Mahir','Penyelia','Ahli Pertama','Ahli Muda','Ahli Madya','Ahli Utama','Tidak Ada'];
    $golongans = ['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','III/a','III/b','III/c','III/d','IV/a','IV/b','IV/c','IV/d','IV/e'];
    $pval = fn ($key, $default = null) => old($key, $profile?->{$key} ?? $default);
@endphp
<h1 class="page-title mb-4"><i class="fa-solid fa-user-pen me-3"></i>Profil Peserta</h1>
<div class="cat-card p-4">
    <form method="post" action="{{ route('profile.update') }}" class="row g-3">
        @csrf @method('PUT')
        <div class="col-12"><h2 class="h5 fw-bold text-primary-emphasis mb-0">Akun</h2></div>
        <div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required></div>
        <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" value="{{ old('username', auth()->user()->username) }}" required></div>
        <div class="col-md-6"><label class="form-label">Nomor HP</label><input class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}"></div>
        <div class="col-md-6"><label class="form-label">Instansi</label><input class="form-control" name="institution" value="{{ old('institution', auth()->user()->institution) }}"></div>
        <div class="col-md-6"><label class="form-label">Nama Jabatan</label><input class="form-control" name="position_name" value="{{ old('position_name', auth()->user()->position_name) }}"></div>
        <div class="col-md-6"><label class="form-label">Unit Kerja</label><input class="form-control" name="work_unit" value="{{ old('work_unit', auth()->user()->work_unit) }}"></div>
        <div class="col-md-6"><label class="form-label">NIP/Nomor Pegawai</label><input class="form-control" name="employee_number" value="{{ old('employee_number', auth()->user()->employee_number) }}"></div>
        <div class="col-md-6"><label class="form-label">Password Baru Opsional</label><input class="form-control" type="password" name="password"></div>
        <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input class="form-control" type="password" name="password_confirmation"></div>

        <div class="col-12 mt-4"><h2 class="h5 fw-bold text-primary-emphasis mb-0">Data Kepegawaian</h2></div>
        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="nama_lengkap" value="{{ $pval('nama_lengkap', auth()->user()->name) }}"></div>
        <div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" name="nip" value="{{ $pval('nip', auth()->user()->employee_number) }}"></div>
        <div class="col-md-4"><label class="form-label">Tanggal Lahir</label><input class="form-control" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($profile?->tanggal_lahir)->format('Y-m-d')) }}"></div>
        <div class="col-md-4">
            <label class="form-label">Status ASN</label>
            <select class="form-select" name="status_asn"><option value="">Pilih</option>@foreach($statusAsn as $item)<option @selected($pval('status_asn') === $item)>{{ $item }}</option>@endforeach</select>
        </div>
        <div class="col-md-4"><label class="form-label">Golongan Ruang</label><select class="form-select" name="golongan_ruang"><option value="">Pilih</option>@foreach($golongans as $item)<option @selected($pval('golongan_ruang') === $item)>{{ $item }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Pendidikan Terakhir</label><input class="form-control" name="pendidikan_terakhir" value="{{ $pval('pendidikan_terakhir') }}"></div>
        <div class="col-md-6"><label class="form-label">Jurusan Pendidikan</label><input class="form-control" name="jurusan_pendidikan" value="{{ $pval('jurusan_pendidikan') }}"></div>
        <div class="col-md-6"><label class="form-label">Jabatan Saat Ini</label><input class="form-control" name="jabatan_saat_ini" value="{{ $pval('jabatan_saat_ini', auth()->user()->position_name) }}"></div>
        <div class="col-md-6"><label class="form-label">Jenis Jabatan Saat Ini</label><select class="form-select" name="jenis_jabatan_saat_ini"><option value="">Pilih</option>@foreach($jenisJabatan as $item)<option @selected($pval('jenis_jabatan_saat_ini') === $item)>{{ $item }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Kategori Jabatan Fungsional</label><select class="form-select" name="kategori_jabatan_fungsional"><option value="">Pilih</option>@foreach($kategoriJf as $item)<option @selected($pval('kategori_jabatan_fungsional') === $item)>{{ $item }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Jenjang Jabatan Saat Ini</label><select class="form-select" name="jenjang_jabatan_saat_ini"><option value="">Pilih</option>@foreach($jenjangJf as $item)<option @selected($pval('jenjang_jabatan_saat_ini') === $item)>{{ $item }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">TMT Pangkat Terakhir</label><input class="form-control" type="date" name="tmt_pangkat_terakhir" value="{{ old('tmt_pangkat_terakhir', optional($profile?->tmt_pangkat_terakhir)->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label">TMT Jabatan Terakhir</label><input class="form-control" type="date" name="tmt_jabatan_terakhir" value="{{ old('tmt_jabatan_terakhir', optional($profile?->tmt_jabatan_terakhir)->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label">Angka Kredit Terakhir</label><input class="form-control" type="number" step="0.0001" min="0" name="angka_kredit_terakhir" value="{{ $pval('angka_kredit_terakhir') }}"></div>
        <div class="col-md-6"><label class="form-label">Unit Kerja</label><input class="form-control" name="unit_kerja" value="{{ $pval('unit_kerja', auth()->user()->work_unit) }}"></div>
        <div class="col-md-6"><label class="form-label">Instansi</label><input class="form-control" name="instansi" value="{{ $pval('instansi', auth()->user()->institution) }}"></div>
        <div class="col-12 d-flex gap-2 flex-wrap">
            <button class="btn btn-navy px-4">Simpan Profil</button>
            <a class="btn btn-outline-primary" href="{{ route('angka-kredit.index') }}">Mulai Simulasi Angka Kredit</a>
        </div>
    </form>
</div>
@endsection

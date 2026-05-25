@extends('layouts.app')
@section('content')
<h1 class="page-title mb-4"><i class="fa-solid fa-user-pen me-3"></i>Profil Peserta</h1>
<div class="cat-card p-4">
    <form method="post" action="{{ route('profile.update') }}" class="row g-3">
        @csrf @method('PUT')
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
        <div class="col-12"><button class="btn btn-navy px-4">Simpan Profil</button></div>
    </form>
</div>
@endsection

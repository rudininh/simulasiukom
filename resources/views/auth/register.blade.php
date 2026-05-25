@extends('layouts.auth')
@section('content')
<div class="login-card shadow-lg">
    <div class="text-center mb-4">
        <div class="brand-logo mx-auto mb-3">CAT</div>
        <h1 class="h4 fw-bold text-primary-emphasis">DAFTAR SIMULASI CAT MANAJEMEN ASN</h1>
        <p class="text-muted small">Lengkapi data peserta untuk membuat akun</p>
    </div>
    @if ($errors->any()) <div class="alert alert-danger py-2">{{ $errors->first() }}</div> @endif
    <form method="post" action="{{ route('register') }}">
        @csrf
        <input class="form-control mb-3" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
        <input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
        <input class="form-control mb-3" name="username" value="{{ old('username') }}" placeholder="Username" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
        <input class="form-control mb-3" type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
        <input class="form-control mb-3" name="phone" value="{{ old('phone') }}" placeholder="Nomor HP opsional">
        <input class="form-control mb-4" name="institution" value="{{ old('institution') }}" placeholder="Instansi opsional">
        <input class="form-control mb-3" name="position_name" value="{{ old('position_name') }}" placeholder="Nama jabatan opsional">
        <input class="form-control mb-3" name="work_unit" value="{{ old('work_unit') }}" placeholder="Unit kerja opsional">
        <input class="form-control mb-4" name="employee_number" value="{{ old('employee_number') }}" placeholder="NIP/nomor pegawai opsional">
        <button class="btn btn-success w-100 fw-bold">Daftar</button>
        <a href="{{ route('login') }}" class="btn btn-link w-100 mt-2">Sudah punya akun? Masuk</a>
    </form>
</div>
@endsection

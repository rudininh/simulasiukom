@extends('layouts.auth')
@section('content')
<div class="login-card shadow-lg">
    <div class="text-center mb-4">
        <div class="brand-logo mx-auto mb-3" style="width:96px;height:96px;">CAT</div>
        <h1 class="h4 fw-bold text-primary-emphasis">SIMULASI CAT UJI KOMPETENSI MANAJEMEN ASN</h1>
        <p class="text-muted small">{{ $quickLogin ? 'Silakan pilih mode akses aplikasi' : 'Silakan masukkan username dan password Anda' }}</p>
    </div>
    @if ($errors->any()) <div class="alert alert-danger py-2">{{ $errors->first() }}</div> @endif
    @if ($quickLogin)
        <div class="d-grid gap-3">
            <form method="post" action="{{ route('quick-login', 'admin') }}">
                @csrf
                <button class="btn btn-navy btn-lg w-100 py-3"><i class="fa-solid fa-user-shield me-2"></i>LOGIN SEBAGAI ADMIN</button>
            </form>
            <form method="post" action="{{ route('quick-login', 'peserta') }}">
                @csrf
                <button class="btn btn-success btn-lg w-100 py-3"><i class="fa-solid fa-circle-user me-2"></i>LOGIN SEBAGAI USER</button>
            </form>
        </div>
    @else
    <form method="post" action="{{ url('/login') }}">
        @csrf
        <label class="form-label fw-bold small"><i class="fa-solid fa-user me-2 text-primary-emphasis"></i> Username / Email</label>
        <input class="form-control form-control-lg mb-3" name="login" value="{{ old('login') }}" placeholder="Masukkan username atau email" required>
        <label class="form-label fw-bold small"><i class="fa-solid fa-lock me-2 text-primary-emphasis"></i> Password</label>
        <div class="input-group input-group-lg mb-3">
            <input id="password" type="password" class="form-control" name="password" placeholder="Masukkan password" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')"><i class="fa-solid fa-eye"></i></button>
        </div>
        <label class="form-label fw-bold small"><i class="fa-solid fa-shield-halved me-2 text-primary-emphasis"></i> Captcha</label>
        <div class="d-flex align-items-center gap-2 mb-3">
            <span id="captcha-code" class="captcha-code">{{ session('captcha_code') }}</span>
            <button class="btn btn-navy" type="button" onclick="refreshCaptcha()"><i class="fa-solid fa-arrows-rotate"></i></button>
            <input class="form-control fw-bold text-uppercase" name="captcha" placeholder="MASUKKAN CAPTCHA" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 small">
            <label><input type="checkbox" name="remember" value="1" class="me-2">Ingat saya</label>
            <a class="text-primary-emphasis fw-bold text-decoration-none" href="#">Lupa password?</a>
        </div>
        <button class="btn btn-navy btn-lg w-100 mb-4"><i class="fa-solid fa-right-to-bracket me-2"></i>MASUK</button>
        <div class="divider mb-4">atau</div>
        <a href="{{ route('register') }}" class="btn btn-success btn-lg w-100"><i class="fa-solid fa-user-plus me-2"></i>DAFTAR SIMULASI</a>
        <p class="text-center small text-muted mt-3 mb-0">Belum punya akun? Daftar sekarang untuk mengikuti Simulasi CAT Manajemen ASN</p>
    </form>
    @endif
</div>
@endsection

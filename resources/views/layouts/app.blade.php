<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIMULASI CAT BKN v2.0' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/cat.css') }}" rel="stylesheet">
</head>
<body>
<div class="top-strip"></div>
<header class="cat-header d-flex align-items-center">
    <div class="cat-container d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-logo">CAT</div>
            <div>
                <div class="brand-title">SIMULASI CAT BKN v2.0</div>
                <div class="brand-subtitle">Pusat Pengembangan Sistem Seleksi</div>
            </div>
        </div>
        @auth
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div><i class="fa-solid fa-circle-user fs-4 me-2"></i> Hello, <strong>{{ auth()->user()->name }}</strong></div>
                <a class="header-btn" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a>
                <form method="post" action="{{ route('logout') }}">@csrf
                    <button class="header-btn"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                </form>
            </div>
        @endauth
    </div>
</header>
<main class="page-wrap">
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

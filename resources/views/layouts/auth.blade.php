<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login CAT BKN' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/cat.css') }}" rel="stylesheet">
</head>
<body>
<div class="top-strip"></div>
<main class="login-page">@yield('content')</main>
<script>
function togglePassword(id){ const input=document.getElementById(id); input.type=input.type==='password'?'text':'password'; }
async function refreshCaptcha(){ const response=await fetch('{{ route('captcha.refresh') }}',{headers:{Accept:'application/json'}}); const data=await response.json(); document.getElementById('captcha-code').textContent=data.code; }
</script>
</body>
</html>

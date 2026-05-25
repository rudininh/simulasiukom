<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Hasil Uji Kompetensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{font-family:Arial,sans-serif}.print-card{max-width:850px;margin:30px auto;border:1px solid #ddd;padding:32px}@media print{.no-print{display:none}}</style>
</head>
<body>
<div class="print-card">
    <h2>SIMULASI CAT UJI KOMPETENSI MANAJEMEN ASN</h2>
    <p class="text-muted">Hasil Simulasi Uji Kompetensi</p>
    <hr>
    <table class="table">
        <tr><th>Nama Peserta</th><td>{{ $attempt->user->name }}</td></tr>
        <tr><th>Nama Ujian</th><td>{{ $attempt->exam->title }}</td></tr>
        <tr><th>Tanggal Ujian</th><td>{{ $attempt->started_at->format('d/m/Y H:i') }}</td></tr>
        <tr><th>Skor Total</th><td>{{ $attempt->score_total }}</td></tr>
        <tr><th>Regulasi ASN</th><td>{{ $attempt->score_regulasi_asn }}</td></tr>
        <tr><th>Manajemen ASN</th><td>{{ $attempt->score_manajemen_asn }}</td></tr>
        <tr><th>Kepemimpinan dan Manajerial</th><td>{{ $attempt->score_kepemimpinan }}</td></tr>
        <tr><th>Pelayanan Publik dan Etika Birokrasi</th><td>{{ $attempt->score_pelayanan_publik }}</td></tr>
        <tr><th>Studi Kasus Manajemen ASN</th><td>{{ $attempt->score_studi_kasus }}</td></tr>
        <tr><th>Status Kompetensi</th><td>{{ strtoupper(str_replace('_', ' ', $attempt->competency_status)) }}</td></tr>
        <tr><th>Passing Grade</th><td>{{ number_format($attempt->exam->passing_grade, 2) }}</td></tr>
    </table>
    <button class="btn btn-primary no-print" onclick="window.print()">Cetak / Simpan PDF</button>
</div>
</body>
</html>

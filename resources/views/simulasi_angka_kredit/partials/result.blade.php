@php
    $fmt = fn ($value) => $value === null ? '-' : rtrim(rtrim(number_format((float) $value, 4, ',', '.'), '0'), ',');
    $selisihText = function ($value) use ($fmt) {
        if ($value === null) {
            return '-';
        }

        return ((float) $value >= 0 ? 'Kelebihan ' : 'Kekurangan ').$fmt(abs((float) $value));
    };
@endphp

<div class="cat-card p-4 mt-4" id="hasil-simulasi">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
        <div>
            <h2 class="h4 fw-bold text-primary-emphasis mb-1">Hasil Simulasi Angka Kredit</h2>
            <p class="text-muted small mb-0">Rincian estimasi berdasarkan data yang Anda masukkan.</p>
        </div>
        <button class="btn btn-outline-dark" type="button" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Cetak</button>
    </div>

    <div class="alert alert-warning">{{ $result['disclaimer'] }}</div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="border rounded p-3 h-100">
                <h3 class="h6 fw-bold">Identitas Simulasi</h3>
                <dl class="row small mb-0">
                    <dt class="col-5">Nama</dt><dd class="col-7">{{ $result['identitas']['nama'] ?: '-' }}</dd>
                    <dt class="col-5">NIP</dt><dd class="col-7">{{ $result['identitas']['nip'] ?: '-' }}</dd>
                    <dt class="col-5">Jenis</dt><dd class="col-7">{{ $result['identitas']['jenis_simulasi'] ?: '-' }}</dd>
                    <dt class="col-5">Jabatan Asal</dt><dd class="col-7">{{ $result['identitas']['jabatan_asal'] ?: '-' }}</dd>
                    <dt class="col-5">Jabatan Tujuan</dt><dd class="col-7">{{ $result['identitas']['jabatan_tujuan'] ?: '-' }}</dd>
                    <dt class="col-5">Golongan</dt><dd class="col-7">{{ $result['identitas']['golongan_ruang'] ?: '-' }}</dd>
                    <dt class="col-5">Jenjang Tujuan</dt><dd class="col-7">{{ $result['identitas']['jenjang_tujuan'] ?: '-' }}</dd>
                    <dt class="col-5">Tanggal</dt><dd class="col-7">{{ $result['identitas']['tanggal_simulasi'] ?: '-' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border rounded p-3 h-100">
                <h3 class="h6 fw-bold">Rincian Perhitungan</h3>
                <dl class="row small mb-0">
                    <dt class="col-7">Angka Kredit Lama</dt><dd class="col-5 text-end">{{ $fmt($result['angka_kredit_lama']) }}</dd>
                    <dt class="col-7">Angka Kredit Konversi</dt><dd class="col-5 text-end">{{ $fmt($result['angka_kredit_konversi']) }}</dd>
                    <dt class="col-7">Angka Kredit Dasar</dt><dd class="col-5 text-end">{{ $fmt($result['angka_kredit_dasar']) }}</dd>
                    <dt class="col-7">Tambahan AK Ijazah</dt><dd class="col-5 text-end">{{ $fmt($result['tambahan_ak_ijazah']) }}</dd>
                    <dt class="col-7">Total AK Simulasi</dt><dd class="col-5 text-end fw-bold">{{ $fmt($result['total_ak']) }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border rounded p-3 h-100">
                <h3 class="h6 fw-bold">Kenaikan Pangkat</h3>
                <dl class="row small mb-0">
                    <dt class="col-7">Kebutuhan AK KP</dt><dd class="col-5 text-end">{{ $fmt($result['kebutuhan_ak_kenaikan_pangkat']) }}</dd>
                    <dt class="col-7">Kekurangan/Kelebihan</dt><dd class="col-5 text-end">{{ $selisihText($result['selisih_kp']) }}</dd>
                    <dt class="col-7">Status</dt><dd class="col-5 text-end">{{ $result['status_kp'] ?: '-' }}</dd>
                    <dt class="col-7">Estimasi Baik</dt><dd class="col-5 text-end">{{ $result['estimasi_kp']['baik'] ? $result['estimasi_kp']['baik'].' tahun' : '-' }}</dd>
                    <dt class="col-7">Estimasi Sangat Baik</dt><dd class="col-5 text-end">{{ $result['estimasi_kp']['sangat_baik'] ? $result['estimasi_kp']['sangat_baik'].' tahun' : '-' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border rounded p-3 h-100">
                <h3 class="h6 fw-bold">Kenaikan Jenjang</h3>
                <dl class="row small mb-0">
                    <dt class="col-7">Kebutuhan AK KJ</dt><dd class="col-5 text-end">{{ $fmt($result['kebutuhan_ak_kenaikan_jenjang']) }}</dd>
                    <dt class="col-7">Kekurangan/Kelebihan</dt><dd class="col-5 text-end">{{ $selisihText($result['selisih_kj']) }}</dd>
                    <dt class="col-7">Status</dt><dd class="col-5 text-end">{{ $result['status_kj'] ?: '-' }}</dd>
                    <dt class="col-7">Estimasi Baik</dt><dd class="col-5 text-end">{{ $result['estimasi_kj']['baik'] ? $result['estimasi_kj']['baik'].' tahun' : '-' }}</dd>
                    <dt class="col-7">Estimasi Sangat Baik</dt><dd class="col-5 text-end">{{ $result['estimasi_kj']['sangat_baik'] ? $result['estimasi_kj']['sangat_baik'].' tahun' : '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    @if(!empty($result['catatan']))
        <div class="alert alert-info mt-3 mb-0">{{ $result['catatan'] }}</div>
    @endif
</div>

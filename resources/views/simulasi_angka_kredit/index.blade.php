@extends('layouts.app')
@section('content')
@php
    $golongans = ['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','III/a','III/b','III/c','III/d','IV/a','IV/b','IV/c','IV/d','IV/e'];
    $jenisJabatan = ['Pelaksana','Jabatan Administrasi - Pengawas','Jabatan Administrasi - Administrator','JPT','Jabatan Fungsional'];
    $kategoriJf = ['Keahlian','Keterampilan','Tidak Ada'];
    $jenjangJf = ['Pemula','Terampil','Mahir','Penyelia','Ahli Pertama','Ahli Muda','Ahli Madya','Ahli Utama','Tidak Ada'];
    $val = fn ($key, $default = null) => old($key, $data[$key] ?? $default);
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h1 class="page-title mb-1"><i class="fa-solid fa-calculator me-3"></i>Simulasi Angka Kredit</h1>
        <p class="page-subtitle mb-0">Hitung estimasi Angka Kredit Jabatan Fungsional berdasarkan data kepegawaian dan predikat kinerja.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}"><i class="fa-solid fa-arrow-left me-2"></i>Dashboard</a>
        <a class="btn btn-outline-primary" href="{{ route('angka-kredit.history') }}"><i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat</a>
    </div>
</div>

@if($profileIncomplete)
    <div class="alert alert-warning">
        Data profil kepegawaian Anda belum lengkap. Simulasi tetap bisa digunakan, tetapi beberapa field perlu dilengkapi manual.
        <a class="fw-bold" href="{{ route('profile.edit') }}">Lengkapi profil</a>
    </div>
@endif

<form id="ak-form" method="post" action="{{ route('angka-kredit.calculate') }}">
    @csrf
    <div class="cat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
            <h2 class="h5 fw-bold text-primary-emphasis mb-0">Data Profil</h2>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('angka-kredit.index') }}"><i class="fa-solid fa-user-check me-2"></i>Gunakan Data Profil</a>
                <button class="btn btn-sm btn-outline-success" name="action" value="save_profile" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Data Ini ke Profil</button>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="nama_lengkap" value="{{ $val('nama_lengkap') }}"></div>
            <div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" name="nip" value="{{ $val('nip') }}"></div>
        </div>
    </div>

    <div class="cat-card p-4 mb-4">
        <h2 class="h5 fw-bold text-primary-emphasis mb-3">Data Jabatan</h2>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Jabatan Asal</label><input class="form-control" name="jabatan_asal" value="{{ $val('jabatan_asal') }}"></div>
            <div class="col-md-4">
                <label class="form-label">Jenis Jabatan Asal</label>
                <select class="form-select" name="jenis_jabatan_asal">
                    <option value="">Pilih</option>
                    @foreach($jenisJabatan as $item)<option @selected($val('jenis_jabatan_asal') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Jabatan Tujuan</label><input class="form-control" name="jabatan_tujuan" value="{{ $val('jabatan_tujuan') }}"></div>
            <div class="col-md-4">
                <label class="form-label">Kategori JF Tujuan</label>
                <select class="form-select" name="kategori_jf_tujuan" id="kategori-jf">
                    <option value="">Pilih</option>
                    @foreach($kategoriJf as $item)<option @selected($val('kategori_jf_tujuan') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenjang JF Tujuan</label>
                <select class="form-select" name="jenjang_jf_tujuan" id="jenjang-jf">
                    <option value="">Pilih</option>
                    @foreach($jenjangJf as $item)<option @selected($val('jenjang_jf_tujuan') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Golongan Ruang</label>
                <select class="form-select" name="golongan_ruang" required>
                    <option value="">Pilih</option>
                    @foreach($golongans as $item)<option @selected($val('golongan_ruang') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">TMT Pangkat Terakhir</label><input class="form-control" type="date" name="tmt_pangkat_terakhir" id="tmt-pangkat" value="{{ $val('tmt_pangkat_terakhir') }}"></div>
            <div class="col-md-4"><label class="form-label">TMT Jabatan Terakhir</label><input class="form-control" type="date" name="tmt_jabatan_terakhir" value="{{ $val('tmt_jabatan_terakhir') }}"></div>
            <div class="col-md-4"><label class="form-label">Tanggal Simulasi</label><input class="form-control" type="date" name="tanggal_simulasi" id="tanggal-simulasi" value="{{ $val('tanggal_simulasi', now()->toDateString()) }}" required></div>
        </div>
    </div>

    <div class="cat-card p-4 mb-4">
        <h2 class="h5 fw-bold text-primary-emphasis mb-3">Data Simulasi</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Jenis Simulasi</label>
                <select class="form-select" name="jenis_simulasi" required>
                    <option value="">Pilih jenis simulasi</option>
                    @foreach($jenisSimulasi as $item)<option @selected($val('jenis_simulasi') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Masa Pangkat Tahun</label><input class="form-control" type="number" min="0" name="masa_pangkat_tahun" id="masa-tahun" value="{{ $val('masa_pangkat_tahun', 0) }}"></div>
            <div class="col-md-3"><label class="form-label">Masa Pangkat Bulan</label><input class="form-control" type="number" min="0" max="11" name="masa_pangkat_bulan" id="masa-bulan" value="{{ $val('masa_pangkat_bulan', 0) }}"></div>
            <div class="col-12"><div class="small text-muted" id="masa-pangkat-info"></div></div>
            <div class="col-md-4">
                <label class="form-label">Predikat Kinerja</label>
                <select class="form-select" name="predikat_kinerja" required>
                    @foreach($predikats as $item)<option @selected($val('predikat_kinerja', 'Baik') === $item)>{{ $item }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Angka Kredit Lama</label><input class="form-control" type="number" step="0.0001" min="0" name="angka_kredit_lama" value="{{ $val('angka_kredit_lama') }}"></div>
            <div class="col-md-4"><label class="form-label">Angka Kredit Dasar</label><input class="form-control" type="number" step="0.0001" min="0" name="angka_kredit_dasar" id="ak-dasar" value="{{ $val('angka_kredit_dasar', 0) }}"></div>
            <div class="col-md-4"><label class="form-label">AK Penyesuaian</label><input class="form-control" type="number" step="0.0001" min="0" name="angka_kredit_penyesuaian" value="{{ $val('angka_kredit_penyesuaian', 0) }}"></div>
            <div class="col-md-4"><label class="form-label">Koefisien AK Tahunan</label><input class="form-control" type="number" step="0.0001" min="0" name="koefisien_ak_tahunan" id="koefisien" value="{{ $val('koefisien_ak_tahunan') }}"></div>
            <div class="col-md-4"><label class="form-label">Kebutuhan AK Kenaikan Pangkat</label><input class="form-control" type="number" step="0.0001" min="0" name="kebutuhan_ak_kenaikan_pangkat" id="kebutuhan-pangkat" value="{{ $val('kebutuhan_ak_kenaikan_pangkat') }}"></div>
            <div class="col-md-4"><label class="form-label">Kebutuhan AK Kenaikan Jenjang</label><input class="form-control" type="number" step="0.0001" min="0" name="kebutuhan_ak_kenaikan_jenjang" id="kebutuhan-jenjang" value="{{ $val('kebutuhan_ak_kenaikan_jenjang') }}"></div>
            <div class="col-md-4"><label class="form-label">Pendidikan Baru</label><input class="form-control" name="pendidikan_baru" value="{{ $val('pendidikan_baru') }}"></div>
            <div class="col-md-4"><label class="form-label">Kebutuhan AK Pangkat Terkait Ijazah</label><input class="form-control" type="number" step="0.0001" min="0" name="kebutuhan_ak_pangkat_terkait" id="kebutuhan-ijazah" value="{{ $val('kebutuhan_ak_pangkat_terkait') }}"></div>
            <div class="col-md-4 d-flex align-items-end">
                <label class="form-check mb-2"><input class="form-check-input me-2" type="checkbox" name="pangkat_puncak" value="1" @checked($val('pangkat_puncak'))>Pangkat puncak/tertinggi JA</label>
            </div>
            <div class="col-12"><label class="form-label">Catatan</label><textarea class="form-control" name="catatan" rows="2">{{ $val('catatan') }}</textarea></div>
        </div>
    </div>

    <div class="cat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
            <h2 class="h5 fw-bold text-primary-emphasis mb-0">Riwayat Predikat Kinerja</h2>
            <button class="btn btn-sm btn-outline-primary" type="button" onclick="addRiwayatRow()"><i class="fa-solid fa-plus me-2"></i>Tambah Baris</button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle" id="riwayat-table">
                <thead><tr><th>Tahun</th><th>Jumlah Bulan</th><th>Predikat</th><th>Koefisien</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach(($data['riwayat_predikat'] ?? [['tahun' => now()->year, 'jumlah_bulan' => 12, 'predikat_kinerja' => 'Baik', 'koefisien_ak_tahunan' => $val('koefisien_ak_tahunan', 12.5)]]) as $i => $row)
                    <tr>
                        <td><input class="form-control" type="number" name="riwayat_predikat[{{ $i }}][tahun]" value="{{ $row['tahun'] ?? now()->year }}"></td>
                        <td><input class="form-control" type="number" min="1" max="12" name="riwayat_predikat[{{ $i }}][jumlah_bulan]" value="{{ $row['jumlah_bulan'] ?? 12 }}"></td>
                        <td><select class="form-select" name="riwayat_predikat[{{ $i }}][predikat_kinerja]">@foreach($predikats as $item)<option @selected(($row['predikat_kinerja'] ?? 'Baik') === $item)>{{ $item }}</option>@endforeach</select></td>
                        <td><input class="form-control" type="number" step="0.0001" min="0" name="riwayat_predikat[{{ $i }}][koefisien_ak_tahunan]" value="{{ $row['koefisien_ak_tahunan'] ?? $val('koefisien_ak_tahunan', 12.5) }}"></td>
                        <td><button class="btn btn-sm btn-outline-danger" type="button" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-navy px-4 py-3" type="submit"><i class="fa-solid fa-calculator me-2"></i>Hitung Simulasi</button>
        <button class="btn btn-success px-4 py-3" type="submit" formaction="{{ route('angka-kredit.store') }}"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Hasil Simulasi</button>
        <a class="btn btn-outline-secondary px-4 py-3" href="{{ route('angka-kredit.index') }}"><i class="fa-solid fa-rotate-left me-2"></i>Reset</a>
        <a class="btn btn-outline-dark px-4 py-3" href="{{ route('dashboard') }}"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>
</form>

@if($result)
    @include('simulasi_angka_kredit.partials.result', ['result' => $result])
@endif
@endsection

@push('scripts')
<script>
const predikats = @json($predikats);
const masters = @json($masterOptions);
let rowIndex = document.querySelectorAll('#riwayat-table tbody tr').length;

function addRiwayatRow() {
    const options = predikats.map(item => `<option>${item}</option>`).join('');
    document.querySelector('#riwayat-table tbody').insertAdjacentHTML('beforeend', `
        <tr>
            <td><input class="form-control" type="number" name="riwayat_predikat[${rowIndex}][tahun]" value="${new Date().getFullYear()}"></td>
            <td><input class="form-control" type="number" min="1" max="12" name="riwayat_predikat[${rowIndex}][jumlah_bulan]" value="12"></td>
            <td><select class="form-select" name="riwayat_predikat[${rowIndex}][predikat_kinerja]">${options}</select></td>
            <td><input class="form-control" type="number" step="0.0001" min="0" name="riwayat_predikat[${rowIndex}][koefisien_ak_tahunan]" value="${document.getElementById('koefisien').value || 12.5}"></td>
            <td><button class="btn btn-sm btn-outline-danger" type="button" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
        </tr>
    `);
    rowIndex++;
}

function setValueIfNeeded(id, value, force = false) {
    const input = document.getElementById(id);
    if (!input || value === null || value === undefined) return;
    if (force || input.value === '') {
        input.value = value;
    }
}

function syncRiwayatKoefisien(force = false) {
    const koefisien = document.getElementById('koefisien').value;
    if (!koefisien) return;

    document.querySelectorAll('input[name$="[koefisien_ak_tahunan]"]').forEach(input => {
        if (force || input.value === '') {
            input.value = koefisien;
        }
    });
}

function fillMaster(force = false) {
    const kategori = document.getElementById('kategori-jf').value;
    const jenjang = document.getElementById('jenjang-jf').value;
    const master = masters.find(item => item.kategori === kategori && item.jenjang === jenjang);
    if (!master) return;
    setValueIfNeeded('koefisien', master.koefisien, force);
    setValueIfNeeded('ak-dasar', master.ak_dasar || 0, force);
    setValueIfNeeded('kebutuhan-pangkat', master.pangkat, force);
    setValueIfNeeded('kebutuhan-jenjang', master.jenjang_ak, force);
    setValueIfNeeded('kebutuhan-ijazah', master.pangkat, false);
    syncRiwayatKoefisien(force);
}

function updateMasaPangkat() {
    const start = document.getElementById('tmt-pangkat').value;
    const end = document.getElementById('tanggal-simulasi').value;
    if (!start || !end) return;
    const a = parseLocalDate(start);
    const b = parseLocalDate(end);
    const info = document.getElementById('masa-pangkat-info');
    if (!a || !b || b < a) {
        document.getElementById('masa-tahun').value = 0;
        document.getElementById('masa-bulan').value = 0;
        info.textContent = '';
        return;
    }

    let years = b.getFullYear() - a.getFullYear();
    let months = b.getMonth() - a.getMonth();
    let days = b.getDate() - a.getDate();

    if (days < 0) {
        months--;
        days += new Date(b.getFullYear(), b.getMonth(), 0).getDate();
    }
    if (months < 0) {
        years--;
        months += 12;
    }

    years = Math.max(0, years);
    months = Math.max(0, months);
    document.getElementById('masa-tahun').value = years;
    document.getElementById('masa-bulan').value = months;
    info.textContent = `Masa pangkat per tanggal simulasi: ${years} tahun ${months} bulan ${days} hari.`;
}

function parseLocalDate(value) {
    const parts = value.split('-').map(Number);
    if (parts.length !== 3 || parts.some(Number.isNaN)) return null;
    return new Date(parts[0], parts[1] - 1, parts[2]);
}

document.getElementById('kategori-jf').addEventListener('change', () => fillMaster(true));
document.getElementById('jenjang-jf').addEventListener('change', () => fillMaster(true));
document.getElementById('koefisien').addEventListener('input', () => syncRiwayatKoefisien(false));
document.getElementById('tmt-pangkat').addEventListener('change', updateMasaPangkat);
document.getElementById('tanggal-simulasi').addEventListener('change', updateMasaPangkat);
fillMaster(false);
updateMasaPangkat();
</script>
@endpush

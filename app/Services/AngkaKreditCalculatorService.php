<?php

namespace App\Services;

use App\Models\MasterPredikatKinerja;
use Carbon\Carbon;

class AngkaKreditCalculatorService
{
    public function calculate(array $data): array
    {
        return match ($data['jenis_simulasi'] ?? '') {
            'Perpindahan dari Jabatan Lain ke JF' => $this->calculatePerpindahanDariJabatanLain($data),
            'Perpindahan Antar Jabatan Fungsional' => $this->calculatePerpindahanAntarJF($data),
            'Penyesuaian / Penyetaraan' => $this->calculatePenyesuaian($data),
            'Promosi ke dalam JF' => $this->calculatePromosi($data),
            'Kenaikan Jenjang Jabatan Fungsional' => $this->calculateKenaikanJenjang($data),
            'Kenaikan Pangkat Jabatan Fungsional' => $this->calculateKenaikanPangkat($data),
            'Pengangkatan Kembali' => $this->calculatePengangkatanKembali($data),
            'Tambahan Angka Kredit karena Ijazah' => $this->calculateTambahanIjazah($data),
            default => $this->baseResult($data, 0, 0, 0, 0, ['Jenis simulasi belum dipilih.']),
        };
    }

    public function calculateKonversi(array $riwayat): float
    {
        $total = 0.0;
        foreach ($riwayat as $row) {
            if (!is_array($row)) {
                continue;
            }

            $bulan = max(0, (int) ($row['jumlah_bulan'] ?? 0));
            $koefisien = $this->number($row['koefisien_ak_tahunan'] ?? 0);
            $persentase = $this->getPredikatPercentage((string) ($row['predikat_kinerja'] ?? '')) / 100;
            $total += ($bulan / 12) * $persentase * $koefisien;
        }

        return round($total, 4);
    }

    public function calculatePerpindahanDariJabatanLain(array $data): array
    {
        $riwayat = $this->riwayatOrSinglePeriod($data);
        if (!empty($data['pangkat_puncak']) && $this->totalMonths($riwayat) > 36) {
            $riwayat = $this->limitRiwayatMonths($riwayat, 36);
        }

        $konversi = $this->calculateKonversi($riwayat);
        $dasar = $this->number($data['angka_kredit_dasar'] ?? 0);

        return $this->baseResult($data, 0, $konversi, $dasar, 0, [
            'Simulasi perpindahan dari jabatan lain menggunakan AK konversi predikat kinerja dan AK dasar.',
            !empty($data['pangkat_puncak']) ? 'Untuk kondisi pangkat puncak/tertinggi, masa yang dihitung dibatasi maksimal 3 tahun terakhir.' : null,
        ]);
    }

    public function calculatePerpindahanAntarJF(array $data): array
    {
        $lama = $this->number($data['angka_kredit_lama'] ?? 0);

        return $this->baseResult($data, $lama, 0, 0, 0, [
            'Simulasi menggunakan Angka Kredit dari Jabatan Fungsional sebelumnya.',
        ]);
    }

    public function calculatePenyesuaian(array $data): array
    {
        $penyesuaian = $this->number($data['angka_kredit_penyesuaian'] ?? 0);
        $dasar = $this->number($data['angka_kredit_dasar'] ?? 0);
        $masa = $this->calculateMasaPangkat($data['tmt_pangkat_terakhir'] ?? null, $data['tanggal_simulasi'] ?? null);

        return $this->baseResult($data, $penyesuaian, 0, $dasar, 0, [
            'Masa pangkat penyesuaian dibulatkan menjadi '.$this->roundedMasaPangkatText($masa['total_bulan']).'.',
        ]);
    }

    public function calculatePromosi(array $data): array
    {
        $konversi = $this->calculateKonversi($this->riwayatOrSinglePeriod($data));
        $dasar = $this->number($data['angka_kredit_dasar'] ?? 0);

        return $this->baseResult($data, 0, $konversi, $dasar, 0, [
            'Promosi ke dalam JF menghitung AK konversi predikat kinerja ditambah AK dasar.',
        ]);
    }

    public function calculateKenaikanJenjang(array $data): array
    {
        $lama = $this->number($data['angka_kredit_lama'] ?? 0);
        $konversi = $this->calculateKonversi($this->riwayatOrSinglePeriod($data));

        return $this->baseResult($data, $lama, $konversi, 0, 0, [
            'Kelebihan AK kenaikan jenjang tidak otomatis diperhitungkan untuk kenaikan jenjang berikutnya.',
        ]);
    }

    public function calculateKenaikanPangkat(array $data): array
    {
        $lama = $this->number($data['angka_kredit_lama'] ?? 0);
        $konversi = $this->calculateKonversi($this->riwayatOrSinglePeriod($data));
        $tambahanIjazah = $this->tambahanIjazah($data);
        $masa = $this->calculateMasaPangkat($data['tmt_pangkat_terakhir'] ?? null, $data['tanggal_simulasi'] ?? null);

        return $this->baseResult($data, $lama, $konversi, 0, $tambahanIjazah, [
            $masa['total_bulan'] >= 24
                ? 'Catatan masa pangkat: memenuhi simulasi minimal 2 tahun.'
                : 'Catatan masa pangkat: belum mencapai 2 tahun, perhitungan AK tetap ditampilkan sebagai estimasi.',
        ]);
    }

    public function calculatePengangkatanKembali(array $data): array
    {
        $riwayat = $this->riwayatOrSinglePeriod($data);
        if ($this->totalMonths($riwayat) > 48) {
            $riwayat = $this->limitRiwayatMonths($riwayat, 48);
        }

        $lama = $this->number($data['angka_kredit_lama'] ?? 0);
        $konversi = $this->calculateKonversi($riwayat);
        $dasar = $this->number($data['angka_kredit_dasar'] ?? 0);

        return $this->baseResult($data, $lama, $konversi, $dasar, 0, [
            'Pengangkatan kembali membatasi masa konversi maksimal 4 tahun.',
        ]);
    }

    public function calculateTambahanIjazah(array $data): array
    {
        $lama = $this->number($data['angka_kredit_lama'] ?? 0);
        $tambahanIjazah = $this->tambahanIjazah($data);
        $notes = ['Tambahan AK ijazah dihitung dari 25% kebutuhan AK pangkat terkait jika predikat minimal Baik.'];

        if ($tambahanIjazah <= 0 && !$this->predikatMinimalBaik((string) ($data['predikat_kinerja'] ?? ''))) {
            $notes[] = 'Tambahan AK ijazah tidak diberikan karena predikat kinerja di bawah Baik.';
        }

        return $this->baseResult($data, $lama, 0, 0, $tambahanIjazah, $notes);
    }

    public function getPredikatPercentage(string $predikat): float
    {
        $record = MasterPredikatKinerja::query()
            ->where('nama_predikat', $predikat)
            ->where('is_active', true)
            ->first();

        if ($record) {
            return (float) $record->persentase;
        }

        return (float) config("angka_kredit.predikat.{$predikat}", 0);
    }

    public function calculateMasaPangkat($tmt, $tanggalSimulasi): array
    {
        if (!$tmt || !$tanggalSimulasi) {
            return ['tahun' => 0, 'bulan' => 0, 'total_bulan' => 0];
        }

        $start = Carbon::parse($tmt)->startOfDay();
        $end = Carbon::parse($tanggalSimulasi)->startOfDay();
        if ($end->lessThan($start)) {
            return ['tahun' => 0, 'bulan' => 0, 'total_bulan' => 0];
        }

        $total = (int) floor($start->diffInMonths($end));

        return [
            'tahun' => intdiv($total, 12),
            'bulan' => $total % 12,
            'total_bulan' => $total,
        ];
    }

    public function estimateTimeToFulfill($kekuranganAk, $koefisien): array
    {
        $kekuranganAk = max(0, $this->number($kekuranganAk));
        $koefisien = $this->number($koefisien);

        return [
            'baik' => $koefisien > 0 ? round($kekuranganAk / $koefisien, 2) : null,
            'sangat_baik' => $koefisien > 0 ? round($kekuranganAk / (1.5 * $koefisien), 2) : null,
        ];
    }

    private function baseResult(array $data, float $lama, float $konversi, float $dasar, float $tambahanIjazah, array $notes): array
    {
        $total = round($lama + $konversi + $dasar + $tambahanIjazah, 4);
        $kebutuhanKp = $this->nullableNumber($data['kebutuhan_ak_kenaikan_pangkat'] ?? null);
        $kebutuhanKj = $this->nullableNumber($data['kebutuhan_ak_kenaikan_jenjang'] ?? null);
        $selisihKp = $kebutuhanKp !== null ? round($total - $kebutuhanKp, 4) : null;
        $selisihKj = $kebutuhanKj !== null ? round($total - $kebutuhanKj, 4) : null;
        $koefisien = $this->number($data['koefisien_ak_tahunan'] ?? 0);

        return [
            'identitas' => [
                'nama' => $data['nama_lengkap'] ?? null,
                'nip' => $data['nip'] ?? null,
                'jenis_simulasi' => $data['jenis_simulasi'] ?? null,
                'jabatan_asal' => $data['jabatan_asal'] ?? null,
                'jabatan_tujuan' => $data['jabatan_tujuan'] ?? null,
                'golongan_ruang' => $data['golongan_ruang'] ?? null,
                'jenjang_tujuan' => $data['jenjang_jf_tujuan'] ?? null,
                'tanggal_simulasi' => $data['tanggal_simulasi'] ?? null,
            ],
            'angka_kredit_lama' => round($lama, 4),
            'angka_kredit_konversi' => round($konversi, 4),
            'angka_kredit_dasar' => round($dasar, 4),
            'tambahan_ak_ijazah' => round($tambahanIjazah, 4),
            'total_ak' => $total,
            'kebutuhan_ak_kenaikan_pangkat' => $kebutuhanKp,
            'kebutuhan_ak_kenaikan_jenjang' => $kebutuhanKj,
            'selisih_kp' => $selisihKp,
            'selisih_kj' => $selisihKj,
            'status_kp' => $this->statusText($selisihKp, 'kenaikan pangkat'),
            'status_kj' => $this->statusText($selisihKj, 'kenaikan jenjang'),
            'estimasi_kp' => $this->estimateTimeToFulfill($selisihKp !== null ? abs(min(0, $selisihKp)) : 0, $koefisien),
            'estimasi_kj' => $this->estimateTimeToFulfill($selisihKj !== null ? abs(min(0, $selisihKj)) : 0, $koefisien),
            'catatan' => implode(' ', array_values(array_filter($notes))),
            'disclaimer' => 'Hasil ini merupakan simulasi penghitungan Angka Kredit dan tidak menggantikan Penetapan Angka Kredit resmi. Verifikasi akhir tetap mengikuti ketentuan peraturan perundang-undangan dan pejabat berwenang.',
        ];
    }

    private function statusText(?float $selisih, string $target): ?string
    {
        if ($selisih === null) {
            return null;
        }

        return $selisih >= 0
            ? 'Memenuhi kebutuhan AK '.$target
            : 'Belum memenuhi kebutuhan AK '.$target;
    }

    private function riwayatOrSinglePeriod(array $data): array
    {
        $riwayat = array_values(array_filter($data['riwayat_predikat'] ?? [], fn ($row) => is_array($row) && ($row['jumlah_bulan'] ?? null)));
        if ($riwayat) {
            return $riwayat;
        }

        $tahun = (int) ($data['masa_pangkat_tahun'] ?? 0);
        $bulan = (int) ($data['masa_pangkat_bulan'] ?? 0);
        $totalBulan = max(0, ($tahun * 12) + $bulan);

        return [[
            'tahun' => isset($data['tanggal_simulasi']) ? Carbon::parse($data['tanggal_simulasi'])->year : now()->year,
            'jumlah_bulan' => $totalBulan,
            'predikat_kinerja' => $data['predikat_kinerja'] ?? 'Baik',
            'koefisien_ak_tahunan' => $data['koefisien_ak_tahunan'] ?? 0,
        ]];
    }

    private function limitRiwayatMonths(array $riwayat, int $maxMonths): array
    {
        $remaining = $maxMonths;
        $limited = [];

        foreach (array_reverse($riwayat) as $row) {
            if ($remaining <= 0) {
                break;
            }

            $months = min((int) ($row['jumlah_bulan'] ?? 0), $remaining);
            $row['jumlah_bulan'] = $months;
            $limited[] = $row;
            $remaining -= $months;
        }

        return array_reverse($limited);
    }

    private function totalMonths(array $riwayat): int
    {
        return array_sum(array_map(fn ($row) => (int) ($row['jumlah_bulan'] ?? 0), $riwayat));
    }

    private function tambahanIjazah(array $data): float
    {
        if (!$this->predikatMinimalBaik((string) ($data['predikat_kinerja'] ?? ''))) {
            return 0;
        }

        return round(0.25 * $this->number($data['kebutuhan_ak_pangkat_terkait'] ?? 0), 4);
    }

    private function predikatMinimalBaik(string $predikat): bool
    {
        return $this->getPredikatPercentage($predikat) >= 100;
    }

    private function roundedMasaPangkatText(int $totalBulan): string
    {
        return match (true) {
            $totalBulan < 12 => 'kurang dari 1 tahun',
            $totalBulan < 24 => '1 tahun',
            $totalBulan < 36 => '2 tahun',
            $totalBulan < 48 => '3 tahun',
            default => '4 tahun',
        };
    }

    private function nullableNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->number($value);
    }

    private function number($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}

<?php

namespace App\Services;

use App\Models\Regulation;
use Illuminate\Support\Str;

class RegulationSummaryService
{
    public function summarize(Regulation $regulation): Regulation
    {
        $text = trim($regulation->extracted_text ?: $regulation->description ?: '');
        $lower = mb_strtolower($regulation->title.' '.$regulation->category.' '.$text);
        $isDivorce = str_contains($lower, 'perceraian') || str_contains($lower, 'perkawinan');

        $summary = $isDivorce
            ? "Pokok pengaturan: izin perkawinan, izin perceraian, dan surat keterangan bagi PNS/ASN.\nSubjek yang diatur: PNS/ASN, pejabat berwenang, atasan, dan unit kepegawaian.\nKewajiban: memperoleh izin atau surat keterangan sebelum proses perceraian.\nProsedur: pengajuan permohonan, pemeriksaan alasan, pemanggilan pihak terkait, pertimbangan pejabat, dan pencatatan administrasi.\nPejabat berwenang: pejabat pembina/pejabat yang ditentukan sesuai ketentuan kepegawaian.\nSanksi/konsekuensi: pelanggaran prosedur dapat menimbulkan konsekuensi disiplin.\nMateri potensial untuk soal: prosedur izin cerai, pemeriksaan pejabat, dokumen pendukung, dan studi kasus disiplin."
            : "Pokok pengaturan: ".Str::limit($text ?: $regulation->title, 500, '')."\nSubjek yang diatur: ASN, instansi pemerintah, dan pejabat pengelola kepegawaian.\nKewajiban: mengikuti prosedur dan prinsip sistem merit sesuai regulasi.\nLarangan: penyimpangan dari ketentuan, konflik kepentingan, dan keputusan tanpa dasar aturan.\nProsedur: identifikasi kebutuhan, verifikasi dokumen, penilaian objektif, dan pencatatan keputusan.\nPejabat berwenang: pejabat sesuai kewenangan dalam regulasi.\nMateri potensial untuk soal: pemahaman konsep, prosedur administratif, penerapan aturan, dan studi kasus manajerial.";

        $regulation->update([
            'summary' => $summary,
            'keywords' => $this->keywords($regulation, $lower),
        ]);

        return $regulation->fresh();
    }

    private function keywords(Regulation $regulation, string $lower): array
    {
        $keywords = [];
        foreach (['angka kredit', 'kenaikan pangkat', 'jabatan fungsional', 'pensiun', 'pemberhentian', 'pengadaan', 'cuti', 'mutasi', 'promosi', 'manajemen talenta', 'perkawinan', 'perceraian', 'disiplin'] as $keyword) {
            if (str_contains($lower, $keyword)) {
                $keywords[] = $keyword;
            }
        }

        return $keywords ?: array_values(array_filter([$regulation->category, $regulation->regulation_number]));
    }
}

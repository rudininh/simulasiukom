<?php

namespace App\Services;

use App\Models\Regulation;
use App\Models\RegulationPage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Smalot\PdfParser\Parser;
use Symfony\Component\Process\Process;

class RegulationTextExtractorService
{
    public function extract(Regulation $regulation): Regulation
    {
        try {
            if (!$regulation->file_path || !Storage::disk('public')->exists($regulation->file_path)) {
                return $this->storeResult($regulation, $regulation->description ?? '', 'manual');
            }

            $path = Storage::disk('public')->path($regulation->file_path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                return $this->storePdfResult($regulation, $this->extractPdfPages($path), 'pdf_text');
            }

            $text = match ($extension) {
                'txt' => file_get_contents($path),
                'docx' => $this->extractDocx($path),
                default => throw new RuntimeException('Format file tidak didukung.'),
            };

            return $this->storeResult($regulation, $text, $extension);
        } catch (\Throwable $e) {
            $regulation->update([
                'extraction_status' => 'failed',
                'extraction_error' => $this->clean($e->getMessage()),
                'extracted_at' => now(),
            ]);

            throw $e;
        }
    }

    public function summarize(Regulation $regulation): Regulation
    {
        $text = $this->clean($regulation->extracted_text ?: $regulation->description ?: '');
        $lower = mb_strtolower($text);
        $isDivorce = str_contains($lower, 'perceraian') || str_contains($lower, 'perkawinan');

        $summary = [
            'Pokok pengaturan: '.($isDivorce ? 'izin perkawinan dan perceraian PNS/ASN, termasuk prosedur dan kewenangan pejabat.' : 'manajemen ASN, kewajiban, larangan, dan prosedur administrasi.'),
            'Subjek yang diatur: PNS/ASN, pejabat berwenang, atasan langsung, dan unit pengelola kepegawaian.',
            'Kewajiban: mengikuti prosedur administratif, menyampaikan dokumen, dan memperoleh izin atau surat keterangan jika dipersyaratkan.',
            'Larangan: melakukan tindakan yang bertentangan dengan ketentuan disiplin, integritas, dan prosedur kepegawaian.',
            'Prosedur: pengajuan permohonan, pemeriksaan alasan, verifikasi dokumen, pertimbangan atasan/pejabat, dan penetapan keputusan.',
            'Pejabat berwenang: pejabat pembina kepegawaian atau pejabat yang menerima delegasi sesuai ketentuan.',
            'Sanksi/konsekuensi: pelanggaran prosedur dapat berdampak pada disiplin PNS/ASN.',
            'Materi potensial untuk soal: pemahaman aturan, prosedur administratif, kewenangan pejabat, sanksi, dan studi kasus pengambilan keputusan.',
        ];

        $keywords = $isDivorce
            ? ['izin perkawinan', 'izin perceraian', 'surat keterangan', 'pejabat berwenang', 'pemeriksaan alasan', 'hukuman disiplin']
            : ['sistem merit', 'manajemen ASN', 'integritas', 'akuntabilitas', 'pelayanan publik'];

        $regulation->update([
            'summary' => implode("\n", $summary),
            'keywords' => $keywords,
        ]);

        return $regulation->refresh();
    }

    private function extractDocx(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('DOCX tidak dapat dibuka.');
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        return strip_tags(str_replace(['</w:p>', '</w:tr>'], "\n", $xml));
    }

    private function extractPdf(string $path): string
    {
        return implode("\f", $this->extractPdfPages($path));
    }

    private function extractPdfPages(string $path): array
    {
        $binary = config('ocr.pdftotext_binary', 'pdftotext');
        $output = tempnam(sys_get_temp_dir(), 'pdf-text-');
        $process = new Process([$binary, '-layout', $path, $output]);
        $process->setTimeout(60);
        $process->run();

        if ($process->isSuccessful() && is_file($output)) {
            $text = file_get_contents($output) ?: '';
            @unlink($output);
            if (mb_strlen($this->clean($text)) >= 500) {
                return $this->splitPages($text);
            }
        }

        @unlink($output);

        return $this->extractPdfPagesWithPhpParser($path);
    }

    private function extractPdfWithPhpParser(string $path): string
    {
        return implode("\f", $this->extractPdfPagesWithPhpParser($path));
    }

    private function extractPdfPagesWithPhpParser(string $path): array
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);

            $pages = [];
            foreach ($pdf->getPages() as $page) {
                $pages[] = $page->getText();
            }

            return $pages ?: [$pdf->getText()];
        } catch (\Throwable) {
            return [''];
        }
    }

    private function storePdfResult(Regulation $regulation, array $pages, string $method): Regulation
    {
        $cleanPages = array_map(fn ($pageText) => $this->clean($pageText), $pages);
        $clean = trim(implode("\n\n", array_filter($cleanPages, fn ($pageText) => trim($pageText) !== '')));
        $tooShort = mb_strlen($clean) < 500;

        RegulationPage::where('regulation_id', $regulation->id)->delete();
        foreach ($cleanPages as $index => $pageText) {
            RegulationPage::create([
                'regulation_id' => $regulation->id,
                'page_number' => $index + 1,
                'text' => $pageText,
                'extraction_method' => $method,
                'status' => $tooShort ? 'pending' : 'extracted',
            ]);
        }

        $regulation->update([
            'extracted_text' => $clean,
            'extraction_status' => $tooShort ? 'need_ocr' : 'extracted',
            'extraction_method' => $method,
            'extraction_error' => $tooShort ? 'PDF tidak memiliki teks yang cukup. Silakan jalankan OCR.' : null,
            'extracted_at' => now(),
            'page_count' => max(1, count($cleanPages)),
        ]);

        return $regulation->refresh();
    }

    private function storeResult(Regulation $regulation, string $text, string $method): Regulation
    {
        $clean = $this->clean($text);
        $tooShort = mb_strlen($clean) < 500 && $regulation->isPdf();
        $pages = $this->splitPages($clean);

        RegulationPage::where('regulation_id', $regulation->id)->delete();
        foreach ($pages as $index => $pageText) {
            RegulationPage::create([
                'regulation_id' => $regulation->id,
                'page_number' => $index + 1,
                'text' => $pageText,
                'extraction_method' => $method,
                'status' => $tooShort ? 'pending' : 'extracted',
            ]);
        }

        $regulation->update([
            'extracted_text' => $clean,
            'extraction_status' => $tooShort ? 'need_ocr' : 'extracted',
            'extraction_method' => $method,
            'extraction_error' => $tooShort ? 'PDF tidak memiliki teks yang cukup. Silakan jalankan OCR.' : null,
            'extracted_at' => now(),
            'page_count' => max(1, count($pages)),
        ]);

        return $regulation->refresh();
    }

    private function splitPages(string $text): array
    {
        if (trim($text) === '') {
            return [''];
        }

        $chunks = preg_split('/\f+/', $text) ?: [];
        if (count($chunks) <= 1) {
            $chunks = str_split($text, 3500);
        }

        return array_map(fn ($chunk) => trim($chunk), array_values(array_filter($chunks, fn ($chunk) => trim($chunk) !== '')));
    }

    private function clean(?string $text): string
    {
        $text = (string) $text;
        $text = @iconv('UTF-8', 'UTF-8//IGNORE', $text) ?: $text;
        $text = preg_replace('/[\x{D800}-\x{DFFF}]/u', '', $text) ?: $text;
        $text = preg_replace('/[^\P{C}\n\r\t]+/u', ' ', $text) ?: $text;

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?: ''), 250000, '');
    }
}

<?php

namespace App\Services;

use App\Models\Regulation;
use App\Models\RegulationPage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class RegulationOcrService
{
    public function process(Regulation $regulation): Regulation
    {
        if (!config('ocr.enabled')) {
            throw new RuntimeException('OCR sedang dinonaktifkan.');
        }

        if (!$regulation->isPdf()) {
            throw new RuntimeException('OCR hanya dapat dijalankan untuk file PDF.');
        }

        if (!$regulation->file_path || !Storage::disk('public')->exists($regulation->file_path)) {
            throw new RuntimeException('File PDF tidak ditemukan.');
        }

        $regulation->update(['extraction_status' => 'ocr_processing', 'extraction_error' => null]);

        try {
            $pdf = Storage::disk('public')->path($regulation->file_path);
            $workDir = rtrim(config('ocr.temp_path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'regulation-'.$regulation->id.'-'.time();
            File::ensureDirectoryExists($workDir);

            $prefix = $workDir.DIRECTORY_SEPARATOR.'page';
            $convert = new Process([
                config('ocr.pdf_to_image_binary'),
                '-r',
                (string) config('ocr.dpi'),
                '-png',
                '-f',
                '1',
                '-l',
                (string) config('ocr.max_pages'),
                $pdf,
                $prefix,
            ]);
            $convert->setTimeout(300);
            $convert->run();

            if (!$convert->isSuccessful()) {
                throw new RuntimeException('OCR gagal dijalankan. Pastikan Tesseract dan pdftoppm sudah terpasang.');
            }

            $images = collect(File::files($workDir))->filter(fn ($file) => str_ends_with(strtolower($file->getFilename()), '.png'))->sortBy(fn ($file) => $file->getFilename())->values();
            if ($images->isEmpty()) {
                throw new RuntimeException('PDF tidak dapat dikonversi menjadi gambar untuk OCR.');
            }

            RegulationPage::where('regulation_id', $regulation->id)->delete();
            $allText = [];
            $confidences = [];

            foreach ($images as $index => $image) {
                $outputBase = $workDir.DIRECTORY_SEPARATOR.'ocr-'.$index;
                $ocr = new Process([
                    config('ocr.tesseract_binary'),
                    $image->getPathname(),
                    $outputBase,
                    '-l',
                    config('ocr.language'),
                ]);
                $ocr->setTimeout(180);
                $ocr->run();

                if (!$ocr->isSuccessful() && config('ocr.language') !== 'eng') {
                    $ocr = new Process([config('ocr.tesseract_binary'), $image->getPathname(), $outputBase, '-l', 'eng']);
                    $ocr->setTimeout(180);
                    $ocr->run();
                }

                $textFile = $outputBase.'.txt';
                $ocrText = is_file($textFile) ? trim(file_get_contents($textFile) ?: '') : '';
                $relativeImage = 'regulation-ocr/'.$regulation->id.'/'.$image->getFilename();
                Storage::disk('public')->put($relativeImage, file_get_contents($image->getPathname()));

                RegulationPage::create([
                    'regulation_id' => $regulation->id,
                    'page_number' => $index + 1,
                    'ocr_text' => $ocrText,
                    'extraction_method' => 'ocr',
                    'confidence' => null,
                    'image_path' => $relativeImage,
                    'status' => $ocr->isSuccessful() ? 'ocr_completed' : 'failed',
                    'error_message' => $ocr->isSuccessful() ? null : $ocr->getErrorOutput(),
                ]);

                $allText[] = $ocrText;
            }

            File::deleteDirectory($workDir);

            $text = trim(preg_replace('/\s+/', ' ', implode("\n\n", $allText)));
            if (mb_strlen($text) < 50) {
                throw new RuntimeException('OCR selesai tetapi teks terlalu pendek. Periksa kualitas scan atau bahasa OCR.');
            }

            $regulation->update([
                'extracted_text' => $text,
                'extraction_status' => 'ocr_completed',
                'extraction_method' => 'ocr',
                'extraction_error' => null,
                'extracted_at' => now(),
                'page_count' => $images->count(),
                'ocr_language' => config('ocr.language'),
                'ocr_confidence' => count($confidences) ? array_sum($confidences) / count($confidences) : null,
            ]);

            return $regulation->refresh();
        } catch (\Throwable $e) {
            $regulation->update([
                'extraction_status' => 'failed',
                'extraction_error' => $e->getMessage(),
                'extracted_at' => now(),
            ]);

            throw $e;
        }
    }
}

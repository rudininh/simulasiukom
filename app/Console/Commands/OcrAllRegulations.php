<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationOcrService;
use Illuminate\Console\Command;

class OcrAllRegulations extends Command
{
    protected $signature = 'regulation:ocr-all';

    protected $description = 'Run OCR for all PDF regulations that need OCR';

    public function handle(RegulationOcrService $ocr): int
    {
        $ok = 0;
        $failed = 0;

        Regulation::where('extraction_status', 'need_ocr')->orderBy('id')->each(function (Regulation $regulation) use ($ocr, &$ok, &$failed) {
            try {
                $ocr->process($regulation);
                $ok++;
                $this->line('OCR: '.$regulation->title);
            } catch (\Throwable $e) {
                $failed++;
                $this->error('OCR failed: '.$regulation->title.' - '.$e->getMessage());
            }
        });

        $this->info("OCR berhasil: {$ok}");
        $this->warn("OCR gagal: {$failed}");

        return self::SUCCESS;
    }
}

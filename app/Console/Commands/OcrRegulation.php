<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationOcrService;
use Illuminate\Console\Command;

class OcrRegulation extends Command
{
    protected $signature = 'regulation:ocr {regulation_id}';

    protected $description = 'Run OCR for scanned PDF regulation';

    public function handle(RegulationOcrService $service): int
    {
        $regulation = Regulation::findOrFail($this->argument('regulation_id'));
        $service->process($regulation);
        $this->info('Regulation OCR finished.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationSummaryService;
use Illuminate\Console\Command;

class SummarizeRegulation extends Command
{
    protected $signature = 'regulation:summarize {regulation_id}';

    protected $description = 'Generate ringkasan regulasi dari hasil extract/OCR.';

    public function handle(RegulationSummaryService $service): int
    {
        $regulation = Regulation::findOrFail($this->argument('regulation_id'));
        $service->summarize($regulation);
        $this->info('Regulation summary generated.');

        return self::SUCCESS;
    }
}

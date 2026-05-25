<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationTextExtractorService;
use Illuminate\Console\Command;

class ExtractRegulationText extends Command
{
    protected $signature = 'regulation:extract {regulation_id}';

    protected $description = 'Extract text from a regulation file';

    public function handle(RegulationTextExtractorService $service): int
    {
        $regulation = Regulation::findOrFail($this->argument('regulation_id'));
        $service->extract($regulation);
        $this->info('Regulation text extraction finished.');

        return self::SUCCESS;
    }
}

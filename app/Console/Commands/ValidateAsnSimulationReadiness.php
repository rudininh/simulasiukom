<?php

namespace App\Console\Commands;

use App\Services\AsnSimulationPreparationService;
use Illuminate\Console\Command;

class ValidateAsnSimulationReadiness extends Command
{
    protected $signature = 'asn:validate-simulation-readiness';

    protected $description = 'Validate that Manajemen ASN CAT simulations are ready for participants';

    public function handle(AsnSimulationPreparationService $service): int
    {
        $result = $service->validateReadiness();

        $this->line('Total regulasi: '.$result['regulations']);
        $this->line('PDF downloaded: '.$result['pdf_downloaded']);
        $this->line('PDF manual_required: '.$result['pdf_manual_required']);
        $this->line('Teks tersedia: '.$result['extracted']);
        foreach ($result['courses'] as $title => $count) {
            $this->line("{$title}: {$count} soal aktif");
        }

        if ($result['ready']) {
            $this->info('READY');
            return self::SUCCESS;
        }

        $this->error('NOT READY');
        foreach ($result['issues'] as $issue) {
            $this->warn($issue);
        }

        return self::FAILURE;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\AsnSimulationPreparationService;
use Illuminate\Console\Command;

class ResetManajemenAsnExam extends Command
{
    protected $signature = 'exam:reset-manajemen-asn';

    protected $description = 'Backup and deactivate old non-final Manajemen ASN simulations';

    public function handle(AsnSimulationPreparationService $service): int
    {
        $result = $service->resetOldSimulation();
        $this->info('Reset simulasi lama selesai.');
        $this->line('Course lama dinonaktifkan: '.$result['archived_exams']);
        $this->line('Soal lama dinonaktifkan: '.$result['archived_questions']);

        return self::SUCCESS;
    }
}

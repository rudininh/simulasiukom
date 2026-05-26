<?php

namespace App\Console\Commands;

use App\Services\AsnSimulationPreparationService;
use Illuminate\Console\Command;

class SeedExamQuestions extends Command
{
    protected $signature = 'exam:seed-questions {--mode=dummy : Question generation mode, dummy or ai}';

    protected $description = 'Seed active Manajemen ASN questions for the four final simulations';

    public function handle(AsnSimulationPreparationService $service): int
    {
        $courses = $service->seedCoursesAndQuestions();

        foreach ($courses as $title => $count) {
            $this->line("{$title}: {$count} soal aktif");
        }

        $this->info('Seed soal simulasi Manajemen ASN selesai.');

        return self::SUCCESS;
    }
}

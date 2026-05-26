<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\AsnSimulationPreparationService;
use App\Services\RegulationPdfDownloaderService;
use App\Services\RegulationTextExtractorService;
use Illuminate\Console\Command;

class PrepareFullAsnSimulation extends Command
{
    protected $signature = 'asn:prepare-full-simulation';

    protected $description = 'Prepare full Manajemen ASN CAT simulations, regulations, categories, and active questions';

    public function handle(
        AsnSimulationPreparationService $simulation,
        RegulationPdfDownloaderService $downloader,
        RegulationTextExtractorService $extractor
    ): int {
        $reset = $simulation->resetOldSimulation();
        $regulations = $simulation->seedRegulations();

        Regulation::whereNotNull('pdf_url')->where('pdf_url', '<>', '')->each(fn (Regulation $regulation) => $downloader->download($regulation));

        Regulation::whereNotNull('file_path')->each(function (Regulation $regulation) use ($extractor) {
            try {
                $extractor->extract($regulation);
            } catch (\Throwable) {
                // Extraction errors are stored on each regulation and should not stop preparation.
            }
        });

        $courses = $simulation->seedCoursesAndQuestions();
        $readiness = $simulation->validateReadiness();

        $this->info('Persiapan simulasi ASN selesai.');
        $this->line('Course lama dinonaktifkan: '.$reset['archived_exams']);
        $this->line('Regulasi default tersinkron: '.$regulations);
        $this->line('Total regulasi: '.$readiness['regulations']);
        $this->line('PDF berhasil diunduh: '.$readiness['pdf_downloaded']);
        $this->line('PDF manual_required: '.$readiness['pdf_manual_required']);
        $this->line('Teks berhasil tersedia: '.$readiness['extracted']);
        foreach ($courses as $title => $count) {
            $this->line("{$title}: {$count} soal aktif");
        }
        $this->line('Status readiness: '.($readiness['ready'] ? 'READY' : 'NOT READY'));

        foreach ($readiness['issues'] as $issue) {
            $this->warn($issue);
        }

        return $readiness['ready'] ? self::SUCCESS : self::FAILURE;
    }
}

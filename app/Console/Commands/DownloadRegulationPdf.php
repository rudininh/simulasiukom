<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationPdfDownloaderService;
use Illuminate\Console\Command;

class DownloadRegulationPdf extends Command
{
    protected $signature = 'regulation:download-pdf {regulation_id}';

    protected $description = 'Download one regulation PDF from official_url or pdf_url';

    public function handle(RegulationPdfDownloaderService $downloader): int
    {
        $regulation = Regulation::findOrFail($this->argument('regulation_id'));
        $result = $downloader->download($regulation);

        $this->line($result['message']);
        if ($result['pdf_url']) {
            $this->line('PDF URL: '.$result['pdf_url']);
        }
        if ($result['file_path']) {
            $this->line('File path: '.$result['file_path']);
        }

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
}
